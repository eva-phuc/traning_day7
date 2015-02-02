<?php
namespace Aucfan;

class Member
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
            static::$_instance = new Member();
        }
        \Config::load('member','member',true);

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
     * オークファンAPI経由でログインを実行
     */
    public static function executeLogin($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.login');

        foreach(\Config::get('member.defaults.api.login') as $def_key => $def_val)
            empty($params[$def_key]) and $params[$def_key] = $def_val;

        //--- バリデーションセット
        $validate = \Validation::forge();
        $required = array_unique(\Config::get('member.validate.login.required',array()));
        foreach($required as $required_key) {
            $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                ->add_rule('required');
        }

        if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if(isset(static::$_response->error_type))
                    static::$_error[] = \Config::get('member.error_messages.'.(string)static::$_response->error_type, \Config::get('member.error_messages.invalid_login'));
                else
                    static::$_error[] = \Config::get('member.error_messages.invalid_login');
                //--- APIからのレスポンスをロギングしておく
                \Common\Error::instance()
                    ->set_log('Login error by API : '.implode("\t",(array)static::$_response))
                    ->logging();
            }
        }
        else {
            //--- エラー内容を抽出してエラーメッセージ生成
            foreach($required as $required_key) {
                (false !== $validate->error($required_key))
                    and static::$_error[] = $validate->error($required_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            }
            $return = false;
        }

        return $return;
    }

    /**
     * オークファンAPI経由でログアウト実行
     */
    public static function executeLogout($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.logout');

        foreach(\Config::get('member.defaults.api.logout') as $def_key => $def_val)
            empty($params[$def_key]) and $params[$def_key] = $def_val;

        static::$_params = $params;

        $return = static::_execute();
        if(false === $return) {
            static::$_error[] = \Config::get('member.error_messages.invalid_logout');

            //--- APIからのレスポンスをロギングしておく
            \Common\Error::instance()
                ->set_log('Logout error by API : '.implode("\t",(array)static::$_response))
                ->logging();
        }

        return $return;
    }

    /**
     * オークファンAPI経由で会員登録実行
     */
    public static function executeRegist($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.regist');

        //--- バリデーション
        if(static::validateRegist($params)) {
            static::$_params = array(
                'mailaddr' => $params['mailaddr'],
                'password' => $params['password'],
                'nickname' => mb_convert_encoding($params['nickname'],"eucJP-win",mb_detect_encoding($params['nickname'])),
                'info_mail_is_permitted' => $params['info_mail_is_permitted'],
                'create_login_session' => $params['create_login_session'],
            );

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type)
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');
                elseif('already_registered' == (string)static::$_response->error_type)
                    static::$_error[] = (string)static::$_response->error_message;
                else
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');

                //--- APIからのレスポンスをロギングしておく
                \Common\Error::instance()
                    ->set_log('Regist error by API : '.implode("\t",(array)static::$_response))
                    ->logging();
            }
        }
        else {
            $return = false;
        }

        return $return;
    }

    /**
     * パスワードリマインダー
     */
    public static function executePasswordRemind($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.password_reminder');

        $validate = \Validation::forge('passwd_remind');
        $required = array_unique(\Config::get('member.validate.password_reminder.required',array()));
        foreach($required as $required_key) {
            $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                ->add_rule('required');
        }


       if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type)
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');
                else
                    static::$_error[] = (string)static::$_response->error_message;
            }
        }
        else {
            foreach($required as $required_key) {
                (false !== $validate->error($required_key))
                    and static::$_error[$required_key] = $validate->error($required_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            }
            $return = false;
        }

        return $return;

    }

    
    public static function executeGetUserProfile($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.get_user_profile');

        $validate = \Validation::forge('get_user_profile');
        $validate->add('user_id', \Config::get('member.key_label.user_id'))
            ->add_rule('required');

        if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type)
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');
                else
                    static::$_error[] = (string)static::$_response->error_message;
            }
        }
        else {
            (false !== $validate->error('user_id'))
                and static::$_error[] = $validate->error('user_id')->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            $return = false;
        }
        return $return;
    }

    public static function executePasswordCheck($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.password_check');

        foreach(\Config::get('member.defaults.api.password_check', array()) as $def_key => $def_val)
            empty($params[$def_key]) and $params[$def_key] = $def_val;

        $validate = \Validation::forge('passwd_check');
        $required = array_unique(\Config::get('member.validate.password_check.required',array()));
        foreach($required as $required_key) {
            $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                ->add_rule('required');
        }

        if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type)
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');
                else
                    static::$_error[] = \Config::get('member.error_messages.invalid_password_check');
            }
        }
        else {
            foreach($required as $required_key) {
                (false !== $validate->error($required_key))
                    and static::$_error[] = $validate->error($required_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            }
            $return = false;
        }

        return $return;
    }
    public static function executePasswordChange($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.password_change');

        foreach(\Config::get('member.defaults.api.password_change', array()) as $def_key => $def_val)
            empty($params[$def_key]) and $params[$def_key] = $def_val;

        $validate = \Validation::forge('passwd_change');
        $required = array_unique(\Config::get('member.validate.password_change.required',array()));
        foreach($required as $required_key) {
            $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                ->add_rule('required');
        }

       if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type)
                    static::$_error[] = \Config::get('member.error_messages.invalid_system');
                else
                    static::$_error[] = (string)static::$_response->error_message;
            }
        }
        else {
            foreach($required as $required_key) {
                (false !== $validate->error($required_key))
                    and static::$_error[$required_key] = $validate->error($required_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            }
            $return = false;
        }

        return $return;
    }

    public static function executeMailaddrChange($params = array())
    {
        $return = true;
        static::$_url = \Config::get('member.url.mailaddr_change');

        foreach(\Config::get('member.defaults.api.mailaddr_change', array()) as $def_key => $def_val)
            empty($params[$def_key]) and $params[$def_key] = $def_val;

        $change_side = 'mobile' == $params['additional_mailaddr_type'] ? 'additional' : 'normal';

        $validate = \Validation::forge('mailaddr_change');
        $required = array_unique(\Config::get('member.validate.mailaddr_change.required.'.$change_side, array()));
        $compared = array_unique(\Config::get('member.validate.mailaddr_change.compared.'.$change_side,  array()));
        foreach($required as $required_key) {
            if(!empty($compared[$required_key])) {
                $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                    ->add_rule('required')->add_rule('match_value', $params[$compared[$required_key]]);
            }
            else {
                $validate->add($required_key, \Config::get('member.key_label.'.$required_key))
                    ->add_rule('required');
            }
        }

        if($validate->run($params)) {
            static::$_params = $params;

            $return = static::_execute();
            if(false === $return) {
                if('parameter_error' == (string)static::$_response->error_type && 'mailaddr' == (string)static::$_response->error_message)
                    static::$_error[] = \Config::get('member.error_messages.invalid_mailaddr');
                else
                    static::$_error[] = ('db_error' == (string)static::$_response->error_type) ? \Config::get('member.error_messages.invalid_mailaddr_exists') : (string)static::$_response->error_message;
            }
        }
        else {
            foreach($required as $required_key) {
                (false !== $validate->error($required_key))
                    and static::$_error[$required_key] = $validate->error($required_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($required_key)->get_message(':rule')));
            }
            $return = false;
        }
        return $return;
    }


    /**
     * 会員登録フォーム用バリデーションメソッド
     */
    public static function validateRegist($posted)
    {
        $validate      = \Validation::forge('register');
        $validate_conf = \Config::get('member.validate.regist', array());
        if(empty($validate_conf['check_keys'])) return true;

        $validate->add_callable('Aucfan\\RegisterValidate');
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
            //--- email
            if(isset($validate_conf['email'][$validate_key])) {
                $validation_rules[] = 'valid_email';
            }
            //--- 正規表現
            if(isset($validate_conf['regex'][$validate_key])) {
                $validation_rules[] = 'match_pattern['.$validate_conf['regex'][$validate_key].']';
            }
            //--- その他
            if(isset($validate_conf['special'][$validate_key])) {
                $validation_rules[] = $validate_conf['special'][$validate_key];
            }

            $validate->add_field($validate_key, \Config::get('member.key_label.'.$validate_key), implode('|',$validation_rules));

        }

        if(false === $validate->run($posted)) {
            //--- エラー内容を抽出してエラーメッセージ生成
            foreach($validate_conf['check_keys'] as $validate_key) {
                (false !== $validate->error($validate_key))
                    and static::$_error[$validate_key] = $validate->error($validate_key)->get_message(\Config::get('member.error_messages.invalid_'.(string)$validate->error($validate_key)->get_message(':rule')));
            }
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

/**
 * バリデーション拡張クラス
 * http://fuelphp.jp/docs/1.7/classes/validation/validation.html#extending_validation
 */
class RegisterValidate
{
    /**
     * 文字数範囲
     */
    public static function _validation_strlen_between($val,$min=0,$max=1000)
    {
        $length = mb_strlen($val);
        return ($min <= $length && $max >= $length);
    }

    /**
     * メルマガ許可/不許可
     */
    public static function _validation_info_mail_is_permitted($val)
    {
        return ('A' == $val || 'D' == $val);
    }
}
