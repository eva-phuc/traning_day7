<?php

namespace Ios;
use \Model\Notificationinfo;

class Controller_Member_Login extends \Controller_Template
{
    //public $template = 'member/template';

    public function before()
    {
        parent::before();
        $this->_init();

    }
    private function _init()
    {
        \Package::load('aucfan');
        \Config::load('member', 'login', true);

        $device_token = \Input::param('dtoken') ? \Input::param('dtoken') : \Input::cookie('dtoken');
        if(! empty($device_token))
            \Session::set('device_token', $device_token);
    }

    public function get_index()
    {
        $this->template->content = \View::forge('member/login', null);
    }

    public function post_index()
    {
        \Package::load('aucfan');
        $member_auth = \Aucfan\Member::forge();
        //--- ログイン実行
        if(false === $member_auth->executeLogin(\Input::post())) {
            $this->template->set_global('error_messages', $member_auth->getError());
            $this->template->content = \View::forge('member/login', null);
            return;
        }

        $response = $member_auth->getResponse();

        //--- Cookieセット
        $login_cookie_config = \Config::get('login.cookie');
        \Cookie::set(
                $login_cookie_config['name'],
                (string)$response->session_id,
                $login_cookie_config['expire'],
                $login_cookie_config['path'],
                $login_cookie_config['domain']
                );

        //--- ニックネーム取得
        \Aucfan\Auth::instance()->check_aucfan_session((string)$response->session_id);
        $nickname = \Aucfan\Auth::instance()->get_user_info('nickname');
        if($nickname) {
            $json_response = \Config::get('login.defaults.json_templates.login.success');
            $json_response['nickname']   = $nickname;
            $json_response['session_id'] = (string)$response->session_id;
            $json_response['expire']     = $login_cookie_config['expire'];
        }
        else {
            $json_response = \Config::get('login.defaults.json_templates.login.error');
        }

	//--- デバイストークンとうろく
	if(! is_null(\Session::get('device_token', null))) {
		$params = array(
			'user_id'      => \Aucfan\Auth::instance()->get_user_info('user_id'),
			'os_type'      => 'ios',
			'device_token' => \Session::get('device_token'),
		);

		$info = Notificationinfo::forge()->set($params);
		$info->saveDevicetokenInfo();
	}

        $this->template->set_global('json_response', json_encode($json_response), false);
        $this->template->content = \View::forge('member/login', null);
        $this->template->head = \View::forge('member/head', null);
    }
}
