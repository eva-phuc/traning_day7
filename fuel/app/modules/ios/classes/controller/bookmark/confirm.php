<?php

/**
 * 商品ハック処理
 * 
 * @author higuchi
 */
namespace Ios;

class Controller_Bookmark_Confirm extends \Controller_Template {

    private $is_login;
    private $_disfa;
    private $user_id;
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
        $this->user_id = $userData['user_id'];
    }
    
    /**
    * 選択項目のチェック、及び商品情報とブックマーク情報の登録
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function action_index() {
        
        $statusCode = \Config::get('cn.status');
        $haq = new Model_Bookmark_Haq();
        
        // データの受け取り
        $inputParams = \Input::post();
        if(empty($inputParams['item_cashe']) || empty($inputParams['url'])) {
            \Response::redirect('ios/error/home/index/error00/');
            return;
        }
        
        // キャッシュされた商品データを参照
        try {
            $itemData = unserialize(\Cache::get($inputParams['item_cashe']));
        
        // 時間切れ等でキャッシュが取れなかった場合、再度商品データ取得
        } catch (\Exception $e) {
            \Package::load('iteminfo');
            $iteminfo = new \Siteparse();
            $iteminfo->setUrl($inputParams['url']);
            $itemData = $iteminfo->getData();
            // 商品再取得エラー
            if ($itemData === false || $itemData['item'] === false || empty($itemData['item'])) {
                \Response::redirect('ios/error/home/index/error00/');
                return;
            }
            // 商品情報をキャッシュに保持
            $item_cashe = $itemData['site'] . $itemData['item_code'];
            if(! empty($itemData['shop_code'])) {
                $item_cashe .= $itemData['shop_code'];
            }
            \Cache::set($item_cashe, serialize($itemData), \Config::get('cn.cache_time'));
            
            // 画面再表示
            $this->viewForm($itemData, $this->errorMsg['error04']);
            return;
        }

        // 未選択項目のチェック
        $val = new Model_Bookmark_Validate($itemData);
        $checkVal = $val->inDataCheck($inputParams);
        if ($checkVal === false) {
            // 未選択によるフォーム再表示
            $this->viewForm($itemData, $this->errorMsg['error05']);
            return;
        }
        
        // 入力データを処理してDB登録
        $result = $haq->setData($inputParams, $itemData, $this->user_id);
        if($result === false) {
            \Response::redirect('ios/error/home/index/error00/');
            return;
        } elseif($result === 'exist') {
            // ブックマーク済商品の場合フォーム再表示
            $this->viewForm($itemData, $this->errorMsg['error06']);
            return;
        }
        
        // 登録成功
        $head = \View::forge('bookmark/confirim/head');
        $this->template->head = $head;
        $this->template->set_global('json_response' , json_encode(array('status'=>'success')));
        
    }
    
    /**
    * ブックマークフォーム再表示
    * 
    * @access private
    * @param string $msg
    * @return response
    * @author higuchi
    */
    private function viewForm($itemData, $msg = null) {
        $formView = \ViewModel::forge('bookmark/api/form');
        $formView->change_view('bookmark/confirim/form');
        $formView->set('inputParams', \Input::post());
        $formView->set('itemData', $itemData);
        $formView->set('url', $itemData['url']);
        $formView->set('msg', $msg);
        $formView->set('_disfa', $this->_disfa);
        $this->template->content = $formView;
    }
}
