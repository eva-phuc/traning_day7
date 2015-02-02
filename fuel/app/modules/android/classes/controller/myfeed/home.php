<?php

/**
 * マイフィード
 * 
 * @author higuchi
 */
namespace Android;

class Controller_Myfeed_Home extends \Controller_Template {
    
    private $_disfa;
    private $userId;
    private $errorMsg;
    
    public function before() {
        parent::before();
        \Config::load('errors', 'err');
        \Config::load('haqconf', 'cn');
        // エラーメッセージ
        $this->errorMsg = \Config::get('err.message');
        // ログイン情報
        $userData = Model_Common_Login::check(array('user_id'));
        $this->_disfa = $userData['_disfa'];
        $this->userId = $userData['user_id'];
    }

    /**
    * マイフィードの項目の取得・表示
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index() {
       
        // テンプレートの固定表示情報
        $sort = \Config::get('cn.myfeed_sort');
        $this->template->set_global('sort', $sort);
        $this->template->set_global('_disfa', $this->_disfa);
        
        // 検索条件生成、商品情報取得
        $inputParams = \Input::get();
        $searchParams = $this->searchItem($inputParams, $sort);
        $item = new Model_Myfeed_Home();
        $itemData = $item->getData($searchParams, $this->userId);
        if(empty($itemData) || $itemData === false) {
            $view = \View::forge('myfeed/home/error', array('error_msg' => $this->errorMsg['error07']));
            $this->template->content = $view;
            return;
        }
        
        // --- 商品表示 ---
        //ヘッダーjs
        $head = \View::forge('myfeed/home/head');
        $this->template->head = $head;
        
        // アイテムリスト
        $items = \ViewModel::forge('myfeed/home/items');
        $items->set('itemData', $itemData);
        
        // メイン表示
        $view = \View::forge('myfeed/home/main');
        $view->set('itemList', $items);
        $this->template->content = $view;
    }
    
    
    /**
    * マイフィードのページネーション追加表示(jQuery用)
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_getitems() {
        
        $inputParams = \Input::get();
        // ログインエラー
        if (empty($this->userId)) {
            return 'error';
        }
        
        // 検索条件生成、商品情報取得
        $sort = \Config::get('cn.myfeed_sort');
        $searchParams = $this->searchItem($inputParams, $sort);
        $item = new Model_Myfeed_Home();
        $itemData = $item->getData($searchParams, $this->userId);
        
        // システムエラー
        if($itemData === false) {
            return 'error';
        }
        // データが無い、指定ページ数以上は表示させない
        if(empty($itemData)) {
            return 'none';
        }
        
        // 商品表示
        return \ViewModel::forge('myfeed/home/items')
            ->set('itemData', $itemData);
    }
    
    
    /**
    * 検索内容の生成
    * 
    * @access private
    * @param array $inputParams
    * @return array
    * @author higuchi
    */
    private function searchItem($inputParams) {
        
        
        if (empty($inputParams['sort'])) {
            $sort = \Config::get('cn.default_search.sort');
            $reqParams['sort'] = $sort;
        } else {
            $reqParams['sort'] = $inputParams['sort'];
        }
        if (empty($inputParams['page'])) {
            $reqParams['page'] = 1;
        } else {
            $reqParams['page'] = (int)$inputParams['page'];
        }
        return $reqParams;
    
    }

}
