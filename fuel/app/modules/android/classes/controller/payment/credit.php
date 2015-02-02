<?php

namespace Android;

class Controller_Payment_Credit extends \Controller_Template
{
    public $template      = 'payment/template';
    public $user_info     = array();
    public $view          = null;

    public function before()
    {
        parent::before();
        $this->_init();
    }
    private function _init()
    {
        $is_permitted = false;
        \Package::load('aucfan');
        \Config::load('payment', 'p_credit', true);

        \Aucfan\Auth::instance()->check_aucfan_session();
        $status = \Aucfan\Auth::instance()->get_user_info('alermo_status');
        $this->user_info = \Aucfan\Auth::instance()->get_user_info();
        if(\Config::get('p_credit.status.none') === $status || ('thanks' == \Request::active()->action && \Config::get('p_credit.status.enable') == $status)) 
            $is_permitted = true;

        if(false === $is_permitted)
            \Response::redirect('http://'.\Input::server('SERVER_NAME').'/about.html');

        $this->view = \ViewModel::forge('payment/credit');
        $this->template->head_title = 'プレミアム登録';
    }

    /**
     * カード登録
     */
    public function get_subscribe()
    {
        $this->view->set_filename('payment/subscribe');
        $this->template->content = $this->view;
    }
    /**
     * カード確認
     */
    public function post_subscribe()
    {
        $payment = \Aucfan\Payment::forge();
        if(false === $payment->validateCreditInfo(\Input::post())) {
            $this->view->setFormError($payment->getError());
            $this->view->set_filename('payment/subscribe');
        }
        else
            $this->view->set_filename('payment/subscribe_conf');

        $this->template->content = $this->view;
    }
    /**
     * 決済実行
     */
    public function post_execute_subscribe()
    {
        //--- POST値の取得
        $posted  = \Input::post('post_info');
        if(empty($posted)) {
            $this->view->setFormError(array(\Config::get('p_credit.error_messages.invalid_system')));
            $this->template->content = $this->view->set_filename('payment/subscribe');
            return;
        }
        $posted  = unserialize(urldecode($posted));

        //--- POST値のバリデーション
        $payment = \Aucfan\Payment::forge();
        if(false === $payment->validateCreditInfo($posted)) {
            $this->view->setFormError($payment->getError());
            $this->template->content = $this->view->set_filename('payment/subscribe');
            return;
        }

        //--- カード登録
        if(false === $payment->executeSaveCard($posted,$this->user_info)) {
            $this->view->setFormError(array(\Config::get('p_credit.error_messages.invalid_save_card')));
            $this->template->content = $this->view->set_filename('payment/subscribe');
            return;
        }

        //--- アラーもプレミアム登録
        if(false === $payment->executeChangeAlermoStatus($posted,$this->user_info)) {
            $this->view->setFormError(array(\Config::get('p_credit.error_messages.invalid_change_status')));
            $this->template->content = $this->view->set_filename('payment/subscribe');
            return;
        }

        //--- メール送信
        \Aucfan\Sendmail::forge()->execute_sendmail_by_id(array('to' => (string)$this->user_info->user_id, 'template'=>'alermo_start'));

        //--- 完了ページヘ
        \Response::redirect('/android/payment/credit/thanks');
    }

    public function action_thanks()
    {
        $this->view->set_filename('payment/subscribe_thanks');
        $this->template->content = $this->view;
    }
}

