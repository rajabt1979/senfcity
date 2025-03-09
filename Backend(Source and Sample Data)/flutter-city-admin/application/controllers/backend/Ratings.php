<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ratings Controller
 */
class Ratings extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'RATINGS' );
		///start allow module check 
		
		$conds_mod['module_name'] = $this->router->fetch_class();
		$module_id = $this->Module->get_one_by($conds_mod)->module_id;
		
		$logged_in_user = $this->ps_auth->get_user_info();

		$user_id = $logged_in_user->user_id;
		if(empty($this->User->has_permission( $module_id,$user_id )) && $logged_in_user->user_is_sys_admin!=1){
			return redirect( site_url('/admin/') );
		}
		///end check
	}

	/**
	 * List down the registered users
	 */
	function index() {
		
		// no publish filter
		$conds['no_publish_filter'] = 1;

		// get rows count
		$this->data['rows_count'] = $this->Rate->count_all_by( $conds );

		// get ratings
		$this->data['ratings'] = $this->Rate->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}
}