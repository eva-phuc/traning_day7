<?php
/**
 * トップフィード商品表示情報整形
 * 
 * @author higuchi
 */

class View_Topfeed_Home_Items extends \ViewModel {
	
    
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
            $items[$key]['item_id'] = $value['item_id'];
            $items[$key]['url'] = $value['url'];
            $items[$key]['item_name'] = $value['item_name'];
            $items[$key]['sale'] = $value['sale'];
            $items[$key]['stock'] = $value['stock'];
            $items[$key]['img_url'] = $value['img_url'];
            $items[$key]['bookmark_count'] = $value['bookmark_count'];
            $items[$key]['shop_name'] = $value['name'];
            // 現在セール価格(セール時の現在価格 or 定価＞現在価格 or 登録最高値＞現在価格)
            if ($value['sale'] == 'yes'
                     || (! empty($value['default_price']) && $value['default_price'] > $value['price'])
                     || ($value['bookmark_high_price'] > $value['price'])
                ) {
                // 表示
                $items[$key]['sale_price'] = $value['price'];
            }
            // 通常表示価格(1.定価  2.登録最高値)
            if (! empty($value['default_price']) && ($value['default_price'] > $value['bookmark_high_price'])) {
                $items[$key]['price'] = $value['default_price'];
            } else {
                $items[$key]['price'] = $value['bookmark_high_price'];
            }
        }
        $this->_view->set_global('items', $items);
        
    }
    /**
     * 別の表示フォーマットで出力
     * 
     * @access public
     * @param string $view
     * @return Response
     * @author higuchi
     */
    public function change_view($view) {
        $this->_view = View::forge($view);
        return $this;
    }
}