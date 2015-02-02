<?php

/**
 * ショップリスト
 * 
 * @author higuchi
 */
namespace Android;

class Controller_Shoplist_Home extends \Controller_Template {
    
    public function before() {
        parent::before();
        \Config::load('haqconf', 'cn');
    }

    /**
    * ショップ一覧の取得・表示
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index() {
        
        // ショップデータ取得
        $shop = new Model_Shoplist_Shops();
        $shopData = $shop->getData();
        if(empty($shopData) || $shopData === false) {
            \Response::redirect('android/error/home/index/error00/');
            return;
        }
        
        // ショップ一覧表示
        $view = \ViewModel::forge('shoplist/home/shops');
        $view->set('shopData', $shopData);
        $this->template->content = $view;
    }
}