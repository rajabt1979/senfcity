<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for category table
 */
class Review extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_reviews', 'id', 'rew' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		
		// default where clause
		if ( !isset( $conds['no_publish_filter'] )) {
			$this->db->where( 'status', 1 );
		}

		// review condition
		if ( isset( $conds['review'] )) {
			$this->db->where( 'review', $conds['review'] );
		}

		// item_id condition
		if ( isset( $conds['item_id'] )) {
			$this->db->where( 'item_id', $conds['item_id'] );
		}

		// user_id condition
		if ( isset( $conds['user_id'] )) {
			$this->db->where( 'user_id', $conds['user_id'] );
		}

		$this->db->order_by( 'added_date', 'desc' );
	}
}