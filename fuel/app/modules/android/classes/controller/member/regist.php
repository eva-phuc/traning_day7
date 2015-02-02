<?php

namespace Android;
use \Model\Notificationinfo;

class Controller_Member_Regist extends \Controller_Template
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
        if(true === (bool)\Aucfan\Auth::instance()->is_login()) {
            //TODO:考える。
        }

        \Config::load('member', 'register', true);
        $device_token = \Input::param('dtoken') ? \Input::param('dtoken') : \Input::cookie('dtoken');
        if(! empty($device_token))
            \Session::set('device_token', $device_token);
    }

    public function get_index()
    {
        $this->template->set_global('error_messages', \Session::get_flash('regist_errror_messages',null));
        $this->template->content = \View::forge('member/regist', null);
    }
    public function post_index()
    {
        $member_register  = \Aucfan\Member::forge();

        $posted = array(
                'nickname'         => \Input::post('nickname'),
                'mailaddr'         => \Input::post('mailaddr'),
                'mailaddr_cnf'     => \Input::post('mailaddr_cnf'),
                'password'         => \Input::post('password'),
                'password_cnf'     => \Input::post('password_cnf'),
                'info_mail_is_permitted'  => \Input::post('info_mail_is_permitted', 'D'),
                );

        $validation_error = array();
        $template_name    = 'member/regist_confirm';
        //--- 戻る
        if(\Input::post('is_back')) {
            $template_name    = 'member/regist';
        }
        //--- バリデーション
        else if(false === $member_register->validateRegist($posted)) {
            $template_name    = 'member/regist';
            $validation_error = $member_register->getError();
        }

        $this->template->set_global('errors',   $validation_error);
        $this->template->set_global('posted',   $posted);
        $this->template->content = \View::forge($template_name, null);
    }

    public function post_execute()
    {
        $posted = array(
                'nickname'         => \Input::post('nickname'),
                'mailaddr'         => \Input::post('mailaddr'),
                'mailaddr_cnf'     => \Input::post('mailaddr_cnf'),
                'password'         => \Input::post('password'),
                'password_cnf'     => \Input::post('password_cnf'),
                'info_mail_is_permitted'  => \Input::post('info_mail_is_permitted'),
                'create_login_session'    => 'alermo',
                );

        $member_register  = \Aucfan\Member::forge();
        //--- 登録実行
        if(false === $member_register->executeRegist($posted)) {
            $this->template->set_global('errors',   array('system'=>implode(" ",$member_register->getError())));
            $this->template->set_global('posted',   $posted);
            $this->template->content = \View::forge('member/regist', null);
            return;
        }

        $response = $member_register->getResponse();
        //--- Cookieセット
        $login_cookie_config = \Config::get('register.cookie');
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
            $json_response = \Config::get('register.defaults.json_templates.login.success');
            $json_response['nickname']   = $nickname;
            $json_response['session_id'] = (string)$response->session_id;
            $json_response['expire']     = $login_cookie_config['expire'];
        }
        else {
            $json_response = \Config::get('register.defaults.json_templates.login.error');
        }

        $aucfan_sendmail  = \Aucfan\Sendmail::forge();
        if(false === $aucfan_sendmail->execute_sendmail_by_id(array('to' => (string)$response->user_id, 'template'=>'alermo_pre'))) {
            // メール送信時のエラーは拾う必要あるかどうか…ないか。
            ;
        }

	//--- デバイストークンとうろく
	if(! is_null(\Session::get('device_token', null))) {
		$params = array(
			'user_id'      => \Aucfan\Auth::instance()->get_user_info('user_id'),
			'os_type'      => 'android',
			'device_token' => \Session::get('device_token'),
		);

		$info = Notificationinfo::forge()->set($params);
		$info->saveDevicetokenInfo();
	}

        $this->template->set_global('json_response', json_encode($json_response), false);
        $this->template->content = \View::forge('member/regist_thanks', null);
        $this->template->head = \View::forge('member/head', null);
    }
}
