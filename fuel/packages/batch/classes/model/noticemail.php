<?php
namespace Batch;

/**
 * メール通知
 * 
 * @author higuchi
 */
class Model_Noticemail extends \Model {

    
    /**
    * 通知処理
    * 
    * @param array $userData
    * @access public
    * @return bool
    * @author higuchi
    */
    public static function setData($userData) {
        
        \Config::load('batch_option', 'o');
        $mailSet = \Config::get('o.log_set.send_mail_list');
        $baseUrl = \Config::get('o.mail_link_url');
        $logList =array();
        
        \Package::load('aucfan');
        $member_userinfo = \Aucfan\Member::forge();
        
        foreach ($userData as $value) {
            try {
                // ユーザーメールアドレス取得出来れば送信実行
                $result = $member_userinfo->executeGetUserProfilePlural(array('user_id'=>$value['user_id']));
                if($result === true) {
                    $profile = $member_userinfo::getResponse();
                    if(! empty($profile->user_profile->mail_addr)) {
                        // ブックマーク商品情報取得
                        $items = \Batch\Model_Noticeuser::getBookmarkItem($value['user_id']);
                        // メール送信
                        if ($items !== false && ! empty($items)) {
                            $mailAddr = (string)$profile->user_profile->mail_addr;
                            \Aucfan\Sendmail::forge()->execute_sendmail_by_in_common(
                                array('tmpl_key'=>'notice', 'to'=>$mailAddr, 'items'=>$items, 'baseUrl'=>$baseUrl));
                            // ログ出力用ユーザーリスト
                            if ($mailSet == 'on') {
                                $logList[] = $value['user_id'];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Common\Error::instance()
                    ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                    ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
                    ->logging();
                return false;
            }
        }
        
        // ログ出力処理
        if ($mailSet == 'on') {
            $msg = implode(', ', $logList);
            \Batch\Batchlog::write('MAIL SEND USER : '.$msg);
        }
        
        return true;
    }
}