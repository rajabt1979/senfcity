<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for News
 */
class Items extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct( 'Item' );
		$this->load->library( 'PS_Image' );
	}

	/**
	 * Default Query for API
	 * @return [type] [description]
	 */
	function default_conds()
	{
		$conds = array();

		if ( $this->is_get ) {
		// if is get record using GET method

			// get default setting for GET_ALL_ItemS
			$setting = $this->Api->get_one_by( array( 'api_constant' => "GET_ALL_ITEMS" ));

			$conds['order_by'] = 1;
			$conds['order_by_field'] = $setting->order_by_field;
			$conds['order_by_type'] = $setting->order_by_type;
		}

		if ( $this->is_search ) {

			if($this->post('keyword') != "") {
				$conds['keyword']   = $this->post('keyword');
			}


			if($this->post('cat_id') != "") {
				$conds['cat_id']   = $this->post('cat_id');
			}

			if($this->post('sub_cat_id') != "") {
				$conds['sub_cat_id']   = $this->post('sub_cat_id');
			}

			if($this->post('is_featured') != "") {
				$conds['is_featured']   = $this->post('is_featured');
			}


			if($this->post('rating_value') != "") {
				$conds['rating_value']   = $this->post('rating_value');
			}

			if($this->post('is_promotion') != "") {
				$conds['is_promotion']   = $this->post('is_promotion');
			}

			if($this->post('lat') != "") {
				$conds['lat']   = $this->post('lat');
			}

			if($this->post('lng') != "") {
				$conds['lng']   = $this->post('lng');
			}

			if($this->post('miles') != "") {
				$conds['miles']   = $this->post('miles');
			}

			if($this->post('is_paid') != "") {
				$conds['is_paid']   = $this->post('is_paid');
			}

			if($this->post('item_status_id') != "") {
				$conds['item_status_id']   = $this->post('item_status_id');
			} else {
				$conds['item_status_id']   = 1;
			}
			
			$conds['order_by'] = 1;
			$conds['order_by_field']    = $this->post('order_by');
			$conds['order_by_type']     = $this->post('order_type');
			$conds['no_publish_filter'] = 1;
			
			if($this->post('added_user_id') != "") {
				$conds['added_user_id']   = $this->post('added_user_id');
				$conds['no_publish_filter'] = 999;
			}
			$conds['item_search'] = 1;
		}
		
		return $conds;
	}


	/**
	* Image Upload
	*/

	function upload_post()
	{
		
		$platform_name = $this->post('platform_name');
		if ( !$platform_name ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		if($platform_name == "ios") {
			
			$item_id = $this->post('item_id');

			if ( !$item_id ) {
				$this->response(array(
					'status'=>'error',
					'message'	=> 'Required Item ID'),
				404
				);
			}

			$description = $this->post('image_desc');
			$img_type = $this->post('img_type');

			//// Start Image Upload

			$uploaddir = 'uploads/';
			
			$path_parts = pathinfo( $_FILES['file']['name'] );
			$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {


				$image_info = getimagesize('uploads/'. $filename);
				$image_width = $image_info[0];
				$image_height = $image_info[1];

				$image_data['img_parent_id'] = $item_id;
				$image_data['img_type'] = "item";
				$image_data['img_path'] = $filename;
				$image_data['img_width'] = $image_width;
				$image_data['img_height'] = $image_height;
				$image_data['img_desc'] = $img_desc;

				if($this->post('img_id') == "") {
					//nothing id for new case
					$img_id = false;
				} else {
					//got id for edit case
					$img_id = $this->post('img_id');
				}

				if ( $this->Image->save( $image_data, $img_id ) ) {

					if(!isset($image_data['img_id'])) {
						$image = $this->Image->get_one($img_id);
					} else {
						$image = $this->Image->get_one($image_data['img_id']);
					}

				   	$this->response(
						$image
					);

			   } else {
				   	$this->response(array(
						'status'=>'error',
						'message'	=>'Sorry, item photo cannot create.'),
						404
					);
			   	}
			}

			
			
		} else {
			
			$item_id = $this->post('item_id');

			if ( !$item_id ) {
				$this->response(array(
					'status'=>'error',
					'message'	=> 'Required Item ID'),
				404
				);
			}

			
			
			$img_desc = $this->post('img_desc');

			$img_type = $this->post('img_type');
			//// Start Image Upload

			$uploaddir = 'uploads/';
			
			$path_parts = pathinfo( $_FILES['file']['name'] );
			$filename = $path_parts['filename'] . date( 'YmdHis' ) .'.'. $path_parts['extension'];
			
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename)) {


				$image_info = getimagesize('uploads/'. $filename);
				$image_width = $image_info[0];
				$image_height = $image_info[1];

				$image_data['img_parent_id'] = $item_id;
				$image_data['img_type'] = $img_type;
				$image_data['img_path'] = $filename;
				$image_data['img_width'] = $image_width;
				$image_data['img_height'] = $image_height;
				$image_data['img_desc'] = $img_desc;

				if($this->post('img_id') == "") {
					
					//nothing id for new case
					$img_id = false;
				} else {
					//got id for edit case
					$img_id = $this->post('img_id');
				}

				if ( $this->Image->save( $image_data, $img_id ) ) {

					if(!isset($image_data['img_id'])) {
						$image = $this->Image->get_one($img_id);
					} else {
						$image = $this->Image->get_one($image_data['img_id']);
					}

				   	$this->response(
						$image
					);

			   } else {
				   	$this->response(array(
						'status'=>'error',
						'message'	=>'Sorry, item photo cannot create.'),
						404
					);
			   	}
			}
			
		}
		
	}

	
	/**
	 * Convert Object
	 */
	function convert_object( &$obj )
	{
		// call parent convert object
		parent::convert_object( $obj );

		// convert customize Item object
		$this->ps_adapter->convert_item( $obj );
	}

	/**
     *add post
     */
    function add_post() {
		$approval_enable = $this->App_setting->get_one('app1')->is_approval_enabled;
		if ($approval_enable == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		// validation rules for user register
		$rules = array(
			array(
	        	'field' => 'cat_id',
	        	'rules' => 'required'
	        ),
	        array(
	        	'field' => 'sub_cat_id',
	        	'rules' => 'required'
	        ),
	        array(
                 'field' =>'item_status_id',
                 'rules' =>'required'
	        ),
	        array(
	        	'field' => 'name',
	        	'rules' => 'required'
	        ),
	  
	        array(
	        	'field' => 'lat',
	        	'rules' => 'required'
	        ),
	        array(
	        	'field' => 'lng',
	        	'rules' => 'required'
	        )

        );

        $lat = $this->post('lat');
		$lng = $this->post('lng');
        $location = location_check($lat,$lng);

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;
        
	  	$item_data = array(

        	"cat_id" => $this->post('cat_id'), 
        	"sub_cat_id" => $this->post('sub_cat_id'),
        	"item_status_id" =>$this->post('item_status_id'),
        	"name" =>$this->post('name'),
        	"description" => $this->post('description'),
        	"highlight_information" => $this->post('highlight_information'),
        	"search_tag" => $this->post('search_tag'),
        	"is_featured" => $this->post('is_featured'),
        	"is_promotion" => $this->post('is_promotion'),
        	"opening_hour" => $this->post('opening_hour'),
        	"closing_hour" =>$this->post('closing_hour'),
        	"time_remark" => $this->post('time_remark'),
        	"phone1" => $this->post('phone1'),
        	"phone2" =>$this->post('phone2'),
        	"phone3" =>$this->post('phone3'),
            "email" => $this->post('email'),
            "address" =>$this->post('address'),
            "facebook" =>$this->post('facebook'),
            "google_plus" =>$this->post('google_plus'),
            "twitter" =>$this->post('twitter'),
            "youtube" =>$this->post('youtube'),
            "instagram" =>$this->post('instagram'),
            "pinterest" =>$this->post('pinterest'),
            "website" =>$this->post('website'),
            "whatsapp" =>$this->post('whatsapp'),
            "messenger" =>$this->post('messenger'),
            "terms" => $this->post('terms'),
            "cancelation_policy" =>$this->post('cancelation_policy'),
            "additional_info" =>$this->post('additional_info'),
        	"lat" => $this->post('lat'),
        	"lng" => $this->post('lng'),        	
        	"id" => $this->post('id'),
        	"added_user_id" => $this->post('added_user_id'),
        	"added_date" =>  date("Y-m-d H:i:s")

        	
        );
        // print_r($item_data);die;

		$id = $item_data['id'];
		
		if($id != ""){
			$item_status_id = $this->Item->get_one($id)->item_status_id;
			$item_data['item_status_id'] = $item_status_id;
		 	$this->Item->save($item_data,$id);

		 	///start deep link update item tb by MN
			$description = $item_data['description'];
			$name = $item_data['name'];
			$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $id );
	        $images = $this->Image->get_all_by( $conds_img )->result();
			$img = $this->ps_image->upload_url . $images[0]->img_path;
			$deep_link = deep_linking_shorten_url($description,$name,$img,$id);
			$itm_data = array(
				'dynamic_link' => $deep_link
			);
			$this->Item->save($itm_data,$id);
			///End

		} else{

		 	$this->Item->save($item_data);

		 	$id = $item_data['id'];

		 	///start deep link update item tb by MN
			$description = $item_data['description'];
			$name = $item_data['name'];
			$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $id );
	        $images = $this->Image->get_all_by( $conds_img )->result();
			$img = $this->ps_image->upload_url . $images[0]->img_path;
			$deep_link = deep_linking_shorten_url($description,$name,$img,$id);
			$itm_data = array(
				'dynamic_link' => $deep_link
			);
			$this->Item->save($itm_data,$id);
			///End

		}
		 
		$obj = $this->Item->get_one( $id );
		
		$this->ps_adapter->convert_item( $obj );
		$this->custom_response( $obj );

	}
	
}