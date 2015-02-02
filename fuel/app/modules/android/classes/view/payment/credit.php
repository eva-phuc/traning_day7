<?php

class View_Payment_credit extends \ViewModel
{

    private $_user_info = array();
    private $_error     = array();
    public  $paymentResponse = null;

    /**
     * Prepare the view data, keeping this in here helps clean up
     * the controller.
     *
     * @return void
     */
    // @override
    public function set_filename($file)
    {
        $this->_view->set_filename($file);
        return $this;
    }

    public function setFormError($error=array())
    {
        $this->_error = $error;
        return $this;
    }
    public function setUserInfo($userinfo)
    {
        $this->_user_info = $userinfo;
        return $this;
    }

    /**
     * Prepare the view data, keeping this in here helps clean up
     * the controller.
     *
     * @return void
     */
    public function view()
    {
        $error     = $this->_error;
        $user_info = $this->_user_info;

        $self = $this;
        $getPaymentInfo  = function($user_info) use(&$self) {
            if(! is_null($self->paymentResponse)) return $self->paymentResponse;

            $payment = \Aucfan\Payment::forge();
            if(false === $payment->getCardNumber($user_info) || ! isset($payment->getResponse()->card_list->card_info->CardNo))
                $self->paymentResponse = false;
            else
                $self->paymentResponse = $payment->getResponse();

            return $self->paymentResponse;
        };

        //--- 月セレクトボックス作成の配列を取得
        $this->_view->set_global('get_expiration_month', function() {
            $array = array(''=>'月');
            foreach(array_fill(1,12,1) as $key => $void) {
                $array[sprintf('%02d',$key)] = $key;
            }

            return $array;
        });

        //--- 年セレクトボックス作成の配列を取得
        $this->_view->set_global('get_expiration_year', function() {
            $array = array(''=>'年');
            foreach(array_fill(0,14,1) as $key => $void) {
                $array[intval(date("y")+$key)] = intval(date("y")+$key);
            }

            return $array;
        });

        //--- 入力カード番号をマスクして返却
        $this->_view->set_global('create_masked_cardno', function($cardno,$display_suffix_num=4,$delemeter='&nbsp;') {
            list($tmp_cardno_mask,$tmp_cardno_display) = str_split($cardno,intval(strlen($cardno) - intval($display_suffix_num)));

            $display_cardno = str_repeat('*',strlen($tmp_cardno_mask)).''.$tmp_cardno_display;
            return implode($delemeter,str_split($display_cardno,4));
        });

        //--- 登録済カード番号を取得
        $this->_view->set_global('get_card_number', function() use($user_info,$getPaymentInfo) {
            $paymentResponse = $getPaymentInfo($user_info);
            if(false === $paymentResponse)
                return 'カード情報が取得できませんでした';

            $cardno = (string)$paymentResponse->card_list->card_info->CardNo;
            $tmp_cardno = str_split($cardno,4);
            return str_replace('*','X',implode('-',$tmp_cardno));
        });
        //--- 登録済カードの有効期限を取得
        $this->_view->set_global('get_card_expire', function() use($user_info,$getPaymentInfo) {
            $paymentResponse = $getPaymentInfo($user_info);
            if(false === $paymentResponse)
                return 'カード情報が取得できませんでした';

            $cardexpire = (string)$paymentResponse->card_list->card_info->Expire;
            list($tmp_year,$tmp_month) = str_split($cardexpire,2);
            return $tmp_month.'/'.$tmp_year;
        });

        //--- エラーのゲッター
        $this->_view->set_global('get_form_error', function() use($error) {
            return $error;
        });
        //--- ユーザセッション情報のゲッター
        $this->_view->set_global('get_user_info', function($key=null) use ($user_info) {
            if(! is_null($key)) 
                return isset($user_info->{$key}) ? $user_info->{$key} : null;

            return $user_info;
        });
    }
}
