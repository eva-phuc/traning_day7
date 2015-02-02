<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.6
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends \Controller_Template
{
	public function before()
	{
		parent::before();

		\Package::load('aucfan');
		$this->_userinfo['is_login']       = false;
		$this->_userinfo['aucuniv_status'] = false;

		$this->template->set_global('userinfo', $this->_userinfo);
		$this->template->h1 = "";
	}

	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		//\Response::redirect('http://www.aucuniv.com/member');
		//exit;
		return \Response::forge(ViewModel::forge('welcome/index'));
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		\Package::load('common');
		\Common\Error::instance()
			->set_log('404 NOT FOUND - '.$_SERVER['REQUEST_URI'])
			->logging();
		$this->template->title = "NOT FOUND.";
		$this->template->content = \View::forge('welcome/404');
	}
}
