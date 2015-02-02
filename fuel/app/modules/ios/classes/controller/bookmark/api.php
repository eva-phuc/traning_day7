<?php

/**
 * URL解析、及び商品情報取得API
 * 
 * @author higuchi
 */
namespace Ios;

class Controller_bookmark_Api extends \Controller_Rest {
    
    protected $format = 'json';
    private $is_login;
    private $_disfa;
    
    public function before() {
        parent::before();
        \Config::load('errors', 'err');
        \Config::load('haqconf', 'cn');

        // ログインセッション情報
        $this->_disfa = \Input::param('_disfa');
        if(empty($this->_disfa)) $this->_disfa = \Input::cookie('_disfa'); 
        \Package::load('aucfan');
        $aucfanAuth = \Aucfan\Auth::forge($this->_disfa);
        $this->is_login = $aucfanAuth->is_login();
    }
    
    /**
    * URL解析から商品情報取得し、入力フォーム等をJSON形式で返す
    * 
    * @access public
    * @return response
    * @author higuchi
    */
    public function get_url() {
        
        $errorMsg = \Config::get('err.message');
        $statusCode = \Config::get('cn.status');
        
        // ログインエラー
        if ($this->is_login === false || empty($this->_disfa)) { 
            $result = $this->errorResult('', 'no_login');
            return $this->response($result);
        }
        
        $item_detail_url = \Input::get('item_detail_url');
        
        // モデルの定義
        \Package::load('iteminfo');
        $iteminfo = new \Siteparse();
        
        // URL判別
        $is_site = $iteminfo->setUrl($item_detail_url);
        if ($is_site === false) { // 対応サイトエラー
            $result = $this->errorResult($errorMsg['error01']);
            return $this->response($result);
        }
        
        // 商品情報取得
        $itemData = $iteminfo->getData();
        if ($itemData === false) { // 対応商品ページエラー
            $result = $this->errorResult($errorMsg['error02']);
            return $this->response($result);
        }
        if ($itemData['item'] === false || empty($itemData['item'])) {
            // 商品情報取得エラー
            \Common\Error::instance()
                ->set_log("BookMark Error \nHTML Parse or API Error \nSite URL : ".$itemData['url'])
                ->set_email("BookMark Error \nHTML Parse or API Error \nSite URL : ".$itemData['url'],'[ERROR] API ')
                ->logging();
            $result = $this->errorResult($errorMsg['error03']);
            return $this->response($result);
        }
        
        try{
            // ブックマーク用HTMLフォーム成形
            $viewData = \ViewModel::forge('bookmark/api/form');
            $viewData->set('_disfa', $this->_disfa);
            $viewData->set('itemData', $itemData);
            $htmlData = $viewData->render();
        
        } catch (\Exception $e) {
            // 商品データ整形時の異常終了（HTMLパースエラーの可能性）
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
                ->logging();
            $result = $this->errorResult($errorMsg['error03']);
            return $this->response($result);
        }
        
        // 異常終了
        if (empty($htmlData)) { 
            $result = $this->errorResult($errorMsg['error00']);
            return $this->response($result);
        }
        
        // 商品取得成功時の戻り値
        $result = array('status' => $statusCode['ok'],
            'item_title' => $itemData['item']['item_name'],
            'html' => $htmlData,
        );
        
        // 商品情報をキャッシュに保持
        $item_cashe = $itemData['site'] . $itemData['item_code'];
        if(! empty($itemData['shop_code'])) {
            $item_cashe .= $itemData['shop_code'];
        }
        \Cache::set($item_cashe, serialize($itemData), \Config::get('cn.cache_time'));
        $this->response($result);
    }
    
    
    /**
    * エラー時のレス内容作成
    * 
    * @access private
    * @param string $msg
    * @return array
    * @author higuchi
    */
    private function errorResult($msg = '', $status = 'ng') {
        $statusCode = \Config::get('cn.status');
        $result = array('status' => $statusCode[$status], 
            'error_message' => $msg,
        );
        return $result;
    }
}
