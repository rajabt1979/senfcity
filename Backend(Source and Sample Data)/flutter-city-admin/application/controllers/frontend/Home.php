<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Front End Controller
 */
class Home extends FE_Controller 
{

	/**
	 * constructs required variables
	 */
	function __construct()
	{
		parent::__construct( NO_AUTH_CONTROL, 'HOME' );

	}

	/**
	 * Home Page
	 */
	function privacy_policy()
	{
		$content = $this->Privacy_policy->get_one('privacy1')->content;
		$this->data['content'] = $content;
		$this->load_template( 'privacy_policy' );
	}
	/** for fb login success */
	function fb_login_success()
	{
		$this->load_template( 'fb_login_success' );
	}
}