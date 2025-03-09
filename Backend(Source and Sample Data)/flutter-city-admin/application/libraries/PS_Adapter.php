<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PanaceaSoft Authentication
 */
class PS_Adapter {

	// codeigniter instance
	protected $CI;

	// login user
	protected $login_user_id;

	/**
	 * Constructor
	 */
	function __construct()
	{
		// get CI instance
		$this->CI =& get_instance();
	}

	/**
	 * Sets the login user.
	 */
	function set_login_user_id( $user_id )
	{
		$this->login_user_id = $user_id;
	}

	/**
	 * Sets the login user.
	 */
	function get_login_user_id()
	{
		return $this->login_user_id;
	}
	
	/**
	 * Gets the default photo.
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_default_photo( $id, $type )
	{
		$default_photo = "";

		// get all images
		$img = $this->CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type ))->result();

		if ( count( $img ) > 0 ) {
		// if there are images for wallpaper,
			
			$default_photo = $img[0];
		} else {
		// if no image, return empty object

			$default_photo = $this->CI->Image->get_empty_object();
		}

		return $default_photo;
	}

	/**
	 * Gets the default photo.
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_default_photo_for_gallery( $id, $type )
	{
		$default_photo = "";
		$conds['img_parent_id'] = $id;
		$conds['img_type'] = $type;
		//$conds['is_default'] = "1";

		// get all images
		$img = $this->CI->Image->get_all_by($conds)->result();
		
		if ( count( $img ) == 1 ) {
			// if there are images for gallery,
			$default_photo = $img[0];
			
		} elseif ( count( $img ) > 1 ) {
			$conds['is_default'] = "1";
			$image = $this->CI->Image->get_all_by($conds)->result();
			// if there are images for gallery,
			if(count($image) != 0) {
				$default_photo = $image[0];
			} else {
				$default_photo = $img[0];
			}

		} else {
			// if no image, return empty object
			$default_photo = $this->CI->Image->get_empty_object();
		}

		return $default_photo;
	}

	/**
	 * Customize category object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_category( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'category' );

		// set default icon 
		$obj->default_icon = $this->get_default_photo( $obj->id, 'category-icon' );
	}

	/**
	 * Customize sub category object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_sub_category( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'sub_category' );

		// set default icon 
		$obj->default_icon = $this->get_default_photo( $obj->id, 'subcat_icon' );
	}

	/**
	 * Customize product object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_item( &$obj , $need_return = false)
	{
		$conds['item_id'] = $obj->id;
		$conds['id'] = $obj->history_id;
		$paid_history = $this->CI->Paid_item->get_all_by($conds)->result();
		
		$today_date = date('Y-m-d H:i:s');
		if (count($paid_history) > 1) {
			foreach ($paid_history as $paid) {
				$tmp_result .= $paid->item_id .",";
				  
			}
			$paid_his_item_id = rtrim($tmp_result,",");
			$item_id = explode(",", $paid_his_item_id);
			
			$progress = $this->CI->Paid_item->get_item_by_paid_progress($item_id)->result();
			//for progress and finished
			if ( !empty($progress) ) {
				
				foreach ($progress as $pro) {
					if ($today_date >= $pro->start_date && $today_date <= $pro->end_date) {
						$obj->paid_status = $this->CI->config->item('progress_label');
					} else {
						$obj->paid_status = $this->CI->config->item('not_yet_start_label');
					}
				}
			} else {
				
				$obj->paid_status = $this->CI->config->item('finished_label');
			}
		} elseif (count($paid_history)==1) {
			$start_date = $paid_history[0]->start_date;
			$end_date = $paid_history[0]->end_date;
			if ($today_date >= $start_date && $today_date <= $end_date) {
				$obj->paid_status = $this->CI->config->item('progress_label');
        	} elseif ($today_date > $start_date && $today_date > $end_date) {
				$obj->paid_status = $this->CI->config->item('finished_label');
        	} else {
				$obj->paid_status = $this->CI->config->item('not_yet_start_label');
 			}
 		} else {
			$obj->paid_status = $this->CI->config->item('not_available');
		}
		unset($obj->history_id);

		//Transaction Status 
		if(isset($obj->trans_status)) {

			if($obj->trans_status != "") {
				$obj->trans_status = $obj->trans_status;
			} else {
				$obj->trans_status = "";
			}

		} else {
			$obj->trans_status = "";
		}
		

		// set default photo
		$obj->default_photo = $this->get_default_photo_for_gallery( $obj->id, 'item' );

		// category object
		if ( isset( $obj->cat_id )) {

			$tmp_category = $this->CI->Category->get_one( $obj->cat_id );

			$this->convert_category( $tmp_category );

			$obj->category = $tmp_category;
		}

		// Sub Category Object
		if ( isset( $obj->sub_cat_id )) {
			$tmp_sub_category = $this->CI->Subcategory->get_one( $obj->sub_cat_id );

			$this->convert_sub_category( $tmp_sub_category );
			$obj->sub_category = $tmp_sub_category;
		}

		// Spec Object 
		if ( isset( $obj->id )) {
			$conds['item_id'] = $obj->id;
			$tmp_spec = $this->CI->Specification->get_spec_by( $conds )->result();

			$this->convert_specification( $tmp_spec );

			$obj->specs = $tmp_spec;
		}

		if ( isset( $obj->added_user_id )) {
			$tmp_item_user = $this->CI->User->get_one( $obj->added_user_id );

			$this->convert_user( $tmp_item_user );
			$obj->user = $tmp_item_user;
		}

		//Need to check for Like and Favourite
		$obj->is_liked = 0;
		$obj->is_favourited = 0;

		if($this->get_login_user_id() != "") {
			//Need to check for Fav
			$conds['item_id'] = $obj->id;
			$conds['user_id']    = $this->get_login_user_id();

			$fav_id = $this->CI->Favourite->get_one_by($conds)->id;
			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		} else if($obj->login_user_id_post != "") {
			$conds['item_id'] = $obj->id;
			$conds['user_id']    = $obj->login_user_id_post;
			// checking for like product by user
			$like_id = $this->CI->Like->get_one_by($conds)->id;
			$obj->is_liked = 0;
			if($like_id != "") {
				$obj->is_liked = 1;
			} else {
				$obj->is_liked = 0;
			}
			
			$fav_id = $this->CI->Favourite->get_one_by($conds)->id;
			$obj->is_favourited = 0;
			if($fav_id != "") {
				$obj->is_favourited = 1;
			} else {
				$obj->is_favourited = 0;
			}

		}

		unset($obj->login_user_id_post);

		$obj->is_liked = $obj->is_liked;
		$obj->is_favourited = $obj->is_favourited;

		// like count
	    $obj->like_count = $this->CI->Like->count_all_by(array("item_id" => $obj->id));

	    // fav count
		//$obj->favourite_count =  $this->CI->Favourite->count_all_by(array("item_id" => $obj->id));

	    // image count 
		$obj->image_count =  $this->CI->Image->count_all_by(array("img_parent_id" => $obj->id));

		// touch count
		$obj->touch_count =  $this->CI->Touch->count_all_by(array("type_id" => $obj->id, "type_name" => "item"));

		// Comment count
		$obj->comment_header_count =  $this->CI->Commentheader->count_all_by(array("item_id" => $obj->id));
		
		$obj->currency_symbol = $this->CI->Paid_config->get_one( 'pconfig1' )->currency_symbol;

		$obj->currency_short_form = $this->CI->Paid_config->get_one( 'pconfig1' )->currency_short_form;
		
		

		//rating details 
		
		$total_rating_count = 0;
		$total_rating_value = 0;

		$five_star_count = 0;
		$five_star_percent = 0;

		$four_star_count = 0;
		$four_star_percent = 0;

		$three_star_count = 0;
		$three_star_percent = 0;

		$two_star_count = 0;
		$two_star_percent = 0;

		$one_star_count = 0;
		$one_star_percent = 0;


		

		//Rating Total how much ratings for this product
		$conds_rating['item_id'] = $obj->id;
		$total_rating_count = $this->CI->Rate->count_all_by($conds_rating);
		$sum_rating_value = $this->CI->Rate->sum_all_by($conds_rating)->result()[0]->rating;

		//Rating Value such as 3.5, 4.3 and etc
		if($total_rating_count > 0) {
			$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
		} else {
			$total_rating_value = 0;
		}

		//For 5 Stars rating

		$conds_five_star_rating['rating'] = 5;
		$conds_five_star_rating['item_id'] = $obj->id;
		$five_star_count = $this->CI->Rate->count_all_by($conds_five_star_rating);
		if($total_rating_count > 0) {
			$five_star_percent = number_format((float) ((100 / $total_rating_count) * $five_star_count), 1, '.', '');
		} else {
			$five_star_percent = 0;
		}

		//For 4 Stars rating
		$conds_four_star_rating['rating'] = 4;
		$conds_four_star_rating['item_id'] = $obj->id;
		$four_star_count = $this->CI->Rate->count_all_by($conds_four_star_rating);
		if($total_rating_count > 0) {
			$four_star_percent = number_format((float) ((100 / $total_rating_count) * $four_star_count), 1, '.', '');
		} else {
			$four_star_percent = 0;
		}


		//For 3 Stars rating
		$conds_three_star_rating['rating'] = 3;
		$conds_three_star_rating['item_id'] = $obj->id;
		$three_star_count = $this->CI->Rate->count_all_by($conds_three_star_rating);
		if($total_rating_count > 0) {
			$three_star_percent = number_format((float) ((100 / $total_rating_count) * $three_star_count), 1, '.', '');
		} else {
			$three_star_percent = 0;
		}


		//For 2 Stars rating
		$conds_two_star_rating['rating'] = 3;
		$conds_two_star_rating['item_id'] = $obj->id;
		$two_star_count = $this->CI->Rate->count_all_by($conds_two_star_rating);

		if($total_rating_count > 0) {
			$two_star_percent = number_format((float) ((100 / $total_rating_count) * $two_star_count), 1, '.', '');
		} else {
			$two_star_percent = 0;
		}

		//For 1 Stars rating
		$conds_one_star_rating['rating'] = 3;
		$conds_one_star_rating['item_id'] = $obj->id;
		$one_star_count = $this->CI->Rate->count_all_by($conds_one_star_rating);

		if($total_rating_count > 0) {
		$one_star_percent = number_format((float) ((100 / $total_rating_count) * $one_star_count), 1, '.', '');
		} else {
			$one_star_percent = 0;
		}


		$rating_std = new stdClass();
		$rating_std->five_star_count = $five_star_count; 
		$rating_std->five_star_percent = $five_star_percent;

		$rating_std->four_star_count = $four_star_count;
		$rating_std->four_star_percent = $four_star_percent;

		$rating_std->three_star_count = $three_star_count;
		$rating_std->three_star_percent = $three_star_percent;

		$rating_std->two_star_count = $two_star_count;
		$rating_std->two_star_percent = $two_star_percent;

		$rating_std->one_star_count = $one_star_count;
		$rating_std->one_star_percent = $one_star_percent;

		$rating_std->total_rating_count = $total_rating_count;
		$rating_std->total_rating_value = $total_rating_value;


		$obj->rating_details = $rating_std;

		if($need_return)
		{
			return $obj;
		} 

	}

	/**
	 * Customize collection object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_collection( &$obj )
	{
		$conds['collection_id'] = $obj->id;

		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'collection' );

		$collection_id = $this->CI->get_collection_id();

		$count_item = 0;
		if($collection_id == "")
		{
			$count_item_collection = $this->CI->Api->get_one_by( array( 'api_constant' => "GET_ALL_COLLECTIONS" ) )->count;
		} else {
			$count_item = $this->CI->Itemcollection->count_all_by( $conds );
		}

		if ( $count_item_collection > 0 ) {

			for($i = 0; $i < $count_item_collection ; $i++) {
				

				$tmp_collection = $this->CI->Itemcollection->get_all_collections( $conds )->result();


				if(isset($tmp_collection[$i]->id)) {

					$itm_conds['id'] = $tmp_collection[$i]->id;

					$tmp_item = $this->CI->Item->get_one_by( $itm_conds );
					$obj->items[] = $this->convert_item($tmp_item, true);
				}

			}

		}

	}

	function convert_paid_history( &$obj )
	{
		$today_date = date('Y-m-d H:i:s');
		if ($today_date >= $obj->start_date && $today_date <= $obj->end_date) {
 			$obj->paid_status = $this->CI->config->item('progress_label');
 		} elseif ($today_date > $obj->start_date && $today_date > $obj->end_date) {
				$obj->paid_status = $this->CI->config->item('finished_label');
 		} elseif ($today_date < $obj->end_date && $today_date < $obj->end_date) {
 			$obj->paid_status = $this->CI->config->item('not_yet_start_label');
		} else {
			$obj->paid_status = $this->CI->config->item('not_available');
 		}	
		// item object
		if ( isset( $obj->item_id )) {
			$tmp_item = $this->CI->Item->get_one( $obj->item_id );
			$tmp_item->history_id = $obj->id;
			$this->convert_item( $tmp_item );

			$obj->item = $tmp_item;
		}

	}

	/**
	 * Customize noti object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_noti( &$obj )
	{
		
		
		if($this->get_login_user_id() != "") {
			$noti_user_data = array(
	        	"noti_id" => $obj->id,
	        	"user_id" => $this->get_login_user_id()
	    	);
			if ( !$this->CI->Notireaduser->exists( $noti_user_data )) {
				$obj->is_read = 0;
			} else {
				$obj->is_read = 1;
			}
		} 
		


		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'noti' );
	}

	/**
	 * Customize user object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_user( &$obj )
	{

	}


	/**
	 * Customize about object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_about( &$obj )
	{
		$obj->privacypolicy = $this->CI->Privacy_policy->get_one('privacy1')->content;
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->about_id, 'about' );

	}


	/*
	 * Customize tag object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_tag( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'category' );

		// set default icon 
		$obj->default_icon = $this->get_default_photo( $obj->id, 'category-icon' );

	}


	/*
	 * Customize city object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_city( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo( $obj->id, 'city' );

		// touch count
		$obj->touch_count =  $this->CI->Touch->count_all_by(array("type_id" => $obj->id, "type_name" => "city"));
	}

	

	/**
	 * Customize category object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_feed( &$obj )
	{
		// set default photo
		$obj->default_photo = $this->get_default_photo_for_gallery( $obj->id, 'feed' );

	}


	/**
	 * Customize tag object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_rating( &$obj )
	{
		// set user object
		if ( isset( $obj->user_id )) {
			$tmp_user = $this->CI->User->get_one( $obj->user_id );

			$this->convert_user( $tmp_user );

			$obj->user = $tmp_user;
		}
	}

	/**
	 * Customize image object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_image( &$obj )
	{

	}

	/**
	 * Customize item specification object
	 *
	 * @param      <type>  $obj    The object
	 */
	function convert_specification( &$obj )
	{

		
	}

}