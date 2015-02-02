<?php
namespace Common;

class Error {
	private static $_instance = null;
	private static $_body    = null;
	private static $_subject = null;
	private static $_text    = null;
	private static $_path    = null;
	private static $_level   = null;
	private static $_level_list = array(
					 \Fuel::L_NONE,
					 \Fuel::L_ERROR,
					 \Fuel::L_WARNING,
					 \Fuel::L_DEBUG,
					 \Fuel::L_INFO,
					 \Fuel::L_ALL);
	private static $_module = null;

	public static function forge()
	{
		if(true === empty(static::$_instance)) {
			static::$_instance = new Error();
		}
		\Config::load('common','common');
		static::$_module = isset(\Request::main()->module) ? \Request::main()->module : '';

		return static::$_instance;
	}

	final private function __construct() {}

	public static function instance()
	{
		if(is_null(static::$_instance)) static::forge();
		return static::$_instance;
	}

	public static function set_email($body,$subject)
	{
		if(isset($_SERVER['HTTPS'])and$_SERVER['HTTPS']=='on'){
			$protocol='https://';
		}else{
			$protocol='http://';
		}
		static::$_body = isset($_SERVER['REQUEST_URI']) ? $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n" : "REQUEST_URI N/A\n";
		static::$_body .= $body ?: \Config::get('common.email.body');

		static::$_subject = '[alermo] ';
		static::$_subject .= static::$_module ? static::$_module.': ' : '';
		static::$_subject .= $subject ?: \Config::get('common.email.subject');
		return static::$_instance;
	}

	public static function set_log($text , $path = '', $level = \Fuel::L_WARNING)
	{
		static::$_text = static::$_module ? static::$_module.': ' : '';
		static::$_text .= $text ?: \Config::get('common.email.text');
		static::$_path = file_exists($path) ? $path : '';
		static::$_level = $level ?: \Fuel::L_DEBUG;
		return static::$_instance;
	}

	public static function logging()
	{
		$threshold = \Config::get('log_threshold', \Fuel::L_DEBUG);
		$level = in_array(static::$_level, static::$_level_list) 
					? static::$_level : $threshold;
		if(!empty(static::$_text)){
			if($level <= $threshold) {
				$default_log_path = \Config::get('log_path');
				if(static::$_path){
					\Config::set('log_path', static::$_path);
				}elseif(static::$_module){
					\Config::set('log_path', APPPATH.'modules/'.static::$_module.'/logs/');
				}
				\Log::write($level, static::$_text);
				\Config::set('log_path', $default_log_path);
			}
		}
		
		if(!empty(static::$_body) && !empty(static::$_subject)){
			\Package::load('email');
			$email = \Email::forge('jis');
			$email->from(\Config::get('common.email.from'), '');
			$email->to(\Config::get('common.email.to'));
			$email->subject(static::$_subject);
			$email->body(static::$_body);
			try {
				$email->send();
			}
			catch (\EmailValidationFailedException $e) {
				\Log::write(static::$_level, 'メール送信できません。形式に問題があります。');
			}
			catch (\EmailSendingFailedException $e) {
				\Log::write(static::$_level, 'メール送信に失敗しました。');
			}
		}
	}

	public static function __callStatic($method, $args='')
	{
		if (array_key_exists($method, static::$_instance))
		{
			return static::$method($method, $args);
		}
		static::logging();
	}
}
