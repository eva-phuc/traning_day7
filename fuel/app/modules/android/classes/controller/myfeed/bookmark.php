<?php

/**
 * マイフィード用ブックマーク
 * 
 * @author higuchi
 */
namespace Android;

class Controller_Myfeed_Bookmark extends \Controller {
    
    private $_disfa;
    private $userId;
    
    public function before() {
        parent::before();
        \Config::load('haqconf', 'cn');
        // ログイン情報
        $userData = Model_Common_Login::check(array('user_id'), false);
        if ($userData === false) return;
        $this->_disfa = $userData['_disfa'];
        $this->userId = $userData['user_id'];
    }
    

    /**
    * ブックマーク済アラートの変更
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public function action_change() {
        
        $inputParams = \Input::post();
        
        // 値取得エラー
        if (empty($inputParams['bookmark_id']) || empty($inputParams['alert']) || empty($this->userId)) {
            return 'error';
        }
        // ブックマークのアラート情報アップデート
        $up = new Model_Myfeed_Bookmark();
        $result = $up->updateAlart($inputParams, $this->userId);
        if ($result === false) {
            return 'error';
        }
        
        return;
    }

    /**
    * ブックマーク削除・有効切り替え
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public function action_delete() {
        
        $inputParams = \Input::post();
        
        // 値取得エラー
        if (empty($inputParams['bookmark_id']) || empty($inputParams['delete_flag']) || empty($this->userId)) {
            return 'error';
        }
        // ブックマーク情報の更新
        $up = new Model_Myfeed_Bookmark();
        $result = $up->deleteStatus($inputParams, $this->userId);
        if ($result === false) {
            return 'error';
        }
        
        return;
    }


}

