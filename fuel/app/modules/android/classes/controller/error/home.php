<?php

/**
 * エラー画面
 * 
 * @author higuchi
 */
namespace Android;

class Controller_Error_Home extends \Controller_Template {
    public function before() {
        parent::before();
        \Config::load('errors', 'err');
    }

    /**
    * エラー表示
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index($errorNo = null) {
        
        $errorMsg = \Config::get('err.message');
        if (empty($errorNo)) {
            $msg = $errorMsg['error08'];
        } else {
            $msg = $errorMsg[$errorNo];
        }
        $view = \View::forge('error/index', array('error_msg' => $msg));
        $this->template->content = $view;
        return;
    }


}