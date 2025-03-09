<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for items_collection table
 */
class Itemcollection extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'cities_items_collection', 'id', 'col_itm' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// collection id condition
		if ( isset( $conds['id'] )) {
			$this->db->where( 'id', $conds['id'] );
		}

		// collection id condition
		if ( isset( $conds['collection_id'] )) {
			$this->db->where( 'collection_id', $conds['collection_id'] );
		}

		// item id condition
		if ( isset( $conds['item_id'] )) {
			$this->db->where( 'item_id', $conds['item_id']);
		}
	}
		
}