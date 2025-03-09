<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for touch table
 */
class Rate extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_ratings', 'id', 'rat' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// rating_id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// user_id condition
		if ( isset( $conds['user_id'] )) {
			$this->db->where( 'user_id', $conds['user_id'] );
		}

		// Item_id condition
		if ( isset( $conds['item_id'] )) {
			$this->db->where( 'item_id', $conds['item_id'] );
		}

		// rating condition
		if ( isset( $conds['rating'] )) {
			$this->db->where( 'rating', $conds['rating'] );
		}

		 $this->db->order_by('added_date', 'desc' );
	}
}