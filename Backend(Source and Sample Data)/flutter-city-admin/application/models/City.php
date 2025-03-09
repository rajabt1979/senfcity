<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for city table
 */
class City extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_city', 'id', 'city' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// about_id condition
		if ( isset( $conds['no_publish_filter'] )) {
			$this->db->where( 'status', $conds['no_publish_filter'] );
		} else {
			$this->db->where('status',1);
		}

		// order by
		if ( isset( $conds['order_by'] )) {
			$order_by_field = $conds['order_by'];
			$order_by_type = $conds['order_by_type'];
			$this->db->order_by( 'cities_city.'.$order_by_field, $order_by_type);
		}

		// city name condition
		if ( isset( $conds['name'] )) {
			$this->db->where( 'name', $conds['name'] );
		}
	
		// id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// searchterm
		if ( isset( $conds['searchterm'] )) {
			$this->db->group_start();
			$this->db->like( 'name', $conds['searchterm'] );
			$this->db->or_like( 'name', $conds['searchterm'] );
			$this->db->group_end();
		}

		// keyword condition
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
		}

		// keyword condition
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'description', $conds['keyword'] );
			$this->db->or_like( 'description', $conds['keyword'] );
		}

		// feature products
		if ( isset( $conds['is_featured'] )) {
			$this->db->where( 'is_featured', $conds['is_featured'] );
		}

		$this->db->order_by( 'added_date','desc');
	}
	
}
?>