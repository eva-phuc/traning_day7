<?php
/**
 * マイフィード商品表示情報整形
 * 
 * @author higuchi
 */

class View_Myfeed_Home_Items extends \ViewModel {
	
    
    /**
     * 表示内容整形
     * 
     * @access public
     * @return Response
     * @author higuchi
     */
	public function view() {
	    
        // 各商品情報表示内容
        foreach ($this->itemData as $key => $value) {
            $items[$key]['bookmark_id'] = $value['bookmark_id'];
            $items[$key]['item_id'] = $value['item_id'];
            $items[$key]['url'] = $value['url'];
            $items[$key]['item_name'] = $value['item_name'];
            $items[$key]['sale'] = $value['sale'];
            $items[$key]['stock'] = $value['stock'];
            $items[$key]['img_url'] = $value['img_url'];
            $items[$key]['bookmark_count'] = $value['bookmark_count'];
            $items[$key]['shop_name'] = $value['name'];
            $items[$key]['alert'] = $value['alert'];
            // 登録価格よりも商品の価格が下がっていた場合、ディスカウント価格表示
            if ($value['price'] < $value['bookmark_price']) {
                $items[$key]['sale_price'] = $value['price'];
            }
            
            // 登録価格
            $items[$key]['price'] = $value['bookmark_price'];
            
            // オプションがある場合
            if (! empty($value['option_id'])) {
                 $items[$key]['option_id'] = $value['option_id'];
                 if (! empty($value['option_stock'])) {
                     $items[$key]['option_stock'] = $value['option_stock'];
                 }
                 // カラー・サイズ等は展開して格納
                 $optionValues = base64_decode($value['option_values']);
                 $optionValues = unserialize($optionValues);
                 $items[$key]['option_values'] = $optionValues;
            }
        }
        $this->_view->set_global('items', $items);
        
        // アラートタイミング
        \Config::load('haqconf', 'cn');
        $this->_view->set_global('alert', \Config::get('cn.alert'));
        
        // 各更新・削除URL
        $formUrl['change'] = '/ios/myfeed/bookmark/change/';
        $formUrl['delete'] = '/ios/myfeed/bookmark/delete/';
        $this->_view->set_global('formUrl', $formUrl);
    }
}