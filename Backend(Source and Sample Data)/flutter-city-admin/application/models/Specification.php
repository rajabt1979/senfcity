<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for category table
 */
class Specification extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_item_spec', 'id', 'itm_spe' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		
		// item 
		if ( isset( $conds['item_id'] )) {
			$this->db->where( 'item_id', $conds['item_id'] );
		}

		//id
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// order_by
		if ( isset( $conds['order_by'] )) {

			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];

			$this->db->order_by( 'cities_item_spec.'.$order_by_field, $order_by_type );
		} else {
			$this->db->order_by( 'added_date' );
		}

		
	}
}