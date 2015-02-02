<?php

namespace Common;


class Api
{

	public    static $userinfo;
	protected static $_instance       = null;

	protected static $_thread_mode  ;
	protected static $_options      ;
	protected static $_headers      ;
	protected static $_url          ;
	protected static $_params       ;
	protected static $_method       ;
	protected static $_response_mode;
	protected static $_response_body;

	protected static $_errors       ;

	public static function forge($url=null)
	{
		if(true === empty(static::$_instance)) {
			static::$_instance = new Api();
		}
		\Config::load('api','api');
		static::init();

		if($url) static::set_url($url);

		return static::$_instance;
	}

	/**
	 * Prevent instantiation
	 */
	final private function __construct() {}

	public static function instance($force_init=true)
	{
		if(is_null(static::$_instance)) static::forge();

		return static::$_instance;
	}

	public static function init()
	{
		static::$_thread_mode    = 'single';
		static::$_options        = array();
		static::$_headers        = array();
		static::$_url            = null;
		static::$_params         = array();
		static::$_method         = 'get';
		static::$_response_mode  = 'php';
		static::$_response_body  = null;

		static::$_errors         = array();

		return static::$_instance;
	}

	public static function set_thread_mode($mode='single')
	{
		static::$_thread_mode = $mode;
		return static::$_instance;
	}

	public static function set_options($options = array())
	{
		if(true === (bool)$options) static::$_options = $options;
		return static::$_instance;
	}

	public static function set_headers($headers = array())
	{
		if(true === (bool)$headers) static::$_headers = $headers;
		return static::$_instance;
	}

	public static function set_response_mode($response_mode='php')
	{
		$acceptable = \Config::get('api.acceptable_response_mode');
		if(is_null($response_mode) || empty($acceptable[$response_mode])) return static::$_instance;

		static::$_response_mode = 'none' == $response_mode ? null : $response_mode;

		return static::$_instance;
	}

	public static function set_method($method='get')
	{
		$methods = \Config::get('api.allowed_method_type');
		if(is_null($method) || empty($methods[strtolower($method)])) return static::$_instance;

		static::$_method = $method;

		return static::$_instance;
	}

	public static function set_url($url=null)
	{
		if(is_null($url)) return static::$_instance;

		if(is_array($url)) {
			if(1 < count($url))
				static::set_thread_mode('multi');
			else
				$url = array_shift($url);
		}

		static::$_url = $url;

		return static::$_instance;
	}

	public static function set_parameter($params = array())
	{
		static::$_params = $params;
		return static::$_instance;
	}

	public static function execute($token=null)
	{
		if(false === (bool)static::$_url) {
			static::$_errors['url_error'] = 'no url';
			return false;
		}

		try {
			if('multi' == static::$_thread_mode)
				static::$_response_body = static::_execute_multi($token);
			else
				static::$_response_body = static::_execute($token);

			if(false === static::$_response_body) return false;
		}
		catch(\HTTPException $e) {
			\Common\Error::instance()
				->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
				->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
				->logging();

			if(!is_null($token) || true === array_key_exists('Authorization', static::$_headers)) {
				$error_array = static::get_serged_response($e->getMessage());
				static::$_errors['token_error'] = $error_array['Error']['Messages'];
			} else {
				static::$_errors['api_error'] = $e->getMessage();
			}
			return false;
		}
		return true;
	}

	private static function _execute($token=null)
	{
		$url  = is_array(static::$_url) ? array_shift(static::$_url) : static::$_url;
		$curl = \Request::forge($url, 'curl');

		if(true === (bool)static::$_headers) {
			$headers = (!is_array(static::$_headers)) ? (array)static::$_headers : static::$_headers;
			array_walk($headers, function($header_value,$header_name) use($curl){ $curl->set_header($header_name, $header_value); });
		}
		if(!is_null($token)) {
			$curl->set_header('Authorization','Bearer '.$token);
		}

		$curl->set_method(static::$_method);
		$curl->set_params(static::$_params);

		if(true === (bool)static::$_options) {
			$options = (!is_array(static::$_options)) ? (array)static::$_options : static::$_options;
			$curl->set_options($options);
		}

		$curl->execute();
		$response = $curl->response();

		if(200 != $response->status || !$response->body) throw new \HttpException('response is invalid.');

		return $response;
	}

	private static function _execute_multi($token=null)
	{
		$curl_resources     = array();
		$curl_multi_handler = curl_multi_init();

		$urls    = is_array(static::$_url) ? static::$_url : (array)static::$_url;
		$options = static::$_options;
		$headers = static::$_headers;
		$params  = static::$_params;
		$method  = static::$_method;

		array_walk($urls, function($url,$key) use($curl_resources,$curl_multi_handler,$options,$headers,$method)
		{
			$curl_resources[$key] = curl_init();

			//--- オプション設定
			if(true === (bool)$options) {
				$opt = (isset($options[$key]) || true === (bool)$options[$key]) ? $options[$key] : $options;

				foreach($opt as $opt_key => $opt_val) {
					$curl_opt_key = strtoupper($opt_key);
					(0 !== strpos($curl_opt_key,'CURLOPT_')) and $curl_opt_key = 'CURLOPT_'.$curl_opt_key;

					if(false === defined($curl_opt_key)) continue;

					curl_setopt($curl_resources[$key], $curl_opt_key, $opt_val);
				}
			}

			//--- ヘッダ設定
			if(true === (bool)$headers) {
				$hd = (isset($headers[$key]) || true === (bool)$headers[$key]) ? $headers[$key] : $headers;

				foreach($hd as $hd_key => $hd_val) {
					$header_array[] = $hd_key.': '.$hd_val;
				}
				curl_setopt($curl_resources[$key], CURLOPT_HTTPHEADER, $header_array);
			}

			//--- メソッド
			$mthd = true === (is_array($method) && !empty($method[$key])) ? $method[$key] : $method;
			($method) and curl_setopt($curl_resources[$key], CURLOPT_CUSTOMREQUEST, $method);

			//--- パラメータ
			if(true === (bool)$params) {
				$prm = (isset($params[$key]) || true === (bool)$params[$key]) ? $params[$key] : $params;

				if('post' == strtolower($mthd))
					curl_setopt($curl_resources[$key], CURLOPT_POSTFIELDS, $prm);
				else {
					$url .= '?';
					foreach($prm as $prm_key => $prm_val)
						$tmp_prm[] = $prm_key.'='.$prm_val;

					$url .= implode('&',$tmp_prm);
				}	
			}

			//--- URL設定
			curl_setopt($curl_resources[$key], CURLOPT_URL, $url);

			curl_multi_add_handle($curl_multi_handler, $curl_resources[$key]);
		});


   		//--- ハンドルを実行
		$run = null;
		do { curl_multi_exec($curl_multi_handler, $run); } while ($run > 0);
	
		while ($run && $mrc == CURLM_OK) {
			if (curl_multi_select($curl_multi_handler) != -1) {
				do {
					$mrc = curl_multi_exec($curl_multi_handler, $run);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}

		//--- 結果取得
		$responses = array();
		foreach($curl_resources as $ch) {
			// HTTPステータスコードを取得する
			$status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			if("" != ($error = curl_error($ch)) || 0 == $status) {
				static::$_errors['curl_multi_error'][] = 'status:'.$status.' message:'.$error;
				return false;
			}

			$responses[] = curl_multi_getcontent($ch);
		}

		//--- ハンドル閉じる
		array_walk($curl_resources,function($curl) use($curl_multi_handler){ curl_multi_remove_handle($curl_multi_handler, $curl); });
		curl_multi_close($curl_multi_handler);

		return $responses;
	}

	public static function get_response()
	{
		return static::$_response_body;
	}

	//--- TODO:もうちょっと熟考します！
	public static function get_serged_response($body=null)
	{
		return static::$_response_body;
/*
		$body = is_null($body) ? static::$_response_body : $body;
		if(is_null($body)) return $body;

		switch(static::$_response_mode) {
			case 'json':
				$response = json_decode($body, true);
				break;
			case 'xml':
				$response = new \SimpleXMLElement($body);
				break;
			default:
				$response = unserialize($body);
				break;
		}

		return $response;
*/
	}

	public static function get_error($key=null)
	{
		if(!is_null($key) && isset(static::$_errors[$key])) 
			return static::$_errors[$key];
		else
			return static::$_errors;
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

