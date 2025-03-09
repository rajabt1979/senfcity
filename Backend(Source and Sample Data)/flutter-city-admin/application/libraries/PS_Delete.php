<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PanaceaSoft Database Trigger
 */
class PS_Delete {

	// codeigniter instance
	protected $CI;

	/**
	 * Constructor
	 */
	function __construct()
	{
		// get CI instance
		$this->CI =& get_instance();

		// load image library
		$this->CI->load->library( 'PS_Image' );
	}

	/**
	 * Delete the category and image under the category
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_category( $category_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Category->delete( $category_id )) {
		// if there is an error in deleting category,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'category', 'img_parent_id' => $category_id );

		if ( $this->CI->delete_images_by( $conds )) {
			$conds = array( 'img_type' => 'category-icon', 'img_parent_id' => $category_id );

			if ( !$this->CI->delete_images_by( $conds )) {
			// if error in deleting image, 

				return false;
			}
		}

		if ( $enable_trigger ) {
		// if execute_trigger is enable, trigger to delete wallpaper related data
			if ( ! $this->delete_category_trigger( $category_id )) {
			// if error in deleteing wallpaper and wallpaper related data

				return false;
			}
		}

		return true;
	}

	/**
	 * Delete the Specification 
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_spec( $item_id )
	{		
		$conds = array( 'item_id' => $item_id );
		if ( ! $this->CI->Specification->delete_by( $conds )) {
		// if there is an error in deleting item,
			return false;
		}
		return true;
	}

	/**
	 * Trigger to delete wallpaper and related data when category is deleted
	 * delete wallpaper
	 * delete wallpaper images
	 * call delete_wallpaper_trigger
	 */
	function delete_category_trigger( $cat_id )
	{
		//sub category condition
		$subcategory = $this->CI->Subcategory->get_all_by( array( 'cat_id' => $cat_id ))->result();
		foreach ( $subcategory as $subcat ) {
			
			if ( !$this->delete_subcategory( $subcat->id, $enable_trigger )) {
			// if error in deleting wallpaper,

				return false;
			}
		}

		// get all wallpaper and delete the wallpaper under the category
		$Items = $this->CI->Item->get_all_by( array( 'cat_id' => $cat_id ))->result();

		if ( !empty( $Items )) {
		// if the wallpaper list not empty
			
			// loop all the wallpaper
			foreach ( $Items as $Item ) {

				// delete wallpaper and images
				$enable_trigger = true;

				if ( !$this->delete_item( $Item->id, $enable_trigger )) {
				// if error in deleting wallpaper,

					return false;
				} 
			}
		}

		return true;
	}

	/**
	 * Delete the category and image under the category
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_subcategory( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Subcategory->delete( $id )) {
		// if there is an error in deleting category,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'sub_category', 'img_parent_id' => $id );

		if ( $this->CI->delete_images_by( $conds )) {
			$conds = array( 'img_type' => 'subcat_icon', 'img_parent_id' => $id );

			if ( !$this->CI->delete_images_by( $conds )) {
			// if error in deleting image, 

				return false;
			}	
		}
		if ( $enable_trigger ) {
		// if execute_trigger is enable, trigger to delete wallpaper related data

			if ( !$this->delete_subcat_trigger( $sub_cat_id )) {
			// if error in deleting wallpaper related data,

				return false;
			}
			
		}

		return true;
	}

	function delete_subcat_trigger( $sub_cat_id )
	{
		// get all product and delete the wallpaper under the subcategory
		$items = $this->CI->Item->get_all_by( array( 'sub_cat_id' => $sub_cat_id, 'no_publish_filter' => 1 ))->result();
		if ( !empty( $items )) {
		// if the wallpaper list not empty
			
			// loop all the wallpaper
			foreach ( $items as $item ) {
				// delete wallpaper and images
				$enable_trigger = true;

				if ( !$this->delete_item( $item->id, $enable_trigger )) {
				// if error in deleting wallpaper,

					return false;
				} 
			}
		}
		return true;
	}

	/**
	 * Delete the Item and image under the Item
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_item( $item_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Item->delete( $item_id )) {
		// if there is an error in deleting Item,
			
			return false;
		} else {
			//Item is successfully deleted so need to save in log table
			//$data_delete['item_id'] = $item_id;
			//$this->CI->Item_delete->save($data_delete);
		}

		// prepare condition
		$conds = array( 'img_type' => 'item', 'img_parent_id' => $item_id );

		if ( !$this->CI->delete_images_by( $conds )) {
			
			// if error in deleting image, 

			return false;
			
		}

		if ( $enable_trigger ) {

		// if execute_trigger is enable, trigger to delete wallpaper related data
			
			if ( !$this->delete_item_trigger( $item_id )) {
			// if error in deleting wallpaper related data,

				return false;
			}
			
		}	

		return true;
	}


	/**
	 * Delete Image by id and type
	 *
	 * @param      <type>  $conds  The conds
	 */
	function delete_images_by( $conds )
	{
		// get all images
		$images = $this->CI->Image->get_all_by( $conds );

		if ( !empty( $images )) {
		// if images are not empty,

			foreach ( $images->result() as $img ) {
			// loop and delete each image

				if ( ! $this->CI->ps_image->delete_images( $img->img_path ) ) {
				// if there is an error in deleting images

					return false;
				}
			}
		}

		if ( ! $this->CI->Image->delete_by( $conds )) {
		// if error in deleting from database,

			return false;
		}

		return true;
	}

	/**
	 * Delete the Item Discount
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_prd_discount( $discount_id )
	{		
		$conds = array( 'discount_id' => $discount_id );
		if ( ! $this->CI->ItemDiscount->delete_by( $conds )) {
		// if there is an error in deleting Item,
			return false;
		}
	}

	/**
	 * Delete the Item Collection
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_itm_collection( $collection_id )
	{		
		$conds = array( 'collection_id' => $collection_id );
		if ( ! $this->CI->Itemcollection->delete_by( $conds )) {
		// if there is an error in deleting Item,
			return false;
		}
	}

	/**
	 * Delete the city Tags
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_user_city( $user_id )
	{		
		$conds = array( 'user_id' => $user_id );
		if ( ! $this->CI->User_city->delete_by( $conds )) {
		// if there is an error in deleting Item,
			return false;
		}
	}

	
	/**
	 * Delete the collection and image under the collection
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_collection( $collection_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Collection->delete( $collection_id )) {
		// if there is an error in deleting category,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'collection', 'img_parent_id' => $collection_id );

		if ( !$this->CI->delete_images_by( $conds )) {
			
			return false;
			
		}
		return true;
	}

	/**
	 * Delete the Discount and image under the Item
	 *
	 * @param      <type>  $id     The identifier
	*/
	function delete_discount( $discount_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Discount->delete( $discount_id )) {
		// if there is an error in deleting Item,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'discount', 'img_parent_id' => $discount_id );

		if ( !$this->CI->delete_images_by( $conds )) {
			
			return false;
			
		}
		return true;
	}

	/**
	 * Delete the Discount and image under the Item
	 *
	 * @param      <type>  $id     The identifier
	*/
	function delete_feed( $feed_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Feed->delete( $feed_id )) {
		// if there is an error in deleting Item,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'feed', 'img_parent_id' => $feed_id );

		if ( !$this->CI->delete_images_by( $conds )) {
			
			return false;
			
		}
		return true;
	}

	/**
	 * Delete the group
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_module_group( $group_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Module_group->delete( $group_id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	/**
	 * Delete the module
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_module( $module_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Module->delete( $module_id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	function delete_report( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Itemreport->delete( $id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	function delete_pending( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Pending->delete( $id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	function delete_disable( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Disable->delete( $id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	function delete_reject( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Reject->delete( $id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	
	 /* Delete the Attributeheader and image under the Attributeheader
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_attribute( $attribute_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Attribute->delete( $attribute_id )) {
		// if there is an error in deleting Attributeheader,
			return false;
		}
		return true;
	}
	/**
	 * Delete the Attributeheader and image under the Attributeheader
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_attdetail( $attdetail_id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Attributedetail->delete( $attdetail_id )) {
		// if there is an error in deleting Attributeheader,
			
			return false;
		}
		
		return true;
	}

	// /**
	//  * Delete the Coupon
	//  *
	//  * @param      <type>  $id     The identifier
	//  */
	function delete_coupon( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Coupon->delete( $id )) {
		// if there is an error in deleting Coupon,
			
			return false;
		}
		return true;
	}

	// /**
	//  * Delete the Shipping
	//  *
	//  * @param      <type>  $id     The identifier
	//  */
	function delete_shipping( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Shipping->delete( $id )) {
		// if there is an error in deleting Shipping,
			
			return false;
		}
		return true;
	}

	// /**
	//  * Delete the Tag
	//  *
	//  * @param      <type>  $id     The identifier
	//  */
	function delete_tag( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Tag->delete( $id )) {
		// if there is an error in deleting Shipping,
			
			return false;
		}
		return true;
	}

	/**
	 * Delete history for API
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_history( $type_id, $type_name, $enable_trigger = false )
	{		
		if( $type_name == "item") {

			if ( ! $this->CI->Item->delete( $type_id )) {
			// if there is an error in deleting item,
				return false;
			} else {
				//item is successfully deleted so need to save in log table
				$data_delete['type_id']   = $type_id;
				$data_delete['type_name'] = $type_name;


				//$this->CI->item_delete->save($data_delete);
				$this->CI->Delete_history->save($data_delete);
			}

		} else if ( $type_name == "category" ) {


			if ( ! $this->CI->Category->delete( $type_id )) {
			// if there is an error in deleting item,
				
				return false;
			} else {
				//item is successfully deleted so need to save in log table
				$data_delete['type_id']   = $type_id;
				$data_delete['type_name'] = $type_name;


				//$this->CI->item_delete->save($data_delete);
				$this->CI->Delete_history->save($data_delete);
			}



		} else if ( $type_name == "city" ) {


			if ( ! $this->CI->City->delete( $type_id )) {
			// if there is an error in deleting item,
				
				return false;
			} else {
				//item is successfully deleted so need to save in log table
				$data_delete['type_id']   = $type_id;
				$data_delete['type_name'] = $type_name;


				//$this->CI->item_delete->save($data_delete);
				$this->CI->Delete_history->save($data_delete);
			}



		} else if ( $type_name == "subcategory" ) {


			if ( ! $this->CI->Subcategory->delete( $type_id )) {
			// if there is an error in deleting item,
				
				return false;
			} else {
				//item is successfully deleted so need to save in log table
				$data_delete['type_id']   = $type_id;
				$data_delete['type_name'] = $type_name;


				//$this->CI->item_delete->save($data_delete);
				$this->CI->Delete_history->save($data_delete);
			}



		}


		// prepare condition
		if($type_name == "item") {
		
			$conds = array( 'img_type' => 'item', 'img_parent_id' => $type_id );
			if ( !$this->CI->delete_images_by( $conds )) {
			
				// if error in deleting image, 

				return false;
				
			}
		
		} else if($type_name == "category") {

			$conds = array( 'img_type' => 'category', 'img_parent_id' => $type_id );
			if ( $this->CI->delete_images_by( $conds )) {
			$conds = array( 'img_type' => 'category-icon', 'img_parent_id' => $type_id );
			// if error in deleting image,
				if ( !$this->CI->delete_images_by( $conds )) {
				
				// if error in deleting image, 

					return false;
				
				} 
			}

		} else if($type_name == "city") {

			$conds = array( 'img_type' => 'city', 'img_parent_id' => $type_id );
			if ( $this->CI->delete_images_by( $conds )) {
			$conds = array( 'img_type' => 'city-icon', 'img_parent_id' => $type_id );
			// if error in deleting image,
				if ( !$this->CI->delete_images_by( $conds )) {
				
				// if error in deleting image, 

					return false;
				
				} 
			}
		
		} else if($type_name == "subcategory") {

			$conds = array( 'img_type' => 'sub_category', 'img_parent_id' => $type_id );
			if ( $this->CI->delete_images_by( $conds )) {
			$conds = array( 'img_type' => 'subcat_icon', 'img_parent_id' => $type_id );
			// if error in deleting image,
				if ( !$this->CI->delete_images_by( $conds )) {
				
				// if error in deleting image, 

					return false;
				
				} 
			}
		
		}

		if ( $enable_trigger ) {
		// if execute_trigger is enable, trigger to delete wallpaper related data
			if( $type_name == "item" ) {
				$item_id = $type_id;
				if ( !$this->delete_item_trigger( $item_id )) {
				// if error in deleting wallpaper related data,

					return false;
				}
			} else if( $type_name == "category" ) {

				if ( !$this->delete_category_trigger( $type_id )) {
				// if error in deleting wallpaper related data,
					return false;
				}

			} else if( $type_name == "city" ) {

				if ( !$this->delete_city_trigger( $type_id )) {
				// if error in deleting wallpaper related data,
					return false;
				}

			} else if( $type_name == "subcategory" ) {

				if ( !$this->delete_subcat_trigger( $type_id )) {
				// if error in deleting wallpaper related data,
					return false;
				}

			}
			
		}

		return true;
	}

	/**
	 * Delete the registered user
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_user( $user_id )
	{		
		$conds_user['user_id'] = $user_id;
		$conds_user_item['added_user_id'] = $user_id;
		$conds_user_report['reported_user_id'] = $user_id;

		// delete user
		if ( ! $this->CI->User->delete_by( $conds_user )) {
		// if there is an error in deleting user,
			
			return false;
		}

		// delete comment header
		if ( ! $this->CI->Commentheader->delete_by( $conds_user )) {
		// if there is an error in deleting comment header,
			
			return false;
		}

		// delete comment detail
		if ( ! $this->CI->Commentdetail->delete_by( $conds_user )) {
		// if there is an error in deleting comment detail,
			
			return false;
		}

		// delete favourite
		if ( ! $this->CI->Favourite->delete_by( $conds_user )) {
		// if there is an error in deleting favourite,
			
			return false;
		}

		// delete ratings
		if ( ! $this->CI->Rate->delete_by( $conds_user )) {
		// if there is an error in deleting ratings,
			
			return false;
		}

		

		// delete items report
		if ( ! $this->CI->Itemreport->delete_by( $conds_user_report )) {
		// if there is an error in deleting items report,
			
			return false;
		}

		// delete noti token
		if ( ! $this->CI->Notitoken->delete_by( $conds_user )) {
		// if there is an error in deleting ratings,
			
			return false;
		}

		// delete items and others related with item

		$items_data 	= $this->CI->Item->get_one_by($conds_user_item);

		$item_data['item_id'] = $items_data->id;
        $touch_data['type_id'] = $items_data->id;
        $img_data['img_parent_id'] = $items_data->id;

        $this->CI->Commentheader->delete_by( $item_data );
        $this->CI->Favourite->delete_by( $item_data );
        $this->CI->Itemcollection->delete_by( $item_data );
        $this->CI->Itemreport->delete_by( $item_data );
        $this->CI->Specification->delete_by( $item_data );
        $this->CI->Rate->delete_by( $item_data ); 
        $this->CI->Touch->delete_by( $touch_data );
        $this->CI->Image->delete_by( $img_data );

		
		if ( ! $this->CI->Item->delete_by( $conds_user_item )) {
		// if there is an error in deleting items,
			return false;
		} 

		return true;
	}

	/**
	* Trigger to delete item related data when item is deleted
	* delete item related data
	*/
	function delete_item_trigger( $item_id )
	{
		$conds = array( 'item_id' => $item_id );
		$cond_touch =  array( 'type_id' => $item_id );

		$spec_data 			= $this->CI->Specification->get_one_by($conds);
		$touch_data 		= $this->CI->Touch->get_one_by($cond_touch);
		$cmt_data 			= $this->CI->Commentheader->get_one_by($conds);
		$fav_data 			= $this->CI->Favourite->get_one_by($conds);
		$collection_data 	= $this->CI->Itemcollection->get_one_by($conds);
		$report_data 		= $this->CI->Itemreport->get_one_by($conds);
		$rating_data 		= $this->CI->Rate->get_one_by($conds);


		$conds_spec['id'] 			= $spec_data->id;
		$conds_touch['id'] 			= $touch_data->id;
		$conds_cmt['id'] 			= $cmt_data->id;
		$conds_fav['fav_id'] 		= $fav_data->id;
		$conds_collection['id'] 	= $collection_data->id;
		$conds_report['id'] 		= $report_data->id;
		$conds_rating['id'] 		= $rating_data->id;

		// delete Paid history
		if ( !$this->CI->Paid_item->delete_by( $conds )) {

			return false;
		}

		// delete touches
		if ( !$this->CI->Touch->delete_by( $conds_touch )) {

			return false;
		}

		// delete specification
		if ( !$this->CI->Specification->delete_by( $conds_spec )) {

			return false;
		}

		// delete comment
		if ( !$this->CI->Commentheader->delete_by( $conds_cmt )) {

			return false;
		}

		// delete favourite
		if ( !$this->CI->Favourite->delete_by( $conds_fav )) {

			return false;
		}

		// // delete collection
		if ( !$this->CI->Itemcollection->delete_by( $conds_collection )) {

			return false;
		}

		// delete item report
		if ( !$this->CI->Itemreport->delete_by( $conds_report )) {

			return false;
		}

		// delete rating
		if ( !$this->CI->Rate->delete_by( $conds_rating )) {

			return false;
		}

		
		return true;
	}

		/**
	 * Delete the notification and image under the notification
	 *
	 * @param      <type>  $id     The identifier
	 */
	function delete_noti( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->Noti->delete( $id )) {
		// if there is an error in deleting notification,
			
			return false;
		}

		// prepare condition
		$conds = array( 'img_type' => 'noti', 'img_parent_id' => $id );

		if ( !$this->CI->delete_images_by( $conds )) {
		// if error in deleting image, 

			return false;
		}

		if ( $enable_trigger ) {
		// if execute_trigger is enable, trigger to delete wallpaper related data

			if ( ! $this->delete_noti_trigger( $id )) {
			// if error in deleteing wallpaper and wallpaper related data

				return false;
			}
		}

		return true;
	}

    function delete_purchase( $id, $enable_trigger = false )
	{		
		if ( ! $this->CI->In_app_purchase->delete( $id )) {
		// if there is an error in deleting category,
			
			return false;
		}

		if ( $enable_trigger ) {
		// if execute_trigger is enable, trigger to delete wallpaper related data
			if ( ! $this->delete_purchase_trigger( $id )) {
			// if error in deleteing wallpaper and wallpaper related data

				return false;
			}
		}

		return true;
	}

}