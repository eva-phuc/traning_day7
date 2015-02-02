<?php
namespace Batch;

/**
 * 通知機能
 * 
 * @author higuchi
 */
class Notice {
    
    /**
     * 新価格の通知とブックマークの更新
     * 
     * @access public
     * @param string $input
     * @return mixed
     * @author higuchi
     */
    public static function execute($input=null) {
        
        \Config::load('batch_option', 'o');
        $logSet = \Config::get('o.log_set');
        \Config::load('notice', 'n');
        $inputCodes = \Config::get('n.input');
        
        $resultIos = true;
        $resultAndroid = true;
        $resultMail = true;
        $result = true;
        
        
        // 通知対象ユーザーを取得
        $userData = \Batch\Model_Noticeuser::getData();
        if (empty($userData)) {
            return true;
        }
        
        // 通知別にユーザー仕分け
        $noticeUser = self::alertUser($userData);
        
        // iosプッシュ
        if (isset($noticeUser['ios']) && ($input === null || $input ==  $inputCodes['ios'])) {
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('iOS PUSH START');
            }
            $resultIos = \Batch\Model_Ios_Push::setData($noticeUser['ios']);
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('iOS PUSH END : push user count '.count($noticeUser['ios']));
            }
        }
        
        // Androidプッシュ
        if (isset($noticeUser['android']) && ($input === null || $input ==  $inputCodes['android'])) {
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('Android PUSH START');
            }
            $resultAndroid = \Batch\Model_Android_Push::setData($noticeUser['android']);
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('Android PUSH END : push user count '.count($noticeUser['android']));
            }
        }
        
        // メール通知
        if (isset($noticeUser['mail']) && ($input === null || $input ==  $inputCodes['mail'])) {
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('MAIL SEND START');
            }
            $resultMail = \Batch\Model_Noticemail::setData($noticeUser['mail']);
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('MAIL SEND END : mail send user count '.count($noticeUser['mail']));
            }
        }
        
        // ブックマークへ通知済情報更新
        if ($resultIos == true && $resultAndroid == true && $resultMail == true 
             && ($input === null || $input ==  $inputCodes['update'])) {
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('USER DATA UPDATE START');
            }
            $result = \Batch\Model_Noticeuser::updateNotice();
            if ($logSet['notice_execute'] == 'on') {
                \Batch\Batchlog::write('USER DATA UPDATE END');
            }
        }
        
        
        return $result;
    }
    
    
    
    /**
     * 通知種類ごとにユーザー選別
     * 
     * @access public
     * @param array $userData
     * @return array
     * @author higuchi
     */
    private static function alertUser($userData) {
        
        $noticeUser = array();
        
        foreach ($userData as $key => $value) {
            // プッシュ通知判定
            if ($value['os_type'] == 'ios') {
                $noticeUser['ios'][] = $value;
            } elseif ($value['os_type'] == 'android') {
                $noticeUser['android'][] = $value;
            }
            
            // メール送信通知
            if (empty($value['is_mail_alert_deny']) || $value['is_mail_alert_deny'] != 1) {
                $noticeUser['mail'][] = $value;
            }
        }
        return $noticeUser;
    }
    
}