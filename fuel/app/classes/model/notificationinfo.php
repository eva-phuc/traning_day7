<?php

namespace Model;

class Notificationinfo extends \Model
{
    protected static $_table_name  = 'notification_info';
    protected static $_primary_key = 'user_id';
    protected static $_rules    = array('user_id' => 'valid_string[numeric]', 'mail_alert' => 'valid_string[numeric]|numeric_between[0,1]');
    protected static $_labels   = array('user_id' => 'user_id', 'mail_alert' => 'メール通知設定'); 
    protected static $_defaults = array('mail_alert' => 0,'deleted_at' => null);
    protected static $_params   = array();
    protected static $_instance = null;

    private   static $_notification_info_error = array();

    public static function forge($data=array())
    {
        if(true === empty(static::$_instance)) {
            static::$_instance = new Notificationinfo();
        }
        \Config::load('ios::member','member_cnf',true);

        return static::$_instance->set($data);
    }
    final private function __construct() {}

    public static function instance()
    {
        if(is_null(static::$_instance)) static::forge();

        return static::$_instance;
    }

    public static function set($data=array())
    {
        static::$_params = $data;
        return static::$_instance;
    }

    public function findMailalertByPK($pkey)
    {
        $result = \DB::select('is_mail_alert_deny')->from(static::$_table_name)->where(static::$_primary_key,$pkey)->execute();
        if(! count($result)) return 0;

        $res = $result->as_array();
        return (int)$res[0];
    }

    public function findDeviceInfoByPK($pkey)
    {
        $result = \DB::select('os_type','device_token')->from(static::$_table_name)->where(static::$_primary_key,$pkey)->execute();
        if(! count($result)) return 0;

        $res = $result->as_array();
        return (int)$res[0]['is_mail_alert_deny'];
    }

    /**
     * インサート及び更新
     */
    public function saveMailInfo($need_validate=true)
    {
        if(true === $need_validate) {
            $validate = \Validation::forge('denylist');

            foreach(static::$_rules as $validate_key => $rule) {
                $validate->add_field($validate_key, static::$_labels[$validate_key], $rule);
            }

            if(! $validate->run(static::$_params)) {
                foreach(array_keys(static::$_rules) as $validate_key) {
                    (false !== $validate->error($validate_key))
                        and static::$_notification_info_error[$validate_key] = $validate->error($validate_key)->get_message(\Config::get('member_cnf.error_messages.invalid_'.(string)$validate->error($validate_key)->get_message(':field')));
                }
                return false;
            }
        }

        $query = 'INSERT INTO '.static::$_table_name.' (user_id,is_mail_alert_deny) VALUES (:user_id,:mail_alert) ON DUPLICATE KEY UPDATE is_mail_alert_deny=:mail_alert ,updated_at=:updated_at ';

        try {
            $return = \DB::query($query)->parameters(array('user_id'=>static::$_params['user_id'],'mail_alert'=>static::$_params['mail_alert'],'updated_at'=>'CURRENT_TIMESTAMP'))->execute();

            if(false === $return)
                static::$_notification_info_error[] = \Config::get('member_cnf.error_messages.invalid_mail_alert');
        }
        catch(\Exception $e) {
			\Common\Error::instance()
				->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
				->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
				->logging();
            
            static::$_notification_info_error[] = \Config::get('member_cnf.error_messages.invalid_mail_alert');
            return false;
        }

        return $return;
    }
   
    public function saveDevicetokenInfo()
    {

        $query = 'INSERT INTO '.static::$_table_name.' (user_id,os_type,device_token) VALUES (:user_id,:os_type,:device_token) ON DUPLICATE KEY UPDATE os_type=:os_type, device_token=:device_token ,updated_at=:updated_at ';

        try {
            $return = \DB::query($query)->parameters(array('user_id'=>static::$_params['user_id'],'os_type'=>static::$_params['os_type'],'device_token'=>static::$_params['device_token'],'updated_at'=>'CURRENT_TIMESTAMP'))->execute();

            if(false === $return)
                static::$_notification_info_error[] = 'Invalid Token save!';
        }
        catch(\Exception $e) {
			\Common\Error::instance()
				->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
				->logging();
            
            static::$_notification_info_error[] = 'Invalid Token save!';
            return false;
        }

        return $return;
    }

    public function getNotificationinfoError()
    {
        return static::$_notification_info_error;
    }
}
