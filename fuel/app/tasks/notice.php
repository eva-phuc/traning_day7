<?php
namespace Fuel\Tasks;

/**
 * 通知
 * 
 * @author higuchi
 */
class Notice {
    
    /**
     * 通知機能の実行
     * 
     * @access public
     * @param string $input
     * @return null
     * @author higuchi
     */
    public static function run($input=null) {
    
        \Package::load('batch');
        \Config::load('notice', 'n');
        $inputCodes = \Config::get('n.input');
        \Config::load('batch_option', 'o');
        $logSet = \Config::get('o.log_set.batch_execute');
        
        // 実行コマンドエラー
        if ($input !== null && ! in_array($input, $inputCodes)) {
            echo "input code ERROR \n";
            return;
        }
        
        // ログ
        if ($logSet == 'on') {
            \Batch\Batchlog::write('NOTICE BATCH START');
        }
        
        // 通知実行
        \Batch\Notice::execute($input);
        
        // ログ
        if ($logSet == 'on') {
            \Batch\Batchlog::write('NOTICE BATCH END');
        }
        
        return;
    }


}