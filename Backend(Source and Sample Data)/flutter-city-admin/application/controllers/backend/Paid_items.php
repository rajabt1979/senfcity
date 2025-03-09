<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paid_items Controller
 */
class Paid_items extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'paid_item_module' );
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
	 * List down the paid item
	 */
	function index() {

		$this->session->unset_userdata('item_id');
		$conds['is_paid'] = 1;
		
		// get rows count
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// get paid_items
		$this->data['paid_items'] = $this->Item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );


		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_search' );

		// condition with search term
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}
			
			if($this->input->post('cat_id') != ""  || $this->input->post('cat_id') != '0') {
				$conds['cat_id'] = $this->input->post('cat_id');
				$this->data['cat_id'] = $this->input->post('cat_id');
				$this->data['selected_cat_id'] = $this->input->post('cat_id');
				$this->session->set_userdata(array("cat_id" => $this->input->post('cat_id')));
				$this->session->set_userdata(array("selected_cat_id" => $this->input->post('cat_id')));
			} else {
				$this->session->set_userdata(array("cat_id" => NULL ));
			}

			if($this->input->post('sub_cat_id') != ""  || $this->input->post('sub_cat_id') != '0') {
				$conds['sub_cat_id'] = $this->input->post('sub_cat_id');
				$this->data['sub_cat_id'] = $this->input->post('sub_cat_id');
				$this->session->set_userdata(array("sub_cat_id" => $this->input->post('sub_cat_id')));
			} else {
				$this->session->set_userdata(array("sub_cat_id" => NULL ));
			}

			if($this->input->post('item_price_type_id') != ""  || $this->input->post('item_price_type_id') != '0') {
				$conds['item_price_type_id'] = $this->input->post('item_price_type_id');
				$this->data['item_price_type_id'] = $this->input->post('item_price_type_id');
				
				$this->session->set_userdata(array("item_price_type_id" => $this->input->post('item_price_type_id')));
				
			} else {
				$this->session->set_userdata(array("item_price_type_id" => NULL ));
			}

			if($this->input->post('item_type_id') != ""  || $this->input->post('item_type_id') != '0') {
				$conds['item_type_id'] = $this->input->post('item_type_id');
				$this->data['item_type_id'] = $this->input->post('item_type_id');
				
				$this->session->set_userdata(array("item_type_id" => $this->input->post('item_type_id')));
				
			} else {
				$this->session->set_userdata(array("item_type_id" => NULL ));
			}

			if($this->input->post('item_currency_id') != ""  || $this->input->post('item_currency_id') != '0') {
				$conds['item_currency_id'] = $this->input->post('item_currency_id');
				$this->data['item_currency_id'] = $this->input->post('item_currency_id');
				
				$this->session->set_userdata(array("item_currency_id" => $this->input->post('item_currency_id')));
				
			} else {
				$this->session->set_userdata(array("item_currency_id" => NULL ));
			}

			if($this->input->post('status') != "0") {
				
				$conds['status'] = $this->input->post('status');
				$this->data['status'] = $this->input->post('status');
				$this->session->set_userdata(array("status" => $this->input->post('status')));
			
			} else {
				$this->session->set_userdata(array("status" => NULL ));
			}



		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('cat_id') != NULL){
				$conds['cat_id'] = $this->session->userdata('cat_id');
				$this->data['cat_id'] = $this->session->userdata('cat_id');
				$this->data['selected_cat_id'] = $this->session->userdata('cat_id');
			}

			if($this->session->userdata('sub_cat_id') != NULL){
				$conds['sub_cat_id'] = $this->session->userdata('sub_cat_id');
				$this->data['sub_cat_id'] = $this->session->userdata('sub_cat_id');
				$this->data['selected_cat_id'] = $this->session->userdata('cat_id');
			}

			if($this->session->userdata('item_price_type_id') != NULL){
				$conds['item_price_type_id'] = $this->session->userdata('item_price_type_id');
				$this->data['item_price_type_id'] = $this->session->userdata('item_price_type_id');
			}

			if($this->session->userdata('item_type_id') != NULL){
				$conds['item_type_id'] = $this->session->userdata('item_type_id');
				$this->data['item_type_id'] = $this->session->userdata('item_type_id');
			}

			if($this->session->userdata('item_currency_id') != NULL){
				$conds['item_currency_id'] = $this->session->userdata('item_currency_id');
				$this->data['item_currency_id'] = $this->session->userdata('item_currency_id');
			}
		
			if($this->session->userdata('status') != 0){
				$conds['status'] = $this->session->userdata('status');
				$this->data['status'] = $this->session->userdata('status');
			}
			

		}

		if ($conds['status'] == "Select Status") {
			$conds['status'] = "1";
		}

		$conds['is_paid'] = "1";
		// pagination
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// search data
		$this->data['paid_items'] = $this->Item->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save category
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false,$item_id = false ) {
		$logged_in_user = $this->ps_auth->get_user_info();
		/* update item is paid 1 */
		$item_data = array(
			"is_paid" => "1"
		);
		$this->Item->save($item_data,$item_id);
		/* save paid item history data*/
	   	$added_user_id = $logged_in_user->user_id;
		$dates = $this->get_data( 'date' );
		$vardate = explode('-',$dates,2);

		$temp_mindate = $vardate[0];
		$temp_maxdate = $vardate[1];		

		$temp_startdate = new DateTime($temp_mindate);
		$start_date = $temp_startdate->format('Y-m-d');

		$temp_enddate = new DateTime($temp_maxdate);
		$end_date = $temp_enddate->format('Y-m-d');

		$d = DateTime::createFromFormat('Y-m-d', $start_date);
		$start_timestamp = $d->getTimestamp();

	  	$paid_data = array(
	  		"item_id" => $item_id,
	  		"start_date" => $start_date,
	  		"start_timestamp" => $start_timestamp,
	  		"end_date" => $end_date,
	  		"amount" => 0,
	  		"payment_method" => 'NA',
	  		"added_user_id" => $added_user_id
	  	);
		//save item
		if ( ! $this->Paid_item->save( $paid_data, $id )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}

		
		/** 
		 * Check Transactions 
		 */

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_prd_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_paid_add' ));
			}
		}


		// Item Id Checking 
		if ( $this->has_data( 'gallery' )) {
		// if there is gallery, redirecti to gallery
			redirect( $this->module_site_url( 'gallery/' .$id ));
		}
		else {
		// redirect to list view
			redirect( $this->module_site_url() );
		}
	}

	function save_edit( $id = false ) {
		
		$logged_in_user = $this->ps_auth->get_user_info();

		// Item id
		   if ( $this->has_data( 'id' )) {
				$data['id'] = $this->get_data( 'id' );

			}

		   // Category id
		   if ( $this->has_data( 'cat_id' )) {
				$data['cat_id'] = $this->get_data( 'cat_id' );
			}

			// Sub Category id
		   if ( $this->has_data( 'sub_cat_id' )) {
				$data['sub_cat_id'] = $this->get_data( 'sub_cat_id' );
			}

			// Sub status id
		   if ( $this->has_data( 'item_status_id' )) {
				$data['item_status_id'] = $this->get_data( 'item_status_id' );
			}

			// prepare Item name
			if ( $this->has_data( 'name' )) {
				$data['name'] = $this->get_data( 'name' );
			}

			// prepare Item description
			if ( $this->has_data( 'description' )) {
				$data['description'] = $this->get_data( 'description' );
			}

			// prepare Item search tag
			if ( $this->has_data( 'search_tag' )) {
				$data['search_tag'] = $this->get_data( 'search_tag' );
			}

			// prepare Item highlight information
			if ( $this->has_data( 'highlight_information' )) {
				$data['highlight_information'] = $this->get_data( 'highlight_information' );
			}
			
			// prepare Item lat
			if ( $this->has_data( 'lat' )) {
				$data['lat'] = $this->get_data( 'lat' );
			}

			// prepare Item lng
			if ( $this->has_data( 'lng' )) {
				$data['lng'] = $this->get_data( 'lng' );
			}

			// prepare Item opening_hour
			if ( $this->has_data( 'opening_hour' )) {
				$data['opening_hour'] = $this->get_data( 'opening_hour' );
			}

			// prepare Item closing_hour
			if ( $this->has_data( 'closing_hour' )) {
				$data['closing_hour'] = $this->get_data( 'closing_hour' );
			}

			// prepare Item time remark
			if ( $this->has_data( 'time_remark' )) {
				$data['time_remark'] = $this->get_data( 'time_remark' );
			}

			// prepare Item phone no 1
			if ( $this->has_data( 'phone1' )) {
				$data['phone1'] = $this->get_data( 'phone1' );
			}

			// prepare Item phone no 2
			if ( $this->has_data( 'phone2' )) {
				$data['phone2'] = $this->get_data( 'phone2' );
			}

			// prepare Item phone 3
			if ( $this->has_data( 'phone3' )) {
				$data['phone3'] = $this->get_data( 'phone3' );
			}

			// prepare Item email
			if ( $this->has_data( 'email' )) {
				$data['email'] = $this->get_data( 'email' );
			}

			// prepare Item address
			if ( $this->has_data( 'address' )) {
				$data['address'] = $this->get_data( 'address' );
			}

			// prepare Item facebook
			if ( $this->has_data( 'facebook' )) {
				$data['facebook'] = $this->get_data( 'facebook' );
			}

			// prepare Item google plus
			if ( $this->has_data( 'google_plus' )) {
				$data['google_plus'] = $this->get_data( 'google_plus' );
			}

			// prepare Item twitter
			if ( $this->has_data( 'twitter' )) {
				$data['twitter'] = $this->get_data( 'twitter' );
			}

			// prepare Item youtube
			if ( $this->has_data( 'youtube' )) {
				$data['youtube'] = $this->get_data( 'youtube' );
			}

			// prepare Item instagram
			if ( $this->has_data( 'instagram' )) {
				$data['instagram'] = $this->get_data( 'instagram' );
			}

			// prepare Item pinterest
			if ( $this->has_data( 'pinterest' )) {
				$data['pinterest'] = $this->get_data( 'pinterest' );
			}

			// prepare Item website
			if ( $this->has_data( 'website' )) {
				$data['website'] = $this->get_data( 'website' );
			}

			// prepare Item whatsapp number
			if ( $this->has_data( 'whatsapp' )) {
				$data['whatsapp'] = $this->get_data( 'whatsapp' );
			}

			// prepare Item messenger
			if ( $this->has_data( 'messenger' )) {
				$data['messenger'] = $this->get_data( 'messenger' );
			}

			// prepare Item terms & condition
			if ( $this->has_data( 'terms' )) {
				$data['terms'] = $this->get_data( 'terms' );
			}

			// prepare Item cancelation policy
			if ( $this->has_data( 'cancelation_policy' )) {
				$data['cancelation_policy'] = $this->get_data( 'cancelation_policy' );
			}

			// prepare Item additional info
			if ( $this->has_data( 'additional_info' )) {
				$data['additional_info'] = $this->get_data( 'additional_info' );
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

			// if 'is promotion' is checked,
			if ( $this->has_data( 'is_promotion' )) {
				$data['is_promotion'] = 1;
			} else {
				$data['is_promotion'] = 0;
			}

			// set timezone
			

			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
				$data['added_user_id'] = $logged_in_user->user_id;
			} else {
				//edit
				unset($data['added_date']);
				$data['updated_date'] = date("Y-m-d H:i:s");
				$data['updated_user_id'] = $logged_in_user->user_id;
			}
		//save item
		if ( ! $this->Item->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}

		
		/** 
		 * Check Transactions 
		 */

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_prd_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_prd_add' ));
			}
		}


		// Item Id Checking 
		if ( $this->has_data( 'gallery' )) {
		// if there is gallery, redirecti to gallery
			redirect( $this->module_site_url( 'gallery/' .$id ));
		}
		else {
		// redirect to list view
			redirect( $this->module_site_url() );
		}
	}

	/**
	 * Create new one
	 */
	function add($item_id=0) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'paid_prd_add' );
		$this->data['item_id'] = $item_id;

		// call the core add logic
		parent::paid_add($item_id);
	}

    function edit_paid( $id )
    {
    	$this->session->set_userdata(array("item_id" => $id ));
		
		redirect( site_url('admin/paid_items/paid_item_edit/') );
    }
	/**
 	* Update the existing one
	*/
	function paid_item_edit( ) 
	{
		$id = $this->session->userdata('item_id');
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_edit' );

		// load item
		$this->data['item'] = $this->Item->get_one( $id );

		// load history
		$conds['item_id'] = $id;
		// get rows count
		$this->data['rows_count'] = $this->Paid_item->count_all_by( $conds );
		// get paid history
		$this->data['paid_histories'] = $this->Paid_item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ));

		// call the parent edit logic
		parent::paid_edit( $id );

	}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) 
	{
		
		// $rule = 'required|callback_is_valid_name['. $id  .']';

		// $this->form_validation->set_rules( 'name', get_msg( 'name' ), $rule);
		
		// if ( $this->form_validation->run() == FALSE ) {
		// // if there is an error in validating,

		// 	return false;
		// }

		return true;
	}

	/**
	 * Determines if valid name.
	 *
	 * @param      <type>   $name  The  name
	 * @param      integer  $id     The  identifier
	 *
	 * @return     boolean  True if valid name, False otherwise.
	 */
	function is_valid_name( $name, $id = 0 )
	{		
		 // $conds['name'] = $name;
		
			// if ( strtolower( $this->Item->get_one( $id )->name ) == strtolower( $name )) {
			// // if the name is existing name for that user id,
			// 	return true;
			// } else if ( $this->Item->exists( ($conds ))) {
			// // if the name is existed in the system,
			// 	$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
			// 	return false;
			// }
			return true;
	}


	/**
	 * Check Item name via ajax
	 *
	 * @param      boolean  $Item_id  The cat identifier
	 */
	function ajx_exists( $id = false )
	{
		
		// // get Item name
		// $name = $_REQUEST['name'];
		
		// if ( $this->is_valid_name( $name, $id )) {
		// // if the Item name is valid,
			
		// 	echo "true";
		// } else {
		// // if invalid Item name,
			
		// 	echo "false";
		// }
	}

	/**
	 * Publish the record
	 *
	 * @param      integer  $prd_id  The Item identifier
	 */
	function ajx_publish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {
			//Need to delete at history table because that wallpaper need to show again on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->delete_by($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  $prd_id  The category identifier
	 */
	function ajx_unpublish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 0 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {

			//Need to save at history table because that wallpaper no need to show on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->save($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}

 }