<?php

namespace Android;

class Controller_Payment_Cancel extends \Controller_Template
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
        if(\Config::get('p_credit.status.enable') === $status || ('end' == \Request::active()->action && \Config::get('p_credit.status.stop') == $status)) 
            $is_permitted = true;

        if(false === $is_permitted)
            \Response::redirect('http://'.\Input::server('SERVER_NAME').'/about.html');

        $this->view = \ViewModel::forge('payment/credit');
        $this->view->setUserInfo($this->user_info);
        $this->template->head_title = 'プレミアムサービスの解約';
    }

    /**
     * 解約注意書き
     */
    public function action_notice()
    {
        $this->view->set_filename('payment/cancel_notice');
        $this->template->content = $this->view;
    }
    /**
     * 解約LP
     */
    public function action_lp()
    {
        $this->view->set_filename('payment/cancel_lp');
        $this->template->content = $this->view;
    }
    /**
     * 解約確認
     */
    public function action_confirm()
    {
        $this->view->set_filename('payment/cancel_confirm');
        $this->template->content = $this->view;
    }
    /**
     * 解約実行
     */
    public function post_execute()
    {
        $payment = \Aucfan\Payment::forge();

        //--- アラーもプレミアム解約
        if(false === $payment->executeChangeAlermoStatus(array(),$this->user_info,'off')) {
            $this->view->setFormError(array(\Config::get('p_credit.error_messages.invalid_change_status')));
            $this->view->set_filename('payment/cancel_confirm');
            return;
        }

        //--- メール送信
        \Aucfan\Sendmail::forge()->execute_sendmail_by_id(array('to' => (string)$this->user_info->user_id, 'template'=>'alermo_stop'));

        //--- 完了ページヘ
        \Response::redirect('/android/payment/cancel/end');
    }

    public function action_end()
    {
        $this->view->set_filename('payment/cancel_end');
        $this->template->content = $this->view;
    }
}

