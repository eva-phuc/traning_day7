<?php
namespace Ios;

/**
 * ブックマーク商品情報閲覧
 * 
 * @author higuchi
 */
class Model_Myfeed_Home extends \Model {

    /**
    * 商品情報取得
    * 
    * @access public
    * @param array $searchParam
    * @param array $userId
    * @return mixed
    * @author higuchi
    */
    public function getData($searchParam, $userId) {
        \Config::load('haqconf', 'cn');
        $parPage = \Config::get('cn.default_search.par_page');
        
        // ページネーション表示範囲
        $limitStart = $parPage * ($searchParam['page'] - 1);
        $limit = ' LIMIT '.$limitStart.', '.$parPage;
        
        // ソート・絞込み
        $order = '';
        $where = '';
        switch ($searchParam['sort']) {
            case 'discount':
                $where = ' AND I.price < B.price';
                $order = ' order by B.created_at desc';
                break;
            
            case 'ten_thousand':
                $where = " AND I.price <= 10000";
                $order = ' order by B.created_at desc';
                break;
            
            case 'new':
            default:
                $order = ' order by B.created_at desc';
        }
        
        // DBアクセス
        try{
            $query = \DB::query(
                'SELECT I.*, S.name, S.code, B.bookmark_id, B.alert, B.price as bookmark_price, IO.option_id, IO.option_values, IO.stock AS option_stock, IO.img_url AS option_img_url 
                FROM bookmarks AS B 
                INNER JOIN items AS I ON B.item_id = I.item_id 
                LEFT JOIN item_options AS IO ON IO.item_id = B.item_id
                 AND IO.option_id = B.option_id 
                INNER JOIN shops AS S ON S.shop_id = I.shop_id 
                WHERE B.deleted_at IS NULL AND B.user_id = '.$userId.$where.$order.$limit
            );
            $results = $query->execute();
            $itemData = $results->as_array();
            // アフィリタグの追加
            \Package::load('iteminfo');
            $resultItems = \Iteminfo\Affili::url_replace($itemData);
            
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