<?php
namespace Batch;

/**
 * iOS PUSH通知
 * 
 * @author higuchi
 */
class Model_Ios_Push extends \Model {

    const SEND_COUNT = 999; // 一度の送信数
    
    /**
    * 通知処理
    * 
    * @access public
    * @param array $userData
    * @return bool
    * @author higuchi
    */
    public static function setData($userData) {
    
        // 通知処理
        $sendArray = array_chunk($userData, self::SEND_COUNT);
        foreach ($sendArray as $value) {
            self::pushAlert($value);
            sleep(1);
        }
        
        // ログ出力処理
        \Config::load('batch_option', 'o');
        $pushSet = \Config::get('o.log_set.send_push_list');
        if ($pushSet == 'on') {
            $logList =array();
            foreach ($userData as $value) {
                if (! empty($value['device_token'])) {
                    $logList[] = $value['user_id'];
                }
            }
            $msg = implode(', ', $logList);
            \Batch\Batchlog::write('iOS PUSH USER : '.$msg);
        }
        return true;
    }
    
    
    /**
    * プッシュ通知
    * 
    * @access public
    * @param array $userData
    * @author higuchi
    */
    public static function pushAlert($userData) {
        \Config::load('push', 'p');
        $pushConfig = \Config::get('p.ios');
        
        try{
            // Adjust to your timezone
            date_default_timezone_set('Asia/Tokyo');

            // Report all PHP errors
            error_reporting(-1);

            // Using Autoload all classes are loaded on-demand
            require_once APPPATH.'vendor/ApnsPHP/Autoload.php';
            
            // Instanciate a new ApnsPHP_Push object
            if ($pushConfig['environment_variable'] == 'production') {
                $push = new \ApnsPHP_Push(
                    \ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
                    APPPATH.'vendor/'.$pushConfig['provider_certificate_path']
                );
            } else {
                $push = new \ApnsPHP_Push(
                    \ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
                    APPPATH.'vendor/'.$pushConfig['provider_certificate_path']
                );
            }

            // Set the Root Certificate Autority to verify the Apple remote peer
            $push->setRootCertificationAuthority(APPPATH.'vendor/'.$pushConfig['route_certificate_path']);

            // Connect to the Apple Push Notification Service
            $push->connect();
            
            foreach ($userData as $value) {
                if (! empty($value['device_token'])) {
                
                    // Instantiate a new Message with a single recipient
                    $message = new \ApnsPHP_Message($value['device_token']);

                    // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
                    // over a ApnsPHP_Message object retrieved with the getErrors() message.
                    $message->setCustomIdentifier("Message-Badge-3");

                    // Set badge icon to "3"
                    $message->setBadge(1);

                    // Set a simple welcome text
                    $message->setText($pushConfig['message']);

                    // Play the default sound
                    $message->setSound();

                    // Set a custom property
                    $message->setCustomProperty('acme2', array('bang', 'whiz'));

                    // Set another custom property
                    $message->setCustomProperty('acme3', array('bing', 'bong'));

                    // Set the expiry value to 30 seconds
                    $message->setExpiry(30);

                    // Add the message to the message queue
                    $push->add($message);
                }
            }
            
            // Send all messages in the message queue
            $push->send();

            // Disconnect from the Apple Push Notification Service
            $push->disconnect();

            // Examine the error message container
            $aErrorQueue = $push->getErrors();
            if (!empty($aErrorQueue)) {
                //var_dump($aErrorQueue);
            }
        
        } catch (\Exception $e) {
            \Common\Error::instance()
                ->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
                ->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
                ->logging();
            return false;
        }
        
        return true;
    
    }
}