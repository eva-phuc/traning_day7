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
class Controller_Index extends \Controller_Template
{
	public function before()
	{
		parent::before();
	}

	/**
	 * The basic welcome message
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
        $inputParams = \Input::get();
        $itemId = 0;
        if (! empty($inputParams['item'])) {
            $itemId = $inputParams['item'];
        }
        
        \Package::load('iteminfo');
        $itemUrl = Affili::getItemUrl($itemId);
        
        if (! empty($itemUrl) && $itemUrl !== false) {
            \Response::redirect($itemUrl);
            return;
        }
        
        $view = View::forge('index');
        $this->template->content = $view;
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
