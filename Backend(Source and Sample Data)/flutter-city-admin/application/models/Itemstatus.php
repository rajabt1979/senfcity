<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Item delete table
 */
class Itemstatus extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_item_status', 'id', 'status' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		

		// Item_id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}
		
		// title condition
		if ( isset( $conds['title'] )) {
			$this->db->where( 'title', $conds['title'] );
		}

	}
}