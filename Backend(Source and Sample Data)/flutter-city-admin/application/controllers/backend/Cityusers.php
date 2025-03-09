<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Users crontroller for BE_USERS table
 */
class Cityusers extends BE_Controller {

	/**
	 * Constructs required variables
	 */
	function __construct() {
		parent::__construct( MODULE_CONTROL, 'USERS' );
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

		// get rows count
		$this->data['rows_count'] = $this->User_city->count_all($conds);

		// get users
		$this->data['users'] = $this->User_city->get_city_admin($conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match in system users
	 */
	function search() {

		// breadcrumb urls
		$data['action_title'] = get_msg( 'user_search' );

		// handle search term
		$search_term = $this->searchterm_handler( $this->input->post( 'searchterm' ));
		
		// condition
		$conds = array( 'searchterm' => $search_term , 'system_role_id' => 4);

		$this->data['rows_count'] = $this->User->count_all_by( $conds );

		$this->data['users'] = $this->User->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ));
		
		parent::search();
	}

	/**
	 * Create the user
	 */
	function add() {

		// breadcrumb
		$this->data['action_title'] = get_msg( 'user_add' );

		// call add logic
		parent::add();
	}

	/**
	 * Update the user
	 */
	function edit( $user_id ) {

		// breadcrumb
		$this->data['action_title'] = get_msg( 'user_edit' );

		// load user
		$this->data['user'] = $this->User->get_one( $user_id );

		// call update logic
		parent::useredit( $user_id );
	}

	/**
	 * Delete the user
	 */
	function delete() {

		// check access
		$this->check_access( DEL );

	}

	/**
	 * Saving User Info logic
	 *
	 * @param      boolean  $user_id  The user identifier
	 */
	function save( $user_id = false ) {
		
		// prepare user object and permission objects
		$user_data = array();
		$logged_in_user = $this->ps_auth->get_user_info();
		// save user_id
		if ( $this->has_data( 'user_id' )) {
			$user_data['user_id'] = $this->get_data( 'user_id' );
		}
		// save username
		if ( $this->has_data( 'user_name' )) {
			$user_data['user_name'] = $this->get_data( 'user_name' );
		}

		// save user email
		if( $this->has_data( 'user_email' )) {
			$user_data['user_email'] = $this->get_data( 'user_email' );
		}

		// save password if exists or not empty
		if ( $this->has_data( 'user_password' ) 
			&& !empty( $this->get_data( 'user_password' ))) {
			$user_data['user_password'] = md5( $this->get_data( 'user_password' ));
		}

		// save role id
		if( $this->has_data( 'role_id' )) {
			$user_data['role_id'] = $this->get_data( 'role_id' );
		}

		if($this->input->post('role_id') == 2){
			$is_city_admin = 1;
			$city_id = $this->input->post('city_id');
			
		}  elseif ($this->input->post('role_id') == 3) {
			$is_city_admin = 1;
			$city_id = $this->input->post('city_id');
		} else {
			$is_city_admin = 0;
			$city_id = 0;
		}
		
		$user_data['is_city_admin'] = $is_city_admin;
		
		$permissions = ( $this->get_data( 'permissions' ) != false )? $this->get_data( 'permissions' ): array();
		

		// save data
		if ( ! $this->User->save_user( $user_data, $permissions, $user_id )) {
		// if there is an error in inserting user data,	

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} 
		$user_id = ( !$user_id )? $user_data['user_id']: $user_id ;
		
		// prepare Item checkbox
		if ( $user_id ) {
			$conds_user_city['user_id'] = $logged_in_user->user_id;
			$user_city = $this->User_city->get_all_by( $conds_user_city )->result();
			if(count($user_city) == 1) {
					 	
						
			$select_data['city_id'] = $this->get_data('city_id');
			$select_data['user_id'] = $user_id;

			$this->User_city->save($select_data);
			} elseif ( count($user_city) > 1 ) {
				if($this->get_data( 'cityselect' ) != "") {
				$user_data['prdselect'] = explode(",", $this->get_data( 'cityselect' ));
			} else {
				$user_data['prdselect'] = explode(",", $this->get_data( 'existing_cityselect' ));
			}
			
			
				//loop

				for($i=0; $i<count($user_data['prdselect']);$i++) {
					
					if($user_data['prdselect'][$i] != "") {
						
						$select_data['city_id'] = $user_data['prdselect'][$i];
						$select_data['user_id'] = $user_id;
						
						$this->User_city->save($select_data);
					}

				}
			}

		}

		redirect( $this->module_site_url());
}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $user_id = 0 ) {

		
		$email_rule = 'required|valid_email|callback_is_valid_email['. $user_id  .']';
		$rule = 'required';

		$this->form_validation->set_rules( 'user_email', get_msg( 'user_email' ), $email_rule);
		$this->form_validation->set_rules( 'user_name', get_msg( 'user_name' ), $rule );
		
		$user = $this->User->get_one( $user_id );

		if ( !$user->user_is_sys_admin ) {
		// if updated user is not system admin,
			
			$this->form_validation->set_rules( 'permissions[]', get_msg( 'allowed_modules'), $rule );
		}
		
		if ( $user_id == 0 ) {
		// password is required if new user
			
			$this->form_validation->set_rules( 'user_password', get_msg( 'user_password' ), $rule );
			$this->form_validation->set_rules( 'conf_password', get_msg( 'conf_password' ), $rule .'|matches[user_password]' );
		}

		if ( $this->form_validation->run() == FALSE ) {
		// if there is an error in validating,
			return false;
		}

		return true;
	}

	/**
	 * Determines if valid email.
	 *
	 * @param      <type>   $email  The user email
	 * @param      integer  $user_id     The user identifier
	 *
	 * @return     boolean  True if valid email, False otherwise.
	 */
	function is_valid_email( $email, $user_id = 0 )
	{		

		if ( strtolower( $this->User->get_one( $user_id )->user_email ) == strtolower( $email )) {
		// if the email is existing email for that user id,
			
			return true;
		} else if ( $this->User->exists( array( 'user_email' => $_REQUEST['user_email'] ))) {
		// if the email is existed in the system,

			$this->form_validation->set_message('is_valid_email', get_msg( 'err_dup_email' ));
			return false;
		}

		return true;
	}

	/**
	 * Ajax Exists
	 *
	 * @param      <type>  $user_id  The user identifier
	 */
	function ajx_exists( $user_id = null )
	{
		$user_email = $_REQUEST['user_email'];
		
		if ( $this->is_valid_email( $user_email, $user_id )) {
		// if the user email is valid,
			
			echo "true";
		} else {
		// if the user email is invalid,

			echo "false";
		}
	}

}