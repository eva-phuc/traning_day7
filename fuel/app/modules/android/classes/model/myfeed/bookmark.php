<?php
namespace Android;

/**
 * ブックマーク商品情報編集
 * 
 * @author higuchi
 */
class Model_Myfeed_Bookmark extends \Model {

    /**
    * ブックマークのアラート情報更新
    * 
    * @access public
    * @param array $inputParams
    * @param array $userId
    * @return bool
    * @author higuchi
    */
    public function updateAlart($inputParams, $userId) {
        
        try{
            $query = \DB::query('UPDATE bookmarks 
                SET alert = :alert, updated_at = NOW() 
                WHERE bookmark_id = :bookmark_id AND user_id = '.$userId);
            $bind = array('alert' => $inputParams['alert'],
                'bookmark_id' => $inputParams['bookmark_id']);
            
            $query->parameters($bind);
            $result = $query->execute();
            if($result == 1) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
    
    
    /**
    * ブックマーク削除or戻す
    * 
    * @access public
    * @param array $inputParams
    * @param array $userId
    * @return bool
    * @author higuchi
    */
    public function deleteStatus($inputParams, $userId) {
       
       if ($inputParams['delete_flag'] == 'on') {
           $setDalete = 'NOW()';
       } else {
           $setDalete = 'NULL';
       }
       try{
            $cntQuery = \DB::query('UPDATE bookmarks SET updated_at = NOW(), deleted_at ='.$setDalete.'  WHERE bookmark_id = :bookmark_id AND user_id = '.$userId);
            $cntQuery->bind('bookmark_id', $inputParams['bookmark_id']);
            $result = $cntQuery->execute();
            
            if($result == 1) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] DB ')
                ->logging();
            return false;
        }
    }
}