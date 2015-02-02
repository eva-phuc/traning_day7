<?php
/**
 * 商品情報表示内容整形 (汎用)
 * 
 * @author higuchi
 */

class View_Bookmark_API_Form extends \ViewModel {
	
    
    /**
     * 表示内容整形
     * 
     * @access public
     * @return Response
     * @author higuchi
     */
	public function view() {
	    
        //メッセージの表示
        if (! empty($msg)) {
            $this->_view->set_global('msg', $msg);
        }
        
        // セッション
        $this->_view->set_global('_disfa', $this->_disfa);
        
        // サイト・商品概要情報
        $this->_view->set_global('site_name', \Config::get('s.name.'.$this->itemData['site']));
        
        $item_cashe = $this->itemData['site'] . $this->itemData['item_code'];
        if(! empty($this->itemData['shop_code'])) {
            $item_cashe .= $this->itemData['shop_code'];
        }
        $this->_view->set_global('item_cashe', $item_cashe);
        
        $item = $this->itemData['item'];
        $this->_view->set_global('item_name', $item['item_name']);
        $this->_view->set_global('price', $item['price']);
        $this->_view->set_global('img_url', $item['img_url']);
        $this->_view->set_global('url', $this->itemData['url']);
        
        // オプション情報
        if (! empty($item['sub_options'])) {
            $option = array();
            foreach ($item['sub_options'] as $key => $value) {
                $optionName[$key] = $value['name'];
                $optionValue[$key] = $value['value'];
            }
            $this->_view->set_global('option_value', $optionValue);
            $this->_view->set_global('option_name', $optionName);
        }
        
        // アラートタイミング
        $this->_view->set_global('alert', \Config::get('cn.alert'));
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