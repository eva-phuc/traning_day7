<?php
namespace Ios;

/**
 * 商品情報閲覧
 * 
 * @author higuchi
 */
class Model_Topfeed_Home extends \Model {

    /**
    * 商品情報取得
    * 
    * @access public
    * @param array $searchParam
    * @return mixed
    * @author higuchi
    */
    public function getData($searchParam) {
        \Config::load('haqconf', 'cn');
        $parPage = \Config::get('cn.default_search.par_page');
        
        // ページネーション表示範囲
        $limitStart = $parPage * ($searchParam['page'] - 1);
        $limit = ' LIMIT '.$limitStart.', '.$parPage;
        
        // ソート・絞込み
        $order = '';
        $where = '';
        switch ($searchParam['sort']) {
            case 'popular':
                $order = ' ORDER BY bookmark_count desc, created_at desc, item_id desc';
                break;
            
            case 'sale':
                $where = " AND sale = 'yes'";
                $order = ' ORDER BY bookmark_count desc, created_at desc, item_id desc';
                break;
            
            case 'ten_thousand':
                $where = " AND price <= 10000";
                $order = ' ORDER BY bookmark_count desc, created_at desc, item_id desc';
                break;
            
            case 'new':
            default:
                $order = ' ORDER BY created_at desc, item_id desc';
        }
        
        // コンフィグからの商品非表示対応
        \Config::load('itemfilter.php', 'filter');
        $filterItem = \Config::get('filter.hidden_item_id');
        if (! empty($filterItem)) {
            $itemstr = implode(',', $filterItem);
            $where .= ' AND I.item_id NOT IN('.$itemstr.')';
        }
        
        // DBアクセス
        try{
            $query = \DB::query(
                'SELECT I.*, S.name, S.code 
                FROM items AS I INNER JOIN shops AS S ON S.shop_id = I.shop_id 
                WHERE I.deleted_at IS NULL '
                .$where.$order.$limit
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