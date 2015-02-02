<?php
namespace Aucfan;

class AuthException extends \HttpException {
	public function response()
	{
		// header("Location: http://www.aucuniv.com/");exit;
	}
}

class Auth {

	protected static $_instance = null;
	private   static $_disfa    = null;
	private   static $_device   = 'pc';
	private   static $_response = null;
	private   static $_need_search_count = false;

	const STATUS_AVAILABLE   = 'AVAILABLE';		//有効
	const STATUS_STOP        = 'STOP';			//休会中
	const STATUS_UNAVAILABLE = 'UNAVAILABLE';	//休会中(強制停止)

	const OPTION_AVAILABLE   = 'ON';			//有効
	const OPTION_UNAVAILABLE = 'OFF';			//無効(休会中)

	const SCHOOL_FULL_CONSULTING = 'SCHOOL_FULL_CONSULTING';	// フルコンサルティング
	const SCHOOL_PLATINUM        = 'SCHOOL_PLATINUM';			// プラチナ
	const SCHOOL_GOLD            = 'SCHOOL_GOLD';				// ゴールド

	public static function forge($_disfa=null,$device='pc')
	{
		if(true === empty(static::$_instance)) {
			static::$_instance = new Auth();
		}
		\Config::load('auth','aucfanauth');
		static::set_disfa($_disfa);
		static::set_device($device);

		return static::$_instance;
	}

	/**
	 * Prevent instantiation
	 */
	final private function __construct() {}

	public static function instance()
	{
		if(is_null(static::$_instance)) static::forge();

		return static::$_instance;
	}
	public static function set_disfa($_disfa=null)
	{
		if(is_null($_disfa)) $_disfa = \Input::cookie('_disfa');

		static::$_disfa = $_disfa;
	}
	public static function set_device($device='pc')
	{
		static::$_device = $device;
	}
	public static function set_need_search_count($flg=false)
	{
		static::$_need_search_count = (bool)$flg;
	}
	private static function _check_response($force=true)
	{
		if(!static::$_response) {
			if(false === $force) return false;

			return static::check_aucfan_session();
		}

		return true;
	}

	public static function check_aucfan_session($_disfa=null)
	{
		try {
			if($_disfa) static::set_disfa($_disfa);

			// Cookie に値がなかった場合は、セッションAPI をコールしない
			if ( empty(static::$_disfa) ) {
				static::$_response = null;
				return;
			}

			$api_host = \Config::get('aucfanauth.session_api_host');
			$api_path = \Config::get('aucfanauth.session_api_path.'.static::$_device, \Config::get('aucfanauth.session_api_path.pc'));
			$api_url  = 'http://'.$api_host.'/'.$api_path;

			$user_remote_addr = \Input::ip();
			!is_null(\Config::get('aucfanauth.override_remote_addr_emulate',null)) and $user_remote_addr = \Config::get('aucfanauth.override_remote_addr_emulate');
			$systemid = \Config::get('aucfanauth.systemid.'.static::$_device, \Config::get('aucfanauth.systemid.pc'));

			$params = array(
					'systemid'=>$systemid,
					'user_remote_addr'=>$user_remote_addr,
					'_disfa'=>static::$_disfa,
					'search_count'=>intval(static::$_need_search_count),
				       );

			\Package::load('common');
			$api = \Common\Api::forge($api_url)
					->set_parameter($params);
			$result = $api->execute();
			if(false === $result) throw new \Exception(implode("\n",$api->get_error()));

			static::$_response = new \SimpleXMLElement($api->get_response());
		}
		catch(\Exception $e) {
			\Common\Error::instance()
				->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
				->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
				->logging();

			//\Response::redirect('http://www.aucuniv.com/');exit;
		}

		return true;
	}

	public static function is_login()
	{
		static::_check_response();
		if ( isset(static::$_response->session_check_result) ) {
			return (bool)('success' == static::$_response->session_check_result);
		} else {
			return false;
		}
	}
	
	public static function getPremiumPaymentMethod ()
	{
		return (string)static::$_response->user_info->premium_payment_method;
	}


	public static function is_service_available($key='premium')
	{
		if(false === static::is_login()) return false;

		$services = \Config::get('aucfanauth.services');
		if(false === isset($services[$key])) $key = 'premium';

		$check_node = $key.'_status';
		return (bool)('AVAILABLE' == (string)static::$_response->user_info->{$check_node});
	}
	
	
	public static function get_aucuniv_status()
	{
		if(false === static::is_login()) return false;
		
		// オークション大学のステータスをチェック
		$status = '';
		switch ((string)static::$_response->user_info->aucuniv_status) {
			case self::STATUS_AVAILABLE:
				$status = self::STATUS_AVAILABLE;
				break;
			
			case self::STATUS_STOP:
				$status = self::STATUS_STOP;
				break;
			
			case self::STATUS_UNAVAILABLE:
				$status = self::STATUS_UNAVAILABLE;
				break;
		}
		
		return $status;
	}
	
	

	public static function is_option_available($key='')
	{
		if(false === static::is_service_available('premium')) return false;

		$options = \Config::get('aucfanauth.premium_options');
		if(!$key || false === isset($options[$key])) return false;

		$check_node = 'premium_option_'.$key;
		return (bool)('ON' == (string)static::$_response->user_info->premium_option_status->{$check_node});
	}

	public static function get_user_info($key='')
	{
		if(false === static::is_login()) return false;

		if(!$key) {
			return static::$_response->user_info;
		}
		elseif(is_array($key)) {
			$return = array();
			foreach($key as $node_name) {
				(true === isset(static::$_response->user_info->{$node_name})) and $return[] = (string)static::$_response->user_info->{$node_name};
				if(0 == strpos('premium_option',$node_name)) {
					(true === isset(static::$_response->user_info->premium_option_status->{$node_name}))
						and $return[] = (string)static::$_response->user_info->premium_option_status->{$node_name};
				}
			}
			return $return;
		}
		else {
			if(true === isset(static::$_response->user_info->{$key})) return (string)static::$_response->user_info->{$key};
			if(0 == strpos('premium_option',$key)) {
				if(true === isset(static::$_response->user_info->premium_option_status->{$key}))
					return (string)static::$_response->user_info->premium_option_status->{$key};
			}
		}

		return '';
	}

	public static function get_status_name()
	{
		//if(false === static::is_login()) return false;
		$status_name = array('ja'=>'', 'en'=>'');
		$status = static::$_response->user_info->premium_status;
		switch($status){
			case self::STATUS_AVAILABLE:
				$status_name['ja'] = 'プレミアム会員';
				$status_name['en'] = 'premium';
				break;
			case self::STATUS_STOP:
			case self::STATUS_UNAVAILABLE:
				$status_name['ja'] = '休会中';
				$status_name['en'] = 'stop';
				break;
			default:
				$status_name['ja'] = '一般会員';
				$status_name['en'] = 'general';
				break;
		}

		return $status_name;
	}

	public static function __callStatic($method, $args='')
	{
                if (array_key_exists($method, static::$_instance))
                {
                        return static::$method($method, $args);
                }

                throw new \BadMethodCallException('Invalid method: '.get_called_class().'::'.$method);
    }
}
