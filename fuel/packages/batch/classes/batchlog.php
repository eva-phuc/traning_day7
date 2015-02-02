<?php
namespace Batch;

/**
 * ログ
 * 
 * @author higuchi
 */
class Batchlog {
    
    /**
     * ログ書き出し
     * 
     * @access public
     * @param string $input
     * @return mixed
     * @author higuchi
     */
    public static function write($text = '') {
        $fp = fopen(APPPATH."tmp/".date("Ymd")."_batch.log", "a");
        flock($fp, LOCK_EX);
        fwrite($fp,date("H:i:s").' : '.$text."\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}