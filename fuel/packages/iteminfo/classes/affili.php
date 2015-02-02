<?php
namespace Iteminfo;

/**
 * アフィリエイトタグの埋め込み関連
 * 
 * @author higuchi
 */
class Affili {

    /**
    * 商品IDから商品リンク用URLを取得
    * 
    * @access public
    * @param integer $itemId
    * @return mixed
    * @author higuchi
    */
    public static function getItemUrl($itemId) {
        // 商品IDが1以下だったり整数じゃなければNG
        $itemId = (int)$itemId;
        if ($itemId < 1) {
            return false;
        }
        
        // 商品IDから商品情報取得
		$modelItem = new Model_Item();
        $itemData = $modelItem->getIdData($itemId);
        if (! empty($itemData)) {
            return false;
        }
        
        // 商品IDにアフィリエイトタグの追加
        $afiItems = self::url_replace(array($itemData));
        
        return $afiItems[0]['url'];
    }

    /**
    * URLをアフィリエイトタグが埋め込まれたものに置換
    * 
    * @access public
    * @param array $items
    * @return array
    * @author higuchi
    */
    public static function url_replace($items) {
        
        if (empty($items)) {
            return array();
        }
        
        \Config::load('sites', true);
        
        foreach ($items as $key => $value) {
            
            switch ($value['code']) {
                case 'yshop':
                    $items[$key]['url'] = self::replaceYshop($value['url']);
                    break;
                    
                default:
                    break;
            }
        }
        
        return $items;
    }
    
    /**
    * URLをアフィリエイトタグが埋め込まれたショップトップURLを作成
    * 
    * @access public
    * @param array $items
    * @return array
    * @author higuchi
    */
    public static function shop_top_url($items) {
        
        if (empty($items)) {
            return array();
        }
        
        \Package::load('iteminfo');
        \Config::load('sites', true);
        
        $replaceItems = array();
        foreach ($items as $key => $value) {
            $replaceItems[$key] = $value;
            
            switch ($value['code']) {
                case 'yshop':
                    $items[$key]['link_url'] = self::replaceYshop('http://'.$value['top_url']);
                    break;
                    
                default:
                    $items[$key]['link_url'] = 'http://'.$value['top_url'];
                    break;
            }
        }
        
        return $items;
    }
    
    

    /**
    * アフィリエイト付きURLに置換(Yahoo!ショッピング)
    * 
    * @access private
    * @param string $url
    * @return string
    * @author higuchi
    */
    private static function replaceYshop($url) {
        $affiliUrl = \Config::get('sites.affili.yshop');
        $encodeUrl = urlencode($url);
        return $affiliUrl . $encodeUrl;
    }

}