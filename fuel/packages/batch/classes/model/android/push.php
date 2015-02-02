<?php
namespace Batch;

/**
 * android PUSH通知
 * 
 * @author higuchi
 */
class Model_Android_Push extends \Model {

    const SEND_COUNT = 999; // 一度の送信数
    
    /**
    * 通知処理
    * 
    * @param array $userData
    * @access public
    * @return bool
    * @author higuchi
    */
    public static function setData($userData) {
        
        $sendArray = array_chunk($userData, self::SEND_COUNT);
        
        foreach ($sendArray as $value) {
            // TODO : Android用PUSH通知の実装
        }
        
        return true;
    }
}
    