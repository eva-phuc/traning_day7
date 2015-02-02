<?php
namespace Android;

/**
 * ショップ情報取得
 * 
 * @author higuchi
 */
class Model_Shoplist_Shops extends \Model {


    /**
    * ショップ一覧情報取得
    * 
    * @access public
    * @return mixed
    * @author higuchi
    */
    public function getData() {
        
        try{
            $query = \DB::query(
                'SELECT * 
                FROM shops ORDER BY initial, sort_name'
            );
            $results = $query->execute();
            $itemData = $results->as_array();
            //TOPリンクURL作成
            \Package::load('iteminfo');
            $resultItems = \Iteminfo\Affili::shop_top_url($itemData);
            
            return $resultItems;
            
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
}