<?php

namespace Uploader;

class ImageException extends \FuelException {
	public function normal_redirect()
	{
		header("Location: http://www.aucuniv.com/");exit;
	}
}

class Image {

	protected static $_instance = null;

	public static function forge()
	{
		if(true === empty(static::$_instance)) {
			static::$_instance = new Image();
		}
		\Config::load('image','upload_image');

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

	public static function __callStatic($method, $args='')
	{
                if (array_key_exists($method, static::$_instance))
                {
                        return static::$method($method, $args);
                }

                throw new \BadMethodCallException('Invalid method: '.get_called_class().'::'.$method);
        }
}
