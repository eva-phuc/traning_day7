<?php

namespace Android;
use \Model\Notificationinfo;

class Controller_Member_Api_Mail extends \Controller_Rest
{
    public function before()
    {
        parent::before();
        $this->_init();

    }
    private function _init()
    {
        $this->format = 'html';
        \Package::load('aucfan');
        if(false === (bool)\Aucfan\Auth::instance()->is_login()) {
            return;
        }

        \Config::load('member', 'user', true);
    }

    public function get_info()
    {
        $params = array(
            'user_id'    => \Aucfan\Auth::instance()->get_user_info('user_id'),
            'mail_alert' => 0 
        );
        $info = Notificationinfo::forge();

        echo $info->findMailalertByPK($params['user_id']);
        return;
    }
    /**
     * メール設定変更
     */
    public function get_switch()
    {
        $params = array(
            'user_id'    => \Aucfan\Auth::instance()->get_user_info('user_id'),
            'mail_alert' => (int)\Input::get('sw'),
        );

        $info = Notificationinfo::forge()->set($params);
        $info->saveMailInfo();
        $is_error = (bool)$info->getNotificationinfoError();

        return $this->response(($is_error ? 'error' : 'success'));
    }
}
