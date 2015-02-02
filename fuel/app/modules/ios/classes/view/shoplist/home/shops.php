<?php
/**
 * ショップリスト表示内容整形
 * 
 * @author higuchi
 */

class View_Shoplist_Home_Shops extends \ViewModel {


    /**
     * 表示内容整形
     * 
     * @access public
     * @return Response
     * @author higuchi
     */
    public function view() {
        
        // インデックスごとにショップリスト作成
        $initial = '';
        $shopList = array();
        $cnt = 0;
        foreach ($this->shopData as $key => $value) {
            if ($initial != $value['initial']) {
                if (! empty($initial)) $cnt++;
                $initial = $value['initial'];
                $shopList[$cnt]['initial'] = $initial;
            }
            $shopList[$cnt]['shop'][] = $value;
        }
        
        $this->_view->set_global('shopList', $shopList);
    }
}