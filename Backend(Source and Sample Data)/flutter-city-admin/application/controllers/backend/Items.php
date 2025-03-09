<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Items Controller
 */
class Items extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'ITEMS' );
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
		$conds['item_status_id'] = 1;
		// get rows count
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// get categories
		$this->data['items'] = $this->Item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );


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

			// echo "aaaaaa";die;

			$conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )));
			
			if($this->input->post('is_featured') == "is_featured") {
				$conds['is_featured'] = '1';
				$this->data['is_featured'] = '1';
				$this->session->set_userdata(array("is_featured" => '1'));
			} else {
				
				$this->session->set_userdata(array("is_featured" => '0'));
			}


			if($this->input->post('is_promotion') == "is_promotion") {
				$conds['is_promotion'] = '1';
				$this->data['is_promotion'] = '1';
				$this->session->set_userdata(array("is_promotion" => '1'));
			} else {
				
				$this->session->set_userdata(array("is_promotion" => '0'));
			}

			if($this->input->post('cat_id') != ""  || $this->input->post('cat_id') != '0') {
				$conds['cat_id'] = $this->input->post('cat_id');
				$this->data['cat_id'] = $this->input->post('cat_id');
				$this->session->set_userdata(array("cat_id" => $this->input->post('cat_id')));
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

			if($this->input->post('price_min') != "") {
				$conds['price_min'] = $this->input->post('price_min');
				$this->data['price_min'] = $this->input->post('price_min');
				$this->session->set_userdata(array("price_min" => $this->input->post('price_min')));
			} else {
				$this->session->set_userdata(array("price_min" => NULL ));
			}

			if($this->input->post('price_max') != "") {
				$conds['price_max'] = $this->input->post('price_max');
				$this->data['price_max'] = $this->input->post('price_max');
				$this->session->set_userdata(array("price_max" => $this->input->post('price_max')));
			} else {
				$this->session->set_userdata(array("price_max" => NULL ));
			}

			//Order By 

			$conds['order_by'] = '1';

			if($this->input->post('order_by') == "name_asc") {
				
				$conds['order_by_field'] = "name";
				$conds['order_by_type'] = "asc";

				$this->data['order_by'] = $this->input->post('order_by');
				$this->session->set_userdata(array("order_by" => $this->input->post('order_by')));
			
			}  

			if($this->input->post('order_by') == "name_desc") {
				
				$conds['order_by_field'] = "name";
				$conds['order_by_type'] = "desc";

				$this->data['order_by'] = $this->input->post('order_by');
				$this->session->set_userdata(array("order_by" => $this->input->post('order_by')));

			
			} 

			if($this->input->post('order_by') == "price_asc") {
				
				$conds['order_by_field'] = "unit_price";
				$conds['order_by_type'] = "asc";

				$this->data['order_by'] = $this->input->post('order_by');
				$this->session->set_userdata(array("order_by" => $this->input->post('order_by')));
			
			} 

			if($this->input->post('order_by') == "price_desc") {
				
				$conds['order_by_field'] = "unit_price";
				$conds['order_by_type'] = "desc";

				$this->data['order_by'] = $this->input->post('order_by');
				$this->session->set_userdata(array("order_by" => $this->input->post('order_by')));
			
			}
			


		} else {
			//read from session value
			if($this->session->userdata('is_featured') != NULL){
				$conds['is_featured'] = $this->session->userdata('is_featured');
				$this->data['is_featured'] = $this->session->userdata('is_featured');
			}

			if($this->session->userdata('is_promotion') != NULL){
				$conds['is_promotion'] = $this->session->userdata('is_promotion');
				$this->data['is_promotion'] = $this->session->userdata('is_promotion');
			}

			if($this->session->userdata('is_landscape') != NULL){
				$conds['is_landscape'] = $this->session->userdata('is_landscape');
				$this->data['is_landscape'] = $this->session->userdata('is_landscape');
			}

			if($this->session->userdata('is_square') != NULL){
				$conds['is_square'] = $this->session->userdata('is_square');
				$this->data['is_square'] = $this->session->userdata('is_square');
			}

			if($this->session->userdata('cat_id') != NULL){
				$conds['cat_id'] = $this->session->userdata('cat_id');
				$this->data['cat_id'] = $this->session->userdata('cat_id');
			}

			if($this->session->userdata('sub_cat_id') != NULL){
				$conds['sub_cat_id'] = $this->session->userdata('sub_cat_id');
				$this->data['sub_cat_id'] = $this->session->userdata('sub_cat_id');
			}

			if($this->session->userdata('price_min') != NULL){
				$conds['price_min'] = $this->session->userdata('price_min');
				$this->data['price_min'] = $this->session->userdata('price_min');
			}			

			if($this->session->userdata('price_max') != NULL){
				$conds['price_max'] = $this->session->userdata('price_max');
				$this->data['price_max'] = $this->session->userdata('price_max');
			}


			//Order By
				$conds['order_by'] = 1;

				if($this->session->userdata('order_by') != NULL){
					
					if($this->session->userdata('order_by') == "name_asc") {
					
						$conds['order_by_field'] = "name";
						$conds['order_by_type'] = "asc";

						$this->data['order_by'] = $this->input->post('order_by');
						
					
					}  

					if($this->session->userdata('order_by') == "name_desc") {
					
						$conds['order_by_field'] = "name";
						$conds['order_by_type'] = "desc";

						$this->data['order_by'] = $this->input->post('order_by');
					
					} 


					if($this->session->userdata('order_by')  == "price_asc") {
					
						$conds['order_by_field'] = "point";
						$conds['order_by_type'] = "asc";

						$this->data['order_by'] = $this->input->post('order_by');
					
					} 

					if($this->session->userdata('order_by') == "price_desc") {
					
						$conds['order_by_field'] = "point";
						$conds['order_by_type'] = "desc";

						$this->data['order_by'] = $this->input->post('order_by');
					
					}

				}  


		}

		if ($conds['order_by_field'] == "" ){
			$conds['order_by_field'] = "added_date";
			$conds['order_by_type'] = "desc";
		}

		$conds['item_status_id'] = 1;

		// pagination
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// search data
		$this->data['items'] = $this->Item->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		$this->data['selected_cat_id'] = $this->input->post( 'cat_id' );

		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_add' );

		// call the core add logic
		parent::add();
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
	function save( $id = false ) {
		
		if((!isset($id ))|| (isset($id))) {

			// specification count
			if($id) {
				$spec_counter_total = $this->get_data( 'spec_total' );
				if ($spec_counter_total == "" || $spec_counter_total== 0) {
					$spec_counter_total = $this->get_data( 'spec_total_existing' );
				}
				$edit_spec_id = $id;
			} else {
				$spec_counter_total = $this->get_data( 'spec_total' );
			}
			// start the transaction
			// $this->db->trans_start();
			$logged_in_user = $this->ps_auth->get_user_info();
			
			/** 
			 * Insert Item Records 
			 */
			$data = array();

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

			//get inserted product id
			$id = ( !$id )? $data['id']: $id ;

			// prepare specification 
			if($spec_counter_total == false) {
				$spec_counter_total = 1;
				$spec_title = $this->get_data('prd_spec_title1');
				$spec_desc = $this->get_data('prd_spec_desc1');
				sleep(1);
					if($spec_title != "" || $spec_desc != "") {
						$spec_data['item_id'] = $id;
						$spec_data['name'] = $spec_title;
						$spec_data['description'] = $spec_desc;
						$spec_data['added_date'] = date("Y-m-d H:i:s");
						$spec_data['added_user_id'] = $logged_in_user->user_id;

						$this->Specification->save($spec_data);
					}
			} else {
				$this->ps_delete->delete_spec( $id );
				$spec_counter_total = $spec_counter_total;
				for($j=1; $j<=$spec_counter_total; $j++) {
					$spec_title = $this->get_data('prd_spec_title' . $j);
					$spec_desc = $this->get_data('prd_spec_desc' . $j);
					sleep(1);
					if( $spec_title != "" || $spec_desc != "" ) {
						$spec_data['item_id'] = $id;
						$spec_data['name'] = $spec_title;
						$spec_data['description'] = $spec_desc;
						$spec_data['added_date'] = date("Y-m-d H:i:s");
						$spec_data['added_user_id'] = $logged_in_user->user_id;
						
						$this->Specification->save($spec_data);
					}
				}
			}

			/** 
			* Upload Image Records 
			*/
		
			if ( $id != "" ) {
			// if id is false, this is adding new record

				if ( ! $this->insert_images( $_FILES, 'item', $data['id'] )) {
				// if error in saving image

				}

				
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
		}
		//get inserted Item id
		$id = ( !$id )? $data['id']: $id ;

		///start deep link update item tb by MN
		$description = $data['description'];
		$name = $data['name'];
		$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $id );
        $images = $this->Image->get_all_by( $conds_img )->result();
		$img = $this->ps_image->upload_url . $images[0]->img_path;
		$deep_link = deep_linking_shorten_url($description,$name,$img,$id);
		$itm_data = array(
			'dynamic_link' => $deep_link
		);
		
		$this->Item->save($itm_data,$id);
		///End	

		// Item Id Checking 
		if ( $this->has_data( 'gallery' )) {
		// if there is gallery, redirecti to gallery
			redirect( $this->module_site_url( 'gallery/' .$id ));
		}else if ( $this->has_data( 'promote' )) {
			redirect( site_url( ) . '/admin/paid_items/add/'.$id);
		}
		else {
		// redirect to list view
			redirect( $this->module_site_url() );
		}
	}

	function get_all_sub_categories( $cat_id )
    {
    	$conds['cat_id'] = $cat_id;
    	
    	$sub_categories = $this->Subcategory->get_all_by($conds);
		echo json_encode($sub_categories->result());
    }

    /**
	 * To get all products for collection
	 *
	 */
	function get_all_items_for_collection($collection_id = '000') 
	{

		//Datatables Variables
		if ($collection_id=='000') {

 			// Datatables Variables
		    $draw = intval($this->input->get("draw"));
	        $start = intval($this->input->get("start"));
	        $length = intval($this->input->get("length"));

			//get all items
	        
	        $items = $this->Item->get_all_by($conds);

	        $data = array();

	        foreach($items->result() as $r) {

	            $data[] = array(
	                $r->id,
	                $r->name,
	                $r->opening_hour,
	                $r->closing_hour
	            );
	        }

	          $output = array(
	               "draw" => $draw,
	                 "recordsTotal" => $items->num_rows(),
	                 "recordsFiltered" => $items->num_rows(),
	                 "data" => $data
	             );
	        echo json_encode($output);
	        exit();
	 	} else {
	 		
 		// Datatables Variables
          $draw = intval($this->input->get("draw"));
          $start = intval($this->input->get("start"));
          $length = intval($this->input->get("length"));

		    $conds1['collection_id'] = $collection_id;
    
		  $item_collection = $this->Itemcollection->get_all_by($conds1)->result();
		  if($item_collection) {
			  $result = "";
			  foreach ($item_collection as $itm_collect) {
			  	$result .= "'".$itm_collect->item_id ."'" .",";
			  
			  }
			  $itm_ids_from_coll = rtrim($result,",");

			  $conds['itm_ids_from_coll'] = $itm_ids_from_coll;
				
	          $itm_dis = $this->Item->get_all_item_collection($conds);

	          $items_data = array();
	          foreach($itm_dis->result() as $prd) {
	          
	          $items_data[] = array(
                 $prd->id,
                 $prd->name,
                 $prd->opening_hour,
                 $prd->closing_hour
             	);
	         }
	          

	          $output = array(
	                "draw" => $draw,
	              "recordsTotal" => $itm_dis->num_rows(),
	              "recordsFiltered" => $itm_dis->num_rows(),
	              "data" => $items_data

	            );
	          echo json_encode($output);
	          exit();
	      	} else {
	      		  $items = $this->Item->get_all_by($conds);

		        $data = array();

		        foreach($items->result() as $r) {

		            $data[] = array(
		                $r->id,
		                $r->name,
		                $r->opening_hour,
		                $r->closing_hour
		            );
		        }

		          $output = array(
		               "draw" => $draw,
		                 "recordsTotal" => $items->num_rows(),
		                 "recordsFiltered" => $items->num_rows(),
		                 "data" => $data
		             );
		        echo json_encode($output);
		        exit();
	      	}
 		} 

	}

    /**
	 * Show Gallery
	 *
	 * @param      <type>  $id     The identifier
	 */
	function gallery( $id ) {
		// breadcrumb urls
		$edit_item = get_msg('prd_edit');

		$this->data['action_title'] = array( 
			array( 'url' => 'edit/'. $id, 'label' => $edit_item ), 
			array( 'label' => get_msg( 'item_gallery' ))
		);
		
		$_SESSION['parent_id'] = $id;
		$_SESSION['type'] = 'item';
    	    	
    	$this->load_gallery();
    }


	/**
 	* Update the existing one
	*/
	function edit( $id ) 
	{
		
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_edit' );

		// load user
		$this->data['item'] = $this->Item->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );

	}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) 
	{
		
		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'name', get_msg( 'name' ), $rule);
		
		if ( $this->form_validation->run() == FALSE ) {
		// if there is an error in validating,

			return false;
		}

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
		 $conds['name'] = $name;
		
			if ( strtolower( $this->Item->get_one( $id )->name ) == strtolower( $name )) {
			// if the name is existing name for that user id,
				return true;
			} else if ( $this->Item->exists( ($conds ))) {
			// if the name is existed in the system,
				$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
				return false;
			}
			return true;
	}


	/**
	 * Delete the record
	 * 1) delete Item
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) 
	{
		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );

		// delete categories and images
		$enable_trigger = true; 
		
		// delete categories and images
		//if ( !$this->ps_delete->delete_product( $id, $enable_trigger )) {
		$type = "item";

		if ( !$this->ps_delete->delete_history( $id, $type, $enable_trigger )) {

			// set error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));

			// rollback
			$this->trans_rollback();

			// redirect to list view
			redirect( $this->module_site_url());
		}
		/**
		 * Check Transcation Status
		 */
		if ( !$this->check_trans()) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
		} else {
        	
			$this->set_flash_msg( 'success', get_msg( 'success_prd_delete' ));
		}
		
		redirect( $this->module_site_url());
	}


	/**
	 * Check Item name via ajax
	 *
	 * @param      boolean  $Item_id  The cat identifier
	 */
	function ajx_exists( $id = false )
	{
		
		// get Item name
		$name = $_REQUEST['name'];
		
		if ( $this->is_valid_name( $name, $id )) {
		// if the Item name is valid,
			
			echo "true";
		} else {
		// if invalid Item name,
			
			echo "false";
		}
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
		$prd_data = array( 'item_status_id'=> 1 );
			
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
		$prd_data = array( 'item_status_id'=> 0 );
			
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