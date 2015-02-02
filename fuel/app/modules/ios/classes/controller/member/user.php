<?php

namespace Ios;
use \Model\Denyinfo;

class Controller_Member_User extends \Controller_Template
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
        if(false === (bool)\Aucfan\Auth::instance()->is_login()) {
            //TODO:考える。
        }

        \Config::load('member', 'user', true);
    }

    public function action_index()
    {
        $this->template->set_global('premium_status', (string)\Aucfan\Auth::instance()->get_user_info('premium_status'));
        $this->template->content = \View::forge('member/user_list', null);
    }

    public function get_profile()
    {
        \Response::redirect('/ios/member/user/');
/************** Phase 1 pend *************
        $message = \Session::get_flash('edit_profile_messages',null);
        $profile = array(
                'mail_addr' => '',
                'nickname' => '',
                'sex' => '',
                'age' => '',
                'occupation' => '',
                'address_01' => '',
                'i_or_e' => '',
                'married' => '',
                'purpose_of_use' => '',
                'aucfan_use_of_freq' => '',
                'income_during_year' => '',
                'info_mail_is_permitted' => '',
                );

        $member_userinfo = \Aucfan\Member::forge();
        $result          = $member_userinfo->executeGetUserProfile(array('user_id'=>\Aucfan\Auth::instance()->get_user_info('user_id')));
        if(false === $result) {
            $message = $member_userinfo->getError();
        }
        else {
            $profile = $member_userinfo::getResponse();
        }

        $this->template->set_global('messages',     \Session::get_flash('edit_profile_messages',null));
        $this->template->set_global('errors',       array());
        $this->template->set_global('profile',      isset($profile->user_profile) ? (array)$profile->user_profile : $profile);
        $this->template->set_global('new_profile',  isset($profile->user_profile) ? (array)$profile->user_profile : $profile);
        $this->template->content = \View::forge('member/edit_profile', null);
************** Phase 1 pend *************/
    }

    /**
     * パスワード変更
     */
    public function get_edit_passwd()
    {
        $this->template->set_global('messages', \Session::get_flash('edit_passwd_messages',null));
        $this->template->content = \View::forge('member/edit_passwd', null);
    }
    public function post_edit_passwd()
    {
        $params = array(
                'mailaddr' => \Aucfan\Auth::instance()->get_user_info('mail_addr'),
                'user_id'  => \Aucfan\Auth::instance()->get_user_info('user_id'),
                'password'         => \Input::post('password'),
                'new_password'     => \Input::post('new_password'),
                'new_password_cnf' => \Input::post('new_password_cnf'),
                );

        $member_passwd = \Aucfan\Member::forge();
        $redirect_path = '/ios/member/user/edit_passwd_done';
        if(false === $member_passwd->executePasswordChange($params)) {
            $message = $member_passwd->getError();
            $redirect_path = '/ios/member/user/edit_passwd';
        }
        else {
            $message = \Config::get('member.messages.success.password_change');
        }

        \Session::set_flash('edit_passwd_messages',$message);
        \Response::redirect($redirect_path);
    }
    public function action_edit_passwd_done()
    {
        $this->template->content = \View::forge('member/edit_passwd_done');
    }


    /**
     * メールアドレス変更
     */
    public function get_edit_mailaddress()
    {
        $this->template->set_global('messages',            \Session::get_flash('mailaddr_change_messages',null));
        $this->template->set_global('messages_additional', \Session::get_flash('mailaddr_change_messages_additional',null));
        $this->template->content = \View::forge('member/edit_mailaddress', null);
    }
    public function post_edit_mailaddress()
    {

        $params = array(
                'user_id'          => \Aucfan\Auth::instance()->get_user_info('user_id'),
                'account'          => \Aucfan\Auth::instance()->get_user_info('mail_addr'),
                'password'         => \Input::post('password'),
                'mailaddr'         => \Input::post('mailaddr'),
                'mailaddr_cnf'     => \Input::post('mailaddr_cnf'),
                'additional_mailaddr_type' => \Input::post('additional_mailaddr_type'),
                'additional_mailaddr'      => \Input::post('additional_mailaddr'),
                'additional_mailaddr_cnf'  => \Input::post('additional_mailaddr_cnf'),
                );

        $member    = \Aucfan\Member::forge();
        $view_name = null;
        if(false === $member->executePasswordCheck($params)) {
            $passwd_error = $member->getError();
            $message[]    = $passwd_error[0];
        }
        else {
            if(false === $member->executeMailaddrChange($params)) 
                $message   = $member->getError();
            else 
                $view_name = 'member/edit_mailaddress_done';
        }

        $flash_key = 'mobile' == $params['additional_mailaddr_type'] ? 'mailaddr_change_messages_additional' : 'mailaddr_change_messages';

        isset($message) and \Session::set_flash($flash_key, $message);

        if(! is_null($view_name))
            $this->template->content = \View::forge($view_name);
        else
            \Response::redirect('/ios/member/user/edit_mailaddress');

    }

    /**
     * メールアドレス変更
     */
    public function get_password_reminder()
    {
        $this->template->set_global('messages', \Session::get_flash('password_reminder_messages',null));
        $this->template->content = \View::forge('member/password_reminder', null);
    }
    public function post_password_reminder()
    {
        $member    = \Aucfan\Member::forge();
        if(false === $member->executePasswordRemind(array('mailaddr'=>\Input::post('mailaddr'),))) {
            \Session::set_flash('password_reminder_messages', $member->getError());
            \Response::redirect('/ios/member/user/password_reminder');
        }

        $this->template->content = \View::forge('member/password_reminder_done');
    }
}
