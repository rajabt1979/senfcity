<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * city Controller
 */
class Cities extends BE_Controller {

	/**
	 * set required variable and libraries
	 */
	function __construct() {
		//echo "4242342424"; die;
		parent::__construct( MODULE_CONTROL, 'CITIES' );
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
	 * Home page for the citys controller
	 */
	 
	function index( $id = "4" ) {

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input()) {

				// save user info
				$this->save( $id );
			}
		}


		$logged_in_user = $this->ps_auth->get_user_info();
		 
	 	$conds['status'] = 1;

		$this->data['city'] = $this->City->get_one( $id );
		 
		$this->data['title'] = "City Directory";

		$this->load_form($this->data);

	}

	

	function edit( $city_id = "" )
	{
		
		$cities = $this->City->get_all()->result(); 
		$city_id = $cities[0]->id;
		$this->data['city'] = $this->City->get_one( $city_id );

		// call the parent edit logic
		parent::edit( $city_id );

	}


	
	
	/**
	 * Saving Logic
	 * 1) save about data
	 * 2) check transaction status
	 *
	 * @param      boolean  $id  The about identifier
	 */
	function save( $id = false ) {
		// start the transaction
		$this->db->trans_start();

		// prepare data for save
		$data = array();
		$logged_in_user = $this->ps_auth->get_user_info();
		// prepare city id
		if ( $this->has_data( 'id' )) {
			$data['id'] = $this->get_data( 'id' );
		}

		// prepare city name
		if ( $this->has_data( 'name' )) {
			$data['name'] = $this->get_data( 'name' );
		}

		// prepare city description
		if ( $this->has_data( 'description' )) {
			$data['description'] = $this->get_data( 'description' );
		}

		// prepare city email
		if ( $this->has_data( 'email' )) {
			$data['email'] = $this->get_data( 'email' );
		}

		// prepare city lat
		if ( $this->has_data( 'lat' )) {
			$data['lat'] = $this->get_data( 'lat' );
		}

		// prepare city lng
		if ( $this->has_data( 'lng' )) {
			$data['lng'] = $this->get_data( 'lng' );
		}

		// prepare city address
		if ( $this->has_data( 'address' )) {
			$data['address'] = $this->get_data( 'address' );
		}

		// if 'is featured' is checked,
		if ( $this->has_data( 'status' )) {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}

		// if 'is featured' is checked,
		if ( $this->has_data( 'is_featured' )) {
			
			$data['is_featured'] = 1;
			if ($data['is_featured'] == 1) {

				if($this->get_data( 'is_featured_stage' ) == $this->has_data( 'is_featured' )) {
					$data['updated_date'] = date("Y-m-d H:i:s");
				} else {
					$data['featured_date'] = date("Y-m-d H:i:s");
					
				}
			}
		} else {
			
			$data['is_featured'] = 0;
		}
		// save about
		if ( ! $this->City->save( $data, $id )) {
		
			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model');
			
			return;
		}
		/** 
		 * Upload Image Records 
		 */
		if ( !$id ) {
			if ( ! $this->insert_images( $_FILES, 'city', $data['id'] )) {
				// if error in saving image

					// commit the transaction
					$this->db->trans_rollback();
					
					return;
				}
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_city_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_city_add' ));
			}
		}
		
		redirect( $this->module_site_url());

	}

	function exports()
	{
		// Load the DB utility class
		$this->load->dbutil();
		
		// Backup your entire database and assign it to a variable
		$export = $this->dbutil->backup();
		
		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download('ps_city.zip', $export);
	}
	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {

		return true;
	}


}