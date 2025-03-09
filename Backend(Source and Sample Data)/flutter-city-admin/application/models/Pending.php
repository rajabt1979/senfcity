<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Item table
 */
class Pending extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_items', 'id', 'itm_' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// is_paid condition
		if (isset( $conds['is_paid'] )) {
			$this->db->where( 'is_paid', $conds['is_paid'] );
		}
		
		// order by
		if ( isset( $conds['order_by'] )) {

			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			$this->db->order_by( 'cities_items.'.$order_by_field, $order_by_type);
		}
		// Item id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );	
		}

		// category id condition
		if ( isset( $conds['cat_id'] )) {
			
			if ($conds['cat_id'] != "") {
				if($conds['cat_id'] != '0'){
				
					$this->db->where( 'cat_id', $conds['cat_id'] );	
				}

			}			
		}

		//  sub category id condition 
		if ( isset( $conds['sub_cat_id'] )) {
			
			if ($conds['sub_cat_id'] != "") {
				if($conds['sub_cat_id'] != '0'){
				
					$this->db->where( 'sub_cat_id', $conds['sub_cat_id'] );	
				}

			}			
		}
		
		// item_status_id condition
		if ( isset( $conds['item_status_id'] )) {
			
			if ($conds['item_status_id'] != "") {
				
				$this->db->where( 'item_status_id', $conds['item_status_id'] );	

			}			
		}
		
		// Item_name condition
		if ( isset( $conds['name'] )) {
			$this->db->where( 'name', $conds['name'] );
		}

		if ( isset( $conds['description'] )) {
			$this->db->where( 'description', $conds['description'] );
		}

		// Item keywords
		if ( isset( $conds['search_tag'] )) {
			$this->db->where( 'search_tag', $conds['search_tag'] );
		}

		// Item highlight information condition
		if ( isset( $conds['info'] )) {
			$this->db->where( 'highlight_information', $conds['info'] );
		}

		// feature Items
		if ( isset( $conds['is_featured'] )) {
			$this->db->where( 'is_featured', $conds['is_featured'] );
		}

		// promotion Items
		if ( isset( $conds['is_promotion'] )) {
			$this->db->where( 'is_promotion', $conds['is_promotion'] );
		}

	
		// point condition
		if ( isset( $conds['rating_min'] ) ) {
			$this->db->where( 'overall_rating >= ', $conds['rating_min'] );
		}

		if ( isset( $conds['rating_max'] ) ) {
			$this->db->where( 'overall_rating <= ', $conds['rating_max'] );
		}

		//user id condition

		if ( isset( $conds['added_user_id'] )) {
			$this->db->where( 'added_user_id', $conds['added_user_id'] );
		}

		// rating condition
		if ( isset( $conds['rating_value'] ) ) {
			// For Rating value with comma 3,4,5
			// $rating_value = explode(',', $conds['rating_value']);
			// $this->db->where_in( 'overall_rating', $rating_value);

			// For single rating value
			$this->db->where( 'overall_rating >=', $conds['rating_value'] );
		}

		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->like( 'name', $conds['searchterm'] );
		}

		// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
		}

		// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'description', $conds['keyword'] );
			$this->db->or_like( 'description', $conds['keyword'] );
		}

		// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'search_tag', $conds['keyword'] );
			$this->db->or_like( 'search_tag', $conds['keyword'] );
		}

		// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'highlight_information', $conds['keyword'] );
			$this->db->or_like( 'highlight_information', $conds['keyword'] );
		}

		// added_user_id
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'added_user_id', $conds['keyword'] );
			$this->db->or_like( 'added_user_id', $conds['keyword'] );
		}

		$this->db->order_by('added_date', 'desc' );

	}


}