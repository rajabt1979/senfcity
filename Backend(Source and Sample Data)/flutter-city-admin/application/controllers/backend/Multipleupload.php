<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Likes Controller
 */

class Multipleupload extends BE_Controller {
		/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'Multiple Upload' );
		$this->load->library('uploader');
		$this->load->library('csvimport');
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
	 * Load Api Key Entry Form
	 */

	function index( ) {
		
		$this->load_form($this->data);

	}

	

	function upload($id = false) {

		if ( $this->is_POST() ) {

			// If file upload form submitted
	        if(!empty($_FILES['images']['name'])){
	            
	        	//Start Prepare Data

	        	// specification count
			if($id) {
				$spec_counter_total = $this->get_data( 'spec_total_existing' );
				$edit_spec_id = $id;
			} else {
				$spec_counter_total = $this->get_data( 'spec_total' );
			}
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
			$data['added_user_id'] = $logged_in_user->user_id;

			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
			} else {
				//edit
				unset($data['added_date']);
				$data['updated_date'] = date("Y-m-d H:i:s");
				$data['updated_user_id'] = $logged_in_user->user_id;
			}
            	
            	//End Prepare Data

	            $filesCount = count($_FILES['images']['name']);
	            
	           	$counter = $this->get_data( 'counter' );
	       
	           	$prd_name = $data['name'];

	            for($i = 0; $i < $filesCount; $i++){
	            	
	            	// start the transaction
					$this->db->trans_start();

					

					$data['name'] = $prd_name . " " . $counter;
					$counter++;


	            	// save item
					if ( ! $this->Item->save( $data, $id )) {
					// if there is an error in inserting user data,	
						// rollback the transaction
						$this->db->trans_rollback();

						// set error message
						$this->data['error'] = get_msg( 'err_model' );
						
						return;
					}

					if ( $data['id'] != "" ) {

						$_FILES['file']['name']     = $_FILES['images']['name'][$i];
		                $_FILES['file']['type']     = $_FILES['images']['type'][$i];
		                $_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
		                $_FILES['file']['error']    = $_FILES['images']['error'][$i];
		                $_FILES['file']['size']     = $_FILES['images']['size'][$i];
		                
		                // File upload configuration
		                $config['upload_path'] = $this->config->item('upload_path');
		                $config['allowed_types'] = $this->config->item('image_type');
		                
		                // Load and initialize upload library
		                $this->load->library('upload', $config);
		                $this->upload->initialize($config);
		                
		                // Upload file to server
		                if($this->upload->do_upload('file')){
		                    // Uploaded file data
		                    $uploaded_data = $this->upload->data();

		                    $image_path = $uploaded_data['full_path'];

							$thumb_width  =   round($uploaded_data['image_width'] * 0.25, 0);
							$thumb_height =   round($uploaded_data['image_height'] * 0.25, 0);

							// create thumbnail
							$this->image_lib->clear();

							$config = array(
								'source_image' => $image_path, //$image_data['full_path'],
								'new_image'    => $this->config->item('upload_thumbnail_path'),
								'maintain_ration' => true,
								'width' => $thumb_width,
								'height' => $thumb_height
							);

							$this->image_lib->initialize($config);
							$this->image_lib->resize();


							 // prepare image data
							$image = array(
								'img_parent_id'	=> $data['id'],
								'img_type' 		=> "product",
								'img_desc' 		=> "",
								'img_path' 		=> $uploaded_data['file_name'],
								'img_width'		=> $uploaded_data['image_width'],
								'img_height'	=> $uploaded_data['image_height']
							);
							// print_r($image);die;

							// save image 
							if ( ! $this->Image->save( $image )) {
							// if error in saving image
								// set error message
								$this->data['error'] = get_msg( 'err_model' );
								
								return false;
							}
							// echo "1";die;
							
		                }
		                
		                // prepare specification 
						if($spec_counter_total == false) {
							$spec_counter_total = 1;
							$spec_title = $this->get_data('prd_spec_title1');
							$spec_desc = $this->get_data('prd_spec_desc1');
								if($spec_title != "" || $spec_desc != "") {
									$spec_data['item_id'] = $data['id'];
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
								if( $spec_title != "" || $spec_desc != "" ) {
									$spec_data['item_id'] = $data['id'];
									$spec_data['name'] = $spec_title;
									$spec_data['description'] = $spec_desc;
									$spec_data['added_date'] = date("Y-m-d H:i:s");
									$spec_data['added_user_id'] = $logged_in_user->user_id;
									
									$this->Specification->save($spec_data);
								}
							}
						}
					}


					//End - Images Upload 

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
	            
	             redirect( site_url('/admin/items'));

	        }
	        // end if $_FILES


		} else {
			//echo "66666";
			$selected_shop_id = $this->session->userdata('selected_shop_id');
			$shop_id = $selected_shop_id['shop_id'];
			$this->data['selected_shop_id'] = $shop_id;
			$this->load_form($this->data);
		}
		

	}

}