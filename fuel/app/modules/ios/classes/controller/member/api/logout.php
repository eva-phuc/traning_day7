<?php

namespace Ios;

class Controller_Member_Api_Logout extends \Controller_Rest
{
    public function get_index()
    {
        \Package::load('aucfan');
        \Config::load('member', 'logout', true);
        $this->format = 'json';

        $json_response = \Config::get('logout.defaults.json_templates.logout.success'); 

        //--- 非ログイン状態ならそのままsuccessで返す
        $this->_userinfo['is_login'] = (bool)\Aucfan\Auth::instance()->is_login();
        if(false === $this->_userinfo['is_login'])
            return $this->response($json_response);

        $login_cookie_config = \Config::get('logout.cookie');

        //--- セッションIDがCookieから取れない場合もログアウト状態としてsuccess返却（ゴミIDはセッションドライバ側のGCに任せる）
        $sid = \Cookie::get($login_cookie_config['name'], null);
        if(is_null($sid)) {
            $json_response = \Config::get('logout.defaults.json_templates.logout.error');
            $json_response['error_message'] = 'セッションIDがありません。';
            return $this->response($json_response);
        }

        $member_auth = \Aucfan\Member::forge();
        //--- ログアウト実行
        if(false === $member_auth->executeLogout(array('sid'=>$sid))) {
            $json_response = \Config::get('logout.defaults.json_templates.logout.error');
            $json_response['error_message'] = implode("\n",$member_auth->getError());
        }

        \Cookie::delete(
                $login_cookie_config['name'],
                $login_cookie_config['path'],
                $login_cookie_config['domain']
                );

       return $this->response($json_response);
    }

}
