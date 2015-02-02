<?php

/**
 * お問合せ
 * 
 * @author higuchi
 */
namespace Ios;

class Controller_Inquiry_Home extends \Controller_Template {
    
    private $_disfa;
    private $user_id;
    private $errorMsg;
    private $mail_addr = null;
    
    public function before() {
        parent::before();
        \Config::load('haqconf', 'cn');
        \Config::load('errors', 'err');
        $this->errorMsg = \Config::get('err.message');
        // ログインセッション情報
        $userData = Model_Common_Login::check(array('user_id', 'mail_addr'), false);
        if ($userData !== false) {
            $this->_disfa = $userData['_disfa'];
            $this->user_id = $userData['user_id'];
            $this->mail_addr = $userData['mail_addr'];
        }
    }
    
    /**
    * お問合せ一覧画面の表示
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index() {
        $list = \Config::get('cn.query_list');
        $view = \View::forge('inquiry/home/inquiry', array('query_list' => $list, '_disfa' => $this->_disfa));
        $this->template->content = $view;
        return;
    }
    
    /**
    * 入力フォーム表示
    * 
    * @access public
    * @param string $param 
    * @return response
    * @author higuchi
    */
    public function action_query($param = null) {
        
        // お問合せ一覧内容取得
        $list = \Config::get('cn.query_list');
        foreach ($list as $key => $value) {
            $listKey[] = $key;
        }

        // パラメーターエラー
        if (empty($param) || ! in_array($param, $listKey)) {
            \Response::redirect('ios/error/home/index/error00/');
            return;
        }
        
        // フォーム表示
        $view = \ViewModel::forge('inquiry/home/query');
        $view->set('query_param', $param);
        $view->set('_disfa', $this->_disfa);
        $view->set('mail_addr', $this->mail_addr);
        $this->template->content = $view;
        return;
    }
    
    
    
    /**
    * 入力チェック、送信
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_confirm() {
        
        $inputData = \Input::post();
        
        // バリデーションチェック
        $val = new Model_Inquiry_Validate();
        $checkVal = $val->inputCheck();
        if (!$checkVal->run()) {
            // エラー
            $view = \ViewModel::forge('inquiry/home/query');
            $view->set('_disfa', $this->_disfa);
            $view->set('error_msg', $checkVal->error());
            $this->template->content = $view;
            return;
        }
        
        // 送信パッケージ処理
        \Package::load('aucfan');
        \Config::load('sendmail', 'sendm');
        
        // 件名作成
        $list = \Config::get('cn.query_list');
        $subject = \Config::get('sendm.defaults.inquiry.subject');
        $subject .= $list[$inputData['query_param']];
        
        // 送信処理
        $inputData['os'] = 'ios';
        $result = \Aucfan\Sendmail::forge()->execute_sendmail_by_in_common(
            array('tmpl_key'=>'inquiry', 'inputData'=>$inputData, 'subject'=>$subject));
        if ($result === false) {
            // エラー
            $view = \ViewModel::forge('inquiry/home/query');
            if(! empty($this->_disfa)) {
                $view->set('_disfa', $this->_disfa);
            }
            $view->set('error_msg', array('送信に失敗しました。'));
            $this->template->content = $view;
            return;
        }
        
        // 送信成功表示
        $view = \View::forge('inquiry/home/success');
        $this->template->content = $view;
        
    }
}