<?php

namespace Android;

class Controller_Payment_Top extends \Controller_Template
{
    public $alermo_status = '';

    public function before()
    {
        parent::before();
        $this->_init();

    }
    private function _init()
    {
        $is_logined = false;
        \Package::load('aucfan');
        \Config::load('member',  'm_top', true);
        \Config::load('payment', 'p_top', true);

        $disfa = \Input::param('_disfa') ? \Input::param('_disfa') : \Input::cookie('_disfa');
        if(! empty($disfa)) {
            
            \Aucfan\Auth::instance()->check_aucfan_session($disfa);
            $status = \Aucfan\Auth::instance()->get_user_info('alermo_status');

            if(false !== $status) {
                $this->alermo_status = $status;
                \Cookie::set(\Config::get('m_top.cookie.name'), (string)$disfa);
                $is_logined = true;
            }
        }

        if(false === $is_logined)
            \Response::redirect('http://'.\Input::server('SERVER_NAME').'/about.html');

    }

    public function get_index()
    {
        if(\Config::get('p_top.status.enable') == $this->alermo_status)
            \Response::redirect('/android/payer_menu.html');
        else
            \Response::redirect('/android/premium_lp.html');
    }

}

