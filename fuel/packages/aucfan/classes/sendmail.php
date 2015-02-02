<?php
namespace Aucfan;

class Sendmail
{
	protected static $_instance = null;
	private   static $_response = null;
	private   static $_error    = array();

	private   static $_url;
	private   static $_method = 'post';
	private   static $_params = array();

	private   static $_response_success = 'success';

	public static function forge()
	{
		if(true === empty(static::$_instance)) {
			static::$_instance = new Sendmail();
		}
		\Config::load('sendmail','sendm',true);

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

	public static function get_error()
	{
		return static::$_error;
	}
	public static function get_response()
	{
		return static::$_response;
	}

	public static function execute_sendmail_by_id($params=array())
	{
		$return = true;
		static::$_url = \Config::get('sendm.url.sendmail');

        $parameter['to']          = isset($params['to']) ? $params['to'] : '';
        $parameter['template']    = isset($params['template']) ? $params['template'] : '';

		foreach(\Config::get('sendm.defaults.template', array()) as $def_key => $def_val) 
			empty($params[$def_key]) and $params[$def_key] = $def_val;


		$validate = \Validation::forge('sendmail');
		$validate_conf = \Config::get('sendm.validation.template', array());
		foreach(array_unique($validate_conf['check_keys']) as $validate_key) {
			$validation_rules = array();
			if(isset($validate_conf['required'][$validate_key])) $validation_rules[] = 'required';
			if(isset($validate_conf['numeric'][$validate_key]))  $validation_rules[] = 'valid_string[numeric]';

			$validate->add_field($validate_key, $validate_key, implode('|',$validation_rules));
		}

		if($validate->run($params)) {
			static::$_params = $params;

			$return = static::_execute();
			if(false === $return) {
				static::$_error[] = \Config::get('sendm.error_messages.invalid_system') .':'. (string)static::$_response->error_message;
			}
		}
		else {
			foreach($validate_conf as $validate_key) {
				(false !== $validate->error($validate_key)) 
					and static::$_error[] = $validate->error($validate_key)->get_message(\Config::get('sendm.error_messages.invalid_'.(string)$validate->error($validate_key)->get_message(':rule')));
			}
			$return = false;
		}

		return $return;
	}

	public static function execute_sendmail_by_inner_template($params=array())
	{
		$return = true;
		static::$_url = \Config::get('sendm.url.sendmail');


		foreach(\Config::get('sendm.defaults.inner_template', array()) as $def_key => $def_val) 
			empty($params[$def_key]) and $params[$def_key] = $def_val;


		$validate = \Validation::forge('sendmail_inner_template');
		$validate_conf = \Config::get('sendm.validation.inner_template', array());
		foreach($validate_conf['check_keys'] as $validate_key) {
			$validation_rules = array();
			if(isset($validate_conf['required'][$validate_key])) $validation_rules[] = 'required';
			if(isset($validate_conf['mailaddr'][$validate_key]))  $validation_rules[] = 'valid_email';

			$validate->add_field($validate_key, $validate_key, implode('|',$validation_rules));
		}


		if($validate->run($params)) {
			$template_script = \Config::get('sendm.template_files.'.$params['tmpl_key'],null);
			if(!is_null($template_script)) {
				$params['body'] = render(PKGPATH.'aucfan/mail_templates/'.$template_script, $params);
			}

			static::$_params = $params;

			$return = static::_execute();
			if(false === $return) {
				static::$_error[] = \Config::get('sendm.error_messages.invalid_system') .':'. (string)static::$_response->error_message;
			}
		}
		else {
			foreach($validate_conf as $validate_key) {
				(false !== $validate->error($validate_key)) 
					and static::$_error[] = $validate->error($validate_key)->get_message(\Config::get('sendm.error_messages.invalid_'.(string)$validate->error($validate_key)->get_message(':rule')));
			}
			$return = false;
		}

		return $return;
	}


    /**
    * 内部処理からのメール送信用(汎用)
    * 
    * @access public
    * @param array $params
    * @return array
    * @author higuchi
    */
	public static function execute_sendmail_by_in_common($params=array()) {
        $return = true;
        static::$_url = \Config::get('sendm.url.sendmail');
        
        foreach(\Config::get('sendm.defaults.'.$params['tmpl_key'], array()) as $def_key => $def_val) 
            empty($params[$def_key]) and $params[$def_key] = $def_val;
        
        $template_script = \Config::get('sendm.template_files.'.$params['tmpl_key'],null);
        if(!is_null($template_script)) {
            $params['body'] = render(PKGPATH.'aucfan/mail_templates/'.$template_script, $params, false);
        }
        
        static::$_params = $params;
        
        $return = static::_execute();
        if(false === $return) {
            static::$_error[] = \Config::get('sendm.error_messages.invalid_system') .':'. (string)static::$_response->error_message;
        }
        
        return $return;
	}


	private static function _execute()
	{

		try {

			\Package::load('common');
			$api = \Common\Api::forge(static::$_url)
					->set_method(static::$_method)
					->set_parameter(static::$_params);
			$result = $api->execute();
			if(false === $result) throw new \Exception(implode("\n",$api->get_error()));

			$response = new \SimpleXMLElement($api->get_response());

			static::$_response = $response;
			if(static::$_response_success != $response->status) 
				return false;
		}
		catch(\Exception $e) {
			\Common\Error::instance()
				->set_log($e->getFile().' '.$e->getLine().' : '.$e->getMessage())
				->set_email($e->getFile().' '.$e->getLine().' : '.$e->getMessage(),'[ERROR] API ')
				->logging();

            return false;
		}

		return true;
	}
}
