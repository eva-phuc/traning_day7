<?php

/**
 * トップフィード
 * 
 * @author higuchi
 */
namespace Android;
use \Model\Notificationinfo;

class Controller_Topfeed_Home extends \Controller_Template {
    
    private $errorMsg;
    
    public function before() {
        parent::before();
        \Config::load('errors', 'err');
        \Config::load('haqconf', 'cn');
        // エラーメッセージ
        $this->errorMsg = \Config::get('err.message');
        
        // デバイストークン等の会員情報を更新
        $userData = Model_Common_Login::check(array('user_id'), false);
        $device_token = \Input::param('dtoken') ? \Input::param('dtoken') : \Input::cookie('dtoken');
        if(! is_null($device_token) && $userData !== false) {
            $params = array(
            'user_id'      => $userData['user_id'],
            'os_type'      => 'android',
            'device_token' => $device_token,
            );
        $info = Notificationinfo::forge()->set($params);
        $info->saveDevicetokenInfo();
        }
    }
    
    
    /**
    * TOPフィードの項目の取得・表示
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index() {
    
        // テンプレートの固定表示情報
        $sort = \Config::get('cn.topfeed_sort');
        $this->template->set_global('sort', $sort);
        
        // 検索条件生成し、商品情報取得
        $inputParams = \Input::get();
        $searchParams = $this->searchItem($inputParams);
        $item = new Model_Topfeed_Home();
        $itemData = $item->getData($searchParams);
        if(empty($itemData) || $itemData === false) {
            $view = \View::forge('topfeed/home/error', array('error_msg' => $this->errorMsg['error07']));
            $this->template->content = $view;
            return;
        }
        
        // --- 商品表示 ---
        //ヘッダーjs
        $head = \View::forge('topfeed/home/head');
        $this->template->head = $head;
        
        // アイテムリスト
        $items = \ViewModel::forge('topfeed/home/items');
        $items->set('itemData', $itemData);
        
        // メイン表示
        $view = \View::forge('topfeed/home/main');
        $view->set('itemList', $items);
        $this->template->content = $view;
    }
    
    
    /**
    * 画面最下部スクロール時の次ページ商品表示(jQuery用)
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_getitems() {
        
        // 検索条件生成し、商品情報取得
        $inputParams = \Input::get();
        $searchParams = $this->searchItem($inputParams);
        $item = new Model_Topfeed_Home();
        $itemData = $item->getData($searchParams);
        
        // エラー
        if($itemData === false) {
            return 'error';
        }
        // データが無い、指定ページ数以上は表示させない
        if(empty($itemData)) {
            return 'none';
        }
        
        // 商品表示
        return \ViewModel::forge('topfeed/home/items')
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

