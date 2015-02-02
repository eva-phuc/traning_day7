<?php

namespace Android;

class Controller_Payment_Cardchange extends \Controller_Template
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
        if(\Config::get('p_credit.status.enable') == $status)
            $is_permitted = true;

        if(false === $is_permitted)
            \Response::redirect('http://'.\Input::server('SERVER_NAME').'/about.html');

        $this->view = \ViewModel::forge('payment/credit');
        $this->view->setUserInfo($this->user_info);
        $this->template->head_title = 'カード情報変更';
    }

    /**
     * カード登録
     */
    public function get_index()
    {
        $this->view->set_filename('payment/cardchange');
        $this->template->content = $this->view;
    }
    /**
     * カード確認
     */
    public function post_index()
    {
        $payment = \Aucfan\Payment::forge();
        //--- 入力値のバリデーション
        if(false === $payment->validateCreditInfo(\Input::post())) {
            $this->view->setFormError($payment->getError());
            $this->template->content = $this->view->set_filename('payment/cardchange');
            return;
        }

        //--- カード登録
        if(false === $payment->executeSaveCard(\Input::post(),$this->user_info)) {
            $this->view->setFormError(array(\Config::get('p_credit.error_messages.invalid_save_card')));
        }
        else
            $this->view->set('done_message','カード情報を変更しました。');

        $this->template->content = $this->view->set_filename('payment/cardchange');
    }
}


