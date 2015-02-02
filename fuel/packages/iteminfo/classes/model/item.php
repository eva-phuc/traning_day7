<?php
namespace Iteminfo;

/**
 * 登録商品情報取得
 * 
 * @author higuchi
 */
class Model_Item extends \Model {


    /**
    * 商品IDから商品情報取得
    * 
    * @access public
    * @param integer $itemId
    * @return mixed
    * @author higuchi
    */
    public static function getIdData($itemId) {
        
        try{
            $query = \DB::query(
                'SELECT I.*, S.code 
                FROM items AS I INNER JOIN shops AS S ON S.shop_id = I.shop_id 
                WHERE I.item_id = '.$itemId.' AND I.deleted_at IS NULL'
            );
            $results = $query->execute();
            return $results->current();
        
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
}