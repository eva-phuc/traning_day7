<?php
namespace Aucfan;

class Payment
{

    protected static $_instance = null;
    private   static $_response = null;
    private   static $_error    = array();

    private   static $_url;
    private   static $_method = 'post';
    private   static $_params = array();

    private   static $_response_success = 'success';

    public static function forge()
    {
        if(true === empty(static::$_instance)) {
            static::$_instance = new Payment();
        }
        \Package::load('common');
        \Config::load('payment','payment',true);

        return static::$_instance;
    }

    /**
     * Prevent instantiation
     */
    final private function __construct() {}

    public static function instance()
    {
        if(is_null(static::$_instance)) static::forge();

        return static::$_instance;
    }

    public static function getError()
    {
        return static::$_error;
    }
    public static function getResponse()
    {
        return static::$_response;
    }


    /**
     * オークファンAPI経由でカード登録実行
     */
    public static function executeSaveCard($params,$userinfo)
    {
        $return = true;
        static::$_url = \Config::get('payment.api.url.savecard');

        static::$_params = array(
            'operation' => \Config::get('payment.api.params.savecard.operation'),
            'MemberID'  => intval($userinfo->user_id),
            'CardNo'    => $params['cardno'],
            'Expire'    => $params['cardyear'].$params['cardmonth'],
        );

        $return = static::_execute();
        if(false === $return) {
            //--- APIからのレスポンスをロギングしておく
            \Common\Error::instance()
                ->set_log('SaveCard error by API : '.implode("\t",(array)static::$_response))
                ->logging();
        }

        return $return;
    }

    /**
     * オークファンAPI経由でアラーモステータス変更実行
     */
    public static function executeChangeAlermoStatus($params,$userinfo,$chenge_to='on')
    {
        $return = true;
        static::$_url = \Config::get('payment.api.url.change_status');

        static::$_params = array(
            'user_id'        => intval($userinfo->user_id),
            'password_check' => \Config::get('payment.api.params.change_status.password_check'),
            'payment'        => \Config::get('payment.api.params.change_status.payment.card'),
            'alermo'         => \Config::get('payment.api.params.change_status.change_to.'.$chenge_to),
        );

        $return = static::_execute();
        if(false === $return) {
            //--- APIからのレスポンスをロギングしておく
            \Common\Error::instance()
                ->set_log('ChangeAlermoStatus error by API : '.implode("\t",(array)static::$_response))
                ->logging();
        }

        return $return;
    }

    /**
     * オークファンAPI経由でカード情報取得
     */
    public static function getCardNumber($userinfo)
    {
        $return = true;
        static::$_url = \Config::get('payment.api.url.getcard');

        static::$_params = array(
            'MemberID'  => intval($userinfo->user_id),
        );

        $return = static::_execute();
        if(false === $return) {
            //--- APIからのレスポンスをロギングしておく
            \Common\Error::instance()
                ->set_log('GetCard error by API : '.implode("\t",(array)static::$_response))
                ->logging();
        }

        return $return;
    }


    /**
     * カード登録フォーム用バリデーションメソッド
     */
    public static function validateCreditInfo($posted,$key='subscribe')
    {
        $validate      = \Validation::forge('credit');
        $validate_conf = \Config::get('payment.validate.'.$key, array());
        if(empty($validate_conf['check_keys'])) return true;

        $validate->add_callable('\\Common\\CreditCardValidate');
        foreach(array_unique($validate_conf['check_keys']) as $validate_key) {

            $validation_rules = array();

            //--- 必須
            if(isset($validate_conf['required'][$validate_key])) {
                $validation_rules[] = 'required';
            }
            //--- 比較
            if(isset($validate_conf['compared'][$validate_key])) {
                $validation_rules[] = 'match_value['.$posted[$validate_conf['compared'][$validate_key]].']';
            }
            //--- 文字種別
            if(isset($validate_conf['valid_string'][$validate_key])) {
                $validation_rules[] = 'valid_string['.$validate_conf['valid_string'][$validate_key].']';
            }
            //--- 文字数
            if(isset($validate_conf['length'][$validate_key])) {
                if(isset($validate_conf['length'][$validate_key]['min']) && isset($validate_conf['length'][$validate_key]['max'])) {
                    $validation_rules[] = 'strlen_between['.$validate_conf['length'][$validate_key]['min'].','.$validate_conf['length'][$validate_key]['max'].']';
                }
                else if(isset($validate_conf['length'][$validate_key]['min'])) {
                    $validation_rules[] = 'min_length['.$validate_conf['length'][$validate_key]['min'].']';
                }
                else if(isset($validate_conf['length'][$validate_key]['max'])) {
                    $validation_rules[] = 'max_length['.$validate_conf['length'][$validate_key]['max'].']';
                }
            }
            //--- 正規表現
            if(isset($validate_conf['regex'][$validate_key])) {
                $validation_rules[] = 'match_pattern['.$validate_conf['regex'][$validate_key].']';
            }
            //--- その他
            if(isset($validate_conf['special'][$validate_key])) {
                $validation_rules[] = $validate_conf['special'][$validate_key];
            }

            $validate->add_field($validate_key, \Config::get('payment.key_label.'.$validate_key), implode('|',$validation_rules));

        }

        // 有効期限のチェック（ここだけはしょうがない）
        $datechecked = true;
        $datechecked = \Common\CreditCardValidate::_validation_expiration_date($posted['cardyear'].$posted['cardmonth']);

        if(false === $validate->run($posted) || false === $datechecked) {
            //--- エラー内容を抽出してエラーメッセージ生成
            foreach($validate_conf['check_keys'] as $validate_key) {
                (false !== $validate->error($validate_key))
                    and static::$_error[$validate_key] = $validate->error($validate_key)->get_message(\Config::get('payment.error_messages.invalid_'.(string)$validate->error($validate_key)->get_message(':rule')));
            }
            (false === $datechecked) and static::$_error['carddate'] = \Config::get('payment.error_messages.invalid_card_date');

            return false;
        }

        return true;
    }


   /**
    * ユーザープロフィール取得　複数回実行用
    * 
    * @access public
    * @param array $pamrams
    * @return mixed
    * @author higuchi
    */
    public static function executeGetUserProfilePlural($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.get_user_profile');
        static::$_params = $params;
        $return = static::_execute();
        if(false === $return) {
            if('parameter_error' == (string)static::$_response->error_type)
                static::$_error[] = \Config::get('member.error_messages.invalid_system');
            else
                static::$_error[] = (string)static::$_response->error_message;
        }
        return $return;
    }


    private static function _execute()
    {

        try {
            \Package::load('common');
            $api = \Common\Api::forge(static::$_url)
                    ->set_method(static::$_method)
                    ->set_parameter(static::$_params);
            $result = $api->execute();
            if(false === $result) throw new \Exception(implode("\n",$api->get_error()));

            $response = new \SimpleXMLElement($api->get_response());
            static::$_response = $response;
            if(static::$_response_success != $response->status)
                return false;
        }
        catch(\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
                ->logging();

            return false;
        }

        return true;
    }
}

