<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Empty Class
 */
class My_Model {}

/**
 * PanaceaSoft Base Model
 */
class PS_Model extends CI_Model {
	
	// name of the database table
	protected $table_name;

	// name of the ID field
	public $primary_key;

	// name of the key prefix
	protected $key_prefix;

	/**
	 * constructs required data
	 */
	function __construct( $table_name, $primary_key = false, $key_prefix = false )
	{
		parent::__construct();

		// set the table name
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		$this->key_prefix = $key_prefix;
	}

	/**
	 * Empty class to be extended
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array()) {
		
	}

	/**
	 * Generate the TeamPS Unique Key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function generate_key()
	{
		return $this->key_prefix . md5( $this->key_prefix . microtime() . uniqid() . 'teamps' );
	}

    /**
     * Determines if exist.
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  True if exist, False otherwise.
     */
    function is_exist( $id ) {
    	
    	// from table
    	$this->db->from( $this->table_name );

    	// where clause
		$this->db->where( $this->primary_key, $id );
		
		// get query
		$query = $this->db->get();

		// return the result
		return ($query->num_rows()==1);
    }

    /**
     * Save the data if id is not existed
     *
     * @param      <type>   $data   The data
     * @param      boolean  $id     The identifier
     */
	function save( &$data, $id = false ) {

		if ( !$id ) {
		// if id is not false and id is not yet existed,
			if ( !empty( $this->primary_key ) && !empty( $this->key_prefix )) {
			// if the primary key and key prefix is existed,
			
				// generate the unique key
				$data[ $this->primary_key ] = $this->generate_key();
			}

			// insert the data as new record
			
			return $this->db->insert( $this->table_name, $data );
			// print_r($this->db->last_query());die;

		} else {
		// else
			// where clause
			$this->db->where( $this->primary_key, $id);

			// update the data
			return $this->db->update($this->table_name,$data);
		}
	}

	/**
	 * Returns all the records
	 *
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 */
	function get_all( $limit = false, $offset = false ) {

		// where clause
		$this->custom_conds();

		// from table
		$this->db->from($this->table_name);

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
		return $this->db->get();
	}

	/**
  * Gets all by the conditions
  *
  * @param      array    $conds   The conds
  * @param      boolean  $limit   The limit
  * @param      boolean  $offset  The offset
  *
  * @return     <type>   All by.
  */
	 function get_all_in( $conds = array(), $limit = false, $offset = false ) {

	  // where clause
	  $this->db->where_in('id', $conds);

	  // from table
	  $this->db->from( $this->table_name );

	  if ( $limit ) {
	  // if there is limit, set the limit
	   
	   $this->db->limit($limit);
	  }
	  
	  if ( $offset ) {
	  // if there is offset, set the offset,
	   
	   $this->db->offset($offset);
	  }
	  
	   return $this->db->get();
	 }

	 

	/**
	 * Returns all the records from not in
	 *
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 */
	function get_all_not_in($ignore, $limit = false, $offset = false ) {
		// where clause
		//$this->custom_conds();

		$this->db->where_not_in('id', $ignore);
		$this->db->where('status', 1);

		$this->db->where('deleted_flag',0);


		// from table
		$this->db->from($this->table_name);

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
		return $this->db->get();
	}

	/**
	 * Returns all the records from not 
	 *
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 */

	function get_all_not($collection_id, $limit = false, $offset = false ) {

		$this->db->where('collection_id !=', $collection_id);

		// from table
		$this->db->from($this->table_name);

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
		return $this->db->get();

	}

	/**
	 * Returns the total count
	 */
	function count_all() {
		// from table
		$this->db->from( $this->table_name );

		// where clause
		$this->custom_conds();

		// return the count all results
		return $this->db->count_all_results();
	}

	/**
	 * Return the info by Id
	 *
	 * @param      <type>  $id     The identifier
	 */
	function get_one( $id ) {
		
		// query the record
		
		$query = $this->db->get_where( $this->table_name, array( $this->primary_key => $id ));
	
		if ( $query->num_rows() == 1 ) {
		// if there is one row, return the record
			
			 return $query->row();
			
		} else {
		// if there is no row or more than one, return the empty object
			
			return $this->get_empty_object( $this->table_name );
		}
	}

	/**
	 * Returns the multiple Info by Id
	 *
	 * @param      array  $ids    The identifiers
	 */
	function get_multi_info( $ids = array()) {
		
		// from table
		$this->db->from( $this->table_name );

		// where clause
		$this->db->where_in( $this->primary_key, $ids );

		// returns
		return $this->db->get();
	}

	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function get_item_favourite( $conds = array(), $limit = false, $offset = false  )
	{
		$this->db->select('cities_items.*'); 
		$this->db->from('cities_items');
		$this->db->join('cities_favourites', 'cities_favourites.item_id = cities_items.id');

		if(isset($conds['user_id'])) {

			if ($conds['user_id'] != "" || $conds['user_id'] != 0) {
					
					$this->db->where( 'user_id', $conds['user_id'] );	

			}

		}

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}
		
		return $this->db->get();
 	}

 	/**
	 * Return all related Items trending
	 */
	function get_all_related_item_trending( $conds = array(), $limit = false, $offset = false ) 
	{

		// where clause
		// inner join with Items and touches
		$this->db->select("prd.*");
		$this->db->from($this->table_name . ' as prd');
		$this->db->join('cities_touches as tou', 'prd.id = tou.type_id');
		$this->db->where( "tou.type_name", "item");
		$this->db->where( "prd.item_status_id", "1" );
		$this->db->where( "tou.type_id !=", $conds['id']);
		$this->db->where( "prd.cat_id =", $conds['cat_id']);

		$this->db->group_by("tou.type_id");
		$this->db->order_by("count(DISTINCT tou.id)", "DESC");

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}
		
	  return $this->db->get();

	}

	

 	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function get_item_like( $conds = array(), $limit = false, $offset = false  )
	{
		$this->db->select('cities_items.*'); 
		$this->db->from('cities_items');
		$this->db->join('cities_likes', 'cities_likes.item_id = cities_items.id');

		if(isset($conds['user_id'])) {

			if ($conds['user_id'] != "" || $conds['user_id'] != 0) {
					
					$this->db->where( 'user_id', $conds['user_id'] );	

			}

		}

		if ( $limit ) {
		// if there is limit, set the limit
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}

		return $this->db->get();
		
 	}


	/**
	 * Return all trending categories
	 */
	function get_all_trending_category( $conds = array(), $limit = false, $offset = false ) 
	{

		// where clause
		//$this->custom_conds( $conds );

		// inner join with Items and touches
		$this->db->select("cat.*");
		$this->db->from($this->table_name . ' as cat');
		$this->db->join('cities_touches as tou', 'cat.id = tou.type_id');
		$this->db->where( "tou.type_name", "category");
		$this->db->where( "cat.status", "1");

		$this->db->group_by("tou.type_id");
		$this->db->order_by("count(DISTINCT tou.id)", "DESC");

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
		 return $this->db->get();

	}

	/**
	 * Delete the records by Id
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete( $id )
	{
		// where clause
		$this->db->where( $this->primary_key, $id );

		// delete the record
		return $this->db->delete( $this->table_name );
		// print_r($this->db->last_query());die;
 	}

 	/**
 	 * Delete the records by ids
 	 *
 	 * @param      array   $ids    The identifiers
 	 *
 	 * @return     <type>  ( description_of_the_return_value )
 	 */
 	function delete_list( $ids = array()) {
 		
 		// where clause
		$this->db->where_in( $this->primary_key, $id );

		// delete the record
		return $this->db->delete( $this->table_name );
 	}

	/**
	 * returns the object with the properties of the table
	 *
	 * @return     stdClass  The empty object.
	 */
    function get_empty_object()
    {   
        $obj = new stdClass();
        
        $fields = $this->db->list_fields( $this->table_name );
        foreach ( $fields as $field ) {
            $obj->$field = '';
        }
        $obj->is_empty_object = true;
        return $obj;
    }

   	/**
   	 * Execute The query
   	 *
   	 * @param      <type>   $sql     The sql
   	 * @param      <type>   $params  The parameters
   	 *
   	 * @return     boolean  ( description_of_the_return_value )
   	 */
	function exec_sql( $sql, $params = false )
	{
		if ( $params ) {
		// if the parameter is not false

			// bind the parameter and run the query
			return $this->db->query( $sql, $params );	
		}

		// if there is no parameter,
		return $this->db->query( $sql );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function conditions( $conds = array())
	{
		// if condition is empty, return true
		if ( empty( $conds )) return true;
	}

	/**
	 * Check if the key is existed,
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function exists( $conds = array()) {
		// where clause
		$this->custom_conds( $conds );
		
		// from table
		$this->db->from( $this->table_name );

		// get query
		$query = $this->db->get();
		// return the result
		return ($query->num_rows() == 1);
	}

	/**
	 * Gets all by the conditions
	 *
	 * @param      array    $conds   The conds
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 *
	 * @return     <type>   All by.
	 */
	function get_all_by( $conds = array(), $limit = false, $offset = false ) {
		
		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    $this->db->having('distance < ' .  $conds['miles'] );
		}

		// where clause
		$this->custom_conds( $conds );

		// from table
		$this->db->from( $this->table_name );

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

		  return $this->db->get();
		  //print_r($this->db->last_query());die;
	}


	

	function get_all_by_type($img_parent_id, $img_type, $limit=false, $offset=false)
 	{
 		$this->db->from($this->table_name);
 		$this->db->where('img_parent_id',$img_parent_id);
 		$this->db->where('img_type', $img_type);
 		
 		if ($limit) {
 			$this->db->limit($limit);
 		}
 		
 		if ($offset) {
 			$this->db->offset($offset);
 		}
 		
 		return $this->db->get();
 	}

	/**
	 * Counts the number of all by the conditions
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  Number of all by.
	 */
	function count_all_by( $conds = array()) {
		
		// where clause
		$this->custom_conds( $conds );
		
		// from table
		$this->db->from( $this->table_name );

		// return the count all results
		return $this->db->count_all_results();
	}


	/**
	 * Sum the number of all by the conditions
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  Number of all by.
	 */
	function sum_all_by( $conds = array()) {
		
		// where clause
		$this->custom_conds( $conds );
		
		$this->db->select_sum('rating');
		// from table
		$this->db->from( $this->table_name );

		// return the count all results
		//return $this->db->count_all_results();
		return $this->db->get();
	}

	/**
	 * Gets the information by.
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  The information by.
	 */
	function get_one_by( $conds = array()) {

		// where clause
		$this->custom_conds( $conds );

		// query the record
		$query = $this->db->get( $this->table_name );

		if ( $query->num_rows() == 1 ) {
		// if there is one row, return the record
			
			return $query->row();
		} else {
		// if there is no row or more than one, return the empty object
			 return $this->get_empty_object( $this->table_name );
			
		}

	}

	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function delete_by( $conds = array() )
	{
		// where clause
		$this->custom_conds( $conds );
		// delete the record
	 return $this->db->delete( $this->table_name );
	 
 	}

 	/**
	 * Returns all the records from not 
	 *
	 * @param      boolean  $limit   The limit
	 * @param      boolean  $offset  The offset
	 */
	function get_all_delete_items($conds, $limit = false, $offset = false ) {
		// where clause
	
		$mindate = $conds['start_date'];
		$maxdate = $conds['end_date'];
		$this->db->where("updated_date BETWEEN '".$mindate."' AND '". $maxdate."'");

		// from table
		$this->db->from($this->table_name);

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}
		
		return $this->db->get();

	}

	/**
	* Gets popular categories record
	*/
	function get_category_by ( $conds = array(), $limit = false, $offset = false ){

		//$this->custom_conds();
		//where clause
		$this->db->select('cities_categories.*, count(cities_touches.type_id) as t_count');    
  		$this->db->from('cities_categories');
  		$this->db->join('cities_touches', 'cities_categories.id = cities_touches.type_id');
  		$this->db->where('cities_touches.type_name','category');
  		$this->db->where('cities_categories.status',1);

  		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			
			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(cities_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(cities_touches.added_date) <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(cities_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(cities_touches.added_date) <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			}
			 
	    }

		
  		$this->db->group_by('cities_touches.type_id');
  		$this->db->order_by('t_count', "DESC");
  		$this->db->order_by( 'cities_touches.added_date', "desc" );
  		

  		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		return $this->db->get();

	}

	/**
	Returns popular categories count
	*/
	function count_category_by($conds = array()){
		$this->custom_conds();
		//where clause
		
		$this->db->select('cities_categories.*, count(cities_touches.type_id) as t_count');    
  		$this->db->from('cities_categories');
  		$this->db->join('cities_touches', 'cities_categories.id = cities_touches.type_id');
  		$this->db->where('cities_touches.type_name','category');
  		$this->db->where('cities_categories.status',1);

		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}

			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_touches.added_date >=', $mindate );
   					$this->db->where( 'cities_touches.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_touches.added_date >=', $mindate );
   					$this->db->where( 'cities_touches.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
				
			}
			 
	    }
		

  		$this->db->group_by('cities_touches.type_id');
  		$this->db->order_by('t_count', "DESC");


  		return $this->db->count_all_results();
	}

	/**
	Returns popular Items count
	*/
	function count_item_by($conds = array()){
		$this->custom_conds();
		//where clause
		$this->db->select('cities_items.*, count(cities_touches.type_id) as t_count');    
  		$this->db->from('cities_items');
  		$this->db->join('cities_touches', 'cities_items.id = cities_touches.type_id');
  		$this->db->where('cities_touches.type_name','item');
  		$this->db->where('cities_items.item_status_id',1);

  		if ( isset( $conds['cat_id'] )) {
			if ($conds['cat_id'] != "" ) {
				if ($conds['cat_id'] != '0') {
					$this->db->where( 'cities_items.cat_id', $conds['cat_id'] );	
				} 
				
			}
		}

		//  sub category id condition 
		if ( isset( $conds['sub_cat_id'] )) {
			if ($conds['sub_cat_id'] != "" ) {
				if ($conds['sub_cat_id'] != '0') {
					$this->db->where( 'cities_items.sub_cat_id', $conds['sub_cat_id'] );
				}
				
			}
			
		}

		if ( isset( $conds['search_term'] ) || isset( $conds['date'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			
			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_touches.added_date >=', $mindate );
   					$this->db->where( 'cities_touches.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_touches.added_date >=', $mindate );
   					$this->db->where( 'cities_touches.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
				
			}
			 
	    }
  		$this->db->group_by('cities_touches.type_id');
  		$this->db->order_by('t_count', "DESC");


  		return $this->db->count_all_results();
	}

	/**
	* Gets popular Items record
	*/
	function get_item_by ( $conds = array(), $limit = false, $offset = false ){

		//where clause
		$this->db->select('cities_items.*, count(cities_touches.type_id) as t_count');    
  		$this->db->from('cities_items');
  		$this->db->join('cities_touches', 'cities_items.id = cities_touches.type_id');
  		$this->db->where('cities_touches.type_name','item');
  		$this->db->where('cities_items.item_status_id',1);
  		
  		
  		if ( isset( $conds['cat_id'] )) {
			if ($conds['cat_id'] != "" ) {
				if ($conds['cat_id'] != '0') {
					$this->db->where( 'cities_items.cat_id', $conds['cat_id'] );	
				} 
				
			}
		}

		//  sub category id condition 
		if ( isset( $conds['sub_cat_id'] )) {
			if ($conds['sub_cat_id'] != "" ) {
				if ($conds['sub_cat_id'] != '0') {
					$this->db->where( 'cities_items.sub_cat_id', $conds['sub_cat_id'] );
				}
				
			}
			
		}
  		
		if ( isset( $conds['search_term'] ) || isset( $conds['date'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			
			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(cities_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(cities_touches.added_date) <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_touches.added_date BETWEEN DATE('".$mindate."') AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {

					$today_date = date('Y-m-d');
					if($today_date == $maxdate) {
						$current_time = date('H:i:s');
						$maxdate = $maxdate . " ". $current_time;
					}

					$this->db->where( 'date(cities_touches.added_date) >=', $mindate );
   					$this->db->where( 'date(cities_touches.added_date) <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
				
			}
			 
	    }

  		$this->db->group_by('cities_touches.type_id');
  		$this->db->order_by('t_count', "DESC");
  		$this->db->order_by( 'cities_touches.added_date', "desc" );
  		
  		


  		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		return $this->db->get();
  		  //print_r($this->db->last_query());die;

	}

	/**
	* Returns purchased categories count
	*/

	 function count_purchased_category_by( $conds = array() ) {

		$this->db->select('cities_categories.*, count(cities_transactions_counts.cat_id) as t_count');    
		$this->db->from('cities_categories');
		$this->db->join('cities_transactions_counts', 'cities_categories.id = cities_transactions_counts.cat_id');
		$this->db->where('cities_categories.status',1);

  		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			
			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );

			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
				
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			}
			 
	    }
		

  		$this->db->group_by('cities_transactions_counts.cat_id');
  		$this->db->order_by('t_count', "DESC");

  		return $this->db->count_all_results();
}

/**
	* Gets purchased categories record
	*/

public function get_purchased_category_by ( $conds = array(), $limit = false, $offset = false ){
		//$this->custom_conds();
		//where clause
		$this->db->select('cities_categories.*, count(cities_transactions_counts.cat_id) as t_count');    
  		$this->db->from('cities_categories');
  		$this->db->join('cities_transactions_counts', 'cities_categories.id = cities_transactions_counts.cat_id');
  		$this->db->where('cities_categories.status',1);

		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			

			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates			
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			}
			 
	    }

  		$this->db->group_by('cities_transactions_counts.cat_id');
  		$this->db->order_by('t_count', "DESC");
  		

  		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		return $this->db->get();

	}

	/**
	Returns purchased Items count
	*/
	function count_purchased_item_by($conds = array()){
		$this->custom_conds();
		//where clause		
		$this->db->select('cities_items.*, count(cities_transactions_counts.item_id) as t_count');    
  		$this->db->from('cities_items');
  		$this->db->join('cities_transactions_counts', 'cities_items.id = cities_transactions_counts.item_id');
  		$this->db->where('cities_items.status',1);

  		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			

			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates				
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );
			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			}
			 
	    }

  		$this->db->group_by('cities_transactions_counts.item_id');
  		$this->db->order_by('t_count', "DESC");


  		return $this->db->count_all_results();
	}

	/**
	* Gets purchased Items record
	*/
	function get_purchased_item_by ( $conds = array(), $limit = false, $offset = false ){

		//$this->custom_conds();
		//where clause
		$this->db->select('cities_items.*, count(cities_transactions_counts.item_id) as t_count');    
  		$this->db->from('cities_items');
  		$this->db->join('cities_transactions_counts', 'cities_items.id = cities_transactions_counts.item_id');
  		$this->db->where('cities_items.status',1);

  		if(isset($conds['cat_id'])) {
			if ($conds['cat_id'] != "" || $conds['cat_id'] != 0) {
				$this->db->where( 'cities_transactions_counts.cat_id', $conds['cat_id'] );	
			}
		}

		if(isset($conds['sub_cat_id'])) {
			if ($conds['sub_cat_id'] != "" || $conds['sub_cat_id'] != 0) {
				$this->db->where( 'cities_transactions_counts.sub_cat_id', $conds['sub_cat_id'] );	
			}
		}

		if ( isset( $conds['search_term'] )) {
			$dates = $conds['date'];

			if ($dates != "") {
				$vardate = explode('-',$dates,2);

				$temp_mindate = $vardate[0];
				$temp_maxdate = $vardate[1];		

				$temp_startdate = new DateTime($temp_mindate);
				$mindate = $temp_startdate->format('Y-m-d');

				$temp_enddate = new DateTime($temp_maxdate);
				$maxdate = $temp_enddate->format('Y-m-d');
			} else {
				$mindate = "";
			 	$maxdate = "";
			}
			

			if ($conds['search_term'] == "" && $mindate != "" && $maxdate != "") {
				//got 2dates			
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->like( '(name', $conds['search_term'] );
				$this->db->or_like( 'name)', $conds['search_term'] );

			} else if ($conds['search_term'] != "" && $mindate != "" && $maxdate != "") {
				//got name and 2dates
				if ($mindate == $maxdate ) {

					$this->db->where("cities_transactions_counts.added_date BETWEEN DATE('".$mindate."' - INTERVAL 1 DAY) AND DATE('". $maxdate."' + INTERVAL 1 DAY)");

				} else {
					$this->db->where( 'cities_transactions_counts.added_date >=', $mindate );
   					$this->db->where( 'cities_transactions_counts.added_date <=', $maxdate );

				}
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();

			} else {
				//only name 
				$this->db->group_start();
				$this->db->like( 'name', $conds['search_term'] );
				$this->db->or_like( 'name', $conds['search_term'] );
				$this->db->group_end();
			}
			 
	    }

  		$this->db->group_by('cities_transactions_counts.item_id');
  		$this->db->order_by('t_count', "DESC");
  		

  		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		return $this->db->get();
  		 //print_r($this->db->last_query());die;

	}


 
 	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function get_city_by_touch_userid( $conds = array(), $limit = false, $offset = false )
	{
		
		$this->db->select('cities_city.*'); 
		$this->db->from('cities_city');
		$this->db->join('cities_touches', 'cities_touches.type_id = cities_city.id');

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

		$this->db->group_by("cities_touches.type_id");

		if($conds['order_by_type'] == "") {
			$this->db->order_by("count(DISTINCT cities_touches.id)", "DESC");
		} else {
			$this->db->order_by("count(DISTINCT cities_touches.id)", $conds['order_by_type']);
		}

		 return $this->db->get();
		 //print_r($this->db->last_query());die;
 	}

	/**
	Returns purchased Items count
	*/
	function get_favourite_by_month($conds = array())
	{
		
		$this->db->select('cities_favourites.*');    
  		$this->db->from('cities_favourites');
  		$this->db->where('month(added_date)',$conds['added_date']);

		return $this->db->get();
	}

	/**
	Returns city Admin 
	*/
	function get_city_admin($conds = array())
	{
	
		$this->db->select('cities_user_citys.*');    
  		$this->db->from('cities_user_citys');
  		$this->db->where('cities_user_citys.city_id',$conds['city_id']);

		return $this->db->get();
		
	}

	/**
	 * Gets the allowed modules.
	 *
	 * @param      <type>  $user_id  The user identifier
	 *
	 * @return     <type>  The allowed modules.
	 */
	function get_city_id( $conds = array() )
	{
		$this->db->select('cities_user_citys.*');    
  		$this->db->from('cities_user_citys');
  		$this->db->where('cities_user_citys.user_id',$conds['user_id']);

  		return $this->db->get();
  		
	}

	/**
	Returns city Admin 
	*/
	function get_all_module( )
	{
	
		$this->db->select('core_modules.*');    
  		$this->db->from('core_modules');
  		$this->db->where('is_show_on_menu',1);
  		$this->db->order_by('group_id','AESC');
		return $this->db->get();
		
	}

	/**
	Returns recent Item
	*/
	function get_rec_item( $conds = array() )
	{
	
		$this->db->select('cities_items.*');    
  		$this->db->from('cities_items');
  		$this->db->limit(4);
		
  		$this->db->order_by('added_date','DESC');
		return $this->db->get();
		
	}

	/**
	Returns recent Item
	*/
	function get_all_item( $conds = array() )
	{
	
		$sql = "SELECT *  FROM cities_Items ORDER BY CASE WHEN id in (". $conds['prd_ids_from_dis'] .") then -1 else id end,id, is_discount asc";
		//echo $sql;die;
		$query = $this->db->query($sql);

		return $query;
		
	}

	function get_item_status_count( $conds = array() )
	{
		$this->db->select(' count(item_status_id) as s_count'); 
		$this->db->from('cities_items');
		$this->db->join('cities_item_status','cities_items.item_status_id = cities_item_status.id');

		if(isset($conds['item_status_id'])) {

			if ($conds['item_status_id'] != "" || $conds['item_status_id'] != 0) {
					
					$this->db->where( 'item_status_id', $conds['item_status_id'] );	

			}

		}

	
	  return $this->db->get();
 	}

 	/**
	Returns recent item
	*/
	function get_all_item_collection( $conds = array() )
	{
	
		$sql = "SELECT *  FROM cities_items ORDER BY CASE WHEN id in (". $conds['itm_ids_from_coll'] .") then -1 else id end,id asc";
		$query = $this->db->query($sql);

		return $query;
		
	}

	/**
	 * Delete the records by condition
	 *
	 * @param      array   $conds  The conds
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function get_spec_by( $conds = array() )
	{
		
		$this->db->select('cities_item_spec.*'); 
		$this->db->from('cities_item_spec');
		$this->db->join('cities_items', 'cities_items.id = cities_item_spec.item_id');

		if(isset($conds['item_id'])) {

			if ($conds['item_id'] != "" || $conds['item_id'] != 0) {
					
					$this->db->where( 'item_id', $conds['item_id'] );	

			}

		}

		return $this->db->get();
		 //print_r($this->db->last_query());die;
 	}

 	function get_all_by_city( $conds = array(), $limit = false, $offset = false ) {
		
		$this->custom_conds();

		$this->db->select('cities_city.*');    
  		$this->db->from('cities_city');
  		$this->db->where('status', $conds['status']);
		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

		return $this->db->get();
		// print_r($this->db->last_query());die;
	}

	/**
	 * Return all collections
	 */
	function get_all_collections( $conds = array(), $limit = false, $offset = false ) 
	{
		
		$this->db->distinct();
		$this->db->select('itm.*');    
		$this->db->from('cities_items as itm');
		$this->db->join('cities_items_collection as cp', 'itm.id = cp.item_id');
		$this->db->where('cp.collection_id',  $conds['collection_id']);
		
		$this->db->order_by("itm.added_date", "DESC");

		if ( $limit ) {
		// if there is limit, set the limit
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			$this->db->offset($offset);
		}

		return $this->db->get();

	}

	function all_items_by_collection( $conds = array(), $limit = false, $offset = false ) 
	{
		
		// from table
		$this->db->select('cities_items.*');
		$this->db->from('cities_items');
		$this->db->where('cities_items_collection.collection_id', $conds['id']);
		
		$this->db->join('cities_items_collection', 'cities_items.id = cities_items_collection.item_id');

		

		if ( $limit ) {
		// if there is limit, set the limit
			
			$this->db->limit($limit);
		}
		
		if ( $offset ) {
		// if there is offset, set the offset,
			
			$this->db->offset($offset);
		}

  		$this->db->order_by('cities_items.added_date', "DESC");
		
		return $this->db->get();


	}

	/**
	Returns recent item
	*/
	function get_one_status( $conds = array() )
	{
	
		$sql = "SELECT *  FROM cities_item_status WHERE id = 1 OR id = 0 ";
		$query = $this->db->query($sql);

		return $query;
		
	}

	// get user with status 2 for request code

	function user_exists( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' AND `status` = '" . $conds['status'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	// get user with email conds

	function get_one_user_email( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	// get user with status 2 for verify code

	function get_one_user( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `status` = '" . $conds['status'] . "' AND `code` = '" . $conds['code'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	function get_all_not_in_lang( $id, $limit = false, $offset = false ) {
		// where clause
		$this->db->where_not_in('id', $id);

		// from table
		$this->db->from( $this->table_name );

		if ( $limit ) {
		  // if there is limit, set the limit
		   
		   $this->db->limit($limit);
		}
		  
		if ( $offset ) {
		  // if there is offset, set the offset,
		   
		   $this->db->offset($offset);
		}
		  
		return $this->db->get();
		// print_r($this->db->last_query());die;
	}

	function get_language_string( $conds = array() ){

		// from table
	  	$this->db->from( $this->table_name );

	  	if(isset($conds['language_id'])) {

			if ($conds['language_id'] != "" || $conds['language_id'] != 0) {
					
					$this->db->where( 'language_id', $conds['language_id'] );	

			}

		}

		if(isset($conds['key'])) {

			if ($conds['key'] != "" || $conds['key'] != 0) {
					
					$this->db->where( 'key', $conds['key'] );	

			}

		}

		if(isset($conds['value'])) {

			if ($conds['value'] != "" || $conds['value'] != 0) {
					
					$this->db->where( 'value', $conds['value'] );	

			}

		}

		return $this->db->get();

	}

	function get_all_item_by_paid ( $conds = array() ) {
		$this->db->select('cities_items.*'); 
		$this->db->from('cities_items');
		$this->db->join('cities_paid_items_history', 'cities_paid_items_history.item_id = cities_items.id');
		$this->db->where( 'cities_items.item_status_id',$conds['item_status_id']);
		$today_date = date('Y-m-d H:i:s');
		$this->db->where( 'cities_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'cities_paid_items_history.end_date >= ', $today_date );
   		return $this->db->get();
   		// print_r($this->db->last_query());die;
	}

	function get_item_by_paid_progress ( $item_id= array() ) {
		$this->db->select('cities_paid_items_history.*'); 
		$this->db->from('cities_paid_items_history');
		$this->db->where_in('item_id', $item_id);
		$today_date = date('Y-m-d H:i:s');
		$this->db->group_start();
		$this->db->where( 'cities_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'cities_paid_items_history.end_date >= ', $today_date );
   		$this->db->or_where( 'cities_paid_items_history.end_date >= ', $today_date );
   		$this->db->group_end();

   		// searchterm
		if(isset($conds['keyword'])) {
			$this->db->group_start();
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
			$this->db->or_like( 'description', $conds['keyword'] );
			$this->db->or_like( 'highlight_info', $conds['keyword'] );
			$this->db->group_end();
		}
   		return $this->db->get();
   		//print_r($this->db->last_query());die;
	}

	function get_all_item_by_paid_date ( $conds = array(), $limit = 200, $offset = false ) {
		$this->db->select('cities_items.*'); 
		//Start - Modify By PPH @ 12 May 2020
		if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    if ($conds['miles'] == "") {
		    	$conds['miles'] = 0;
		    	$this->db->having('distance < ' .  $conds['miles'] );
		    } else {
		    	$this->db->having('distance < ' .  $conds['miles'] );

		    } 
		}
		$this->db->from('cities_items');
		$this->db->join('cities_paid_items_history', 'cities_paid_items_history.item_id = cities_items.id');
		$today_date = date('Y-m-d H:i:s');
		$this->db->where( 'cities_items.item_status_id',$conds['item_status_id']);
		$this->db->where( 'cities_items.added_user_id',$conds['added_user_id']);
		$this->db->where( 'cities_paid_items_history.start_date <= ', $today_date );
   		$this->db->where( 'cities_paid_items_history.end_date >= ', $today_date );
   		//Start - Modify By PPH @ 12 May 2020
		if ( isset( $conds['cat_id'] )) {
			
			if ($conds['cat_id'] != "") {
				if($conds['cat_id'] != '0'){
				
					$this->db->where( 'cat_id', $conds['cat_id'] );	
				}

			}			
		}

		// searchterm
		if(isset($conds['keyword'])) {
			$this->db->group_start();
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
			$this->db->or_like( 'description', $conds['keyword'] );
			$this->db->or_like( 'highlight_info', $conds['keyword'] );
			$this->db->group_end();
		}
		
		//End - Modify By PPH @ 12 May 2020
   		if ( $limit ) {
		  	// if there is limit, set the limit
		   
		   	$this->db->limit($limit);
	  	}
	  
	  	if ( $offset ) {
		  	// if there is offset, set the offset,
		   
		   	$this->db->offset($offset);
	  	}
   		$query1 = $this->db->get_compiled_select();

	  	// from table
	  	$this->db->from( $this->table_name );

	  	if($conds['lat'] != "" && $conds['lng'] != "") {
			$this->db->select('*,( 3959
		      * acos( cos( radians('. $conds['lat'] .') )
		              * cos(  radians( lat )   )
		              * cos(  radians( lng ) - radians('. $conds['lng'] .') )
		            + sin( radians('. $conds['lat'] .') )
		              * sin( radians( lat ) )
		            )
		    ) as distance');

		    $this->db->having('distance < ' .  $conds['miles'] );
		}

	  	// keyword
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
		}

		// default where clause
		if (isset( $conds['status'] )) {
			$this->db->where( 'status', $conds['status'] );
		}

		// default where clause
		if (isset( $conds['item_status_id'] )) {
			$this->db->where( 'item_status_id', $conds['item_status_id'] );
		}

   		if(isset($conds['cat_id'])) {

			if ($conds['cat_id'] != "" || $conds['cat_id'] != 0) {
					
					$this->db->where( 'cat_id', $conds['cat_id'] );	

			}

		}

		if(isset($conds['sub_cat_id'])) {

			if ($conds['sub_cat_id'] != "" || $conds['sub_cat_id'] != 0) {
					
					$this->db->where( 'sub_cat_id', $conds['sub_cat_id'] );	

			}

		}

		// searchterm
		if(isset($conds['keyword'])) {
			$this->db->group_start();
			$this->db->like( 'name', $conds['keyword'] );
			$this->db->or_like( 'name', $conds['keyword'] );
			$this->db->or_like( 'description', $conds['keyword'] );
			$this->db->or_like( 'highlight_info', $conds['keyword'] );
			$this->db->group_end();
		}

		// feature Items
		if ( isset( $conds['is_featured'] )) {
			$this->db->where( 'is_featured', $conds['is_featured'] );
		}

		// promotion Items
		if ( isset( $conds['is_promotion'] )) {
			$this->db->where( 'is_promotion', $conds['is_promotion'] );
		}

		// rating condition
		if ( isset( $conds['rating_value'] ) ) {
			// For single rating value
			$this->db->where( 'overall_rating >=', $conds['rating_value'] );
		}

	  	if ( $limit ) {
		  	// if there is limit, set the limit
		   
		   	$this->db->limit($limit);
	  	}
	  
	  	if ( $offset ) {
		  	// if there is offset, set the offset,
		   
		   	$this->db->offset($offset);
	  	}
	  	
	  	// order by
		if ( isset( $conds['order_by_field'] )) {
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'cities_items.'.$order_by_field, $order_by_type);
		} else {
			$this->db->order_by('added_date', 'desc' );
		}
		
	    $query2 = $this->db->get_compiled_select();
	    $query = $this->db->query('( '. $query1 . ' ) UNION DISTINCT (' . $query2 .') ');
	 	
	  	return $query;
	   	
	}

	/**
	Returns recent product
	*/
	function get_all_image_update( $conds1 = array() )
	{
	
		$sql = "UPDATE core_images SET is_default=0 WHERE img_id IN ( ". $conds1['img_id'] ." )";
		
		$query = $this->db->query($sql);

		return $query;
		
	}

	// get name with status 0 or 1

	function lang_exists( $conds = array()) {

		$sql = "SELECT * FROM cities_language WHERE `name` = '" . $conds['name'] . "' ";


		$query = $this->db->query($sql);

		return $query;
	}
	
	// get symbol with status 0 or 1

	function symbol_exists( $conds = array()) {

		$sql = "SELECT * FROM cities_language WHERE `symbol` = '" . $conds['symbol'] . "' ";
		

		$query = $this->db->query($sql);

		return $query;
	}

	// get user with phone conds

	function get_one_user_phone( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_phone` = '" . $conds['user_phone'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	//get user with email and phone

	function get_email_phone( $conds = array()) {

		$sql = "SELECT * FROM core_users WHERE `user_email` = '" . $conds['user_email'] . "' AND `phone_id` = '" . $conds['phone_id'] . "' ";

		$query = $this->db->query($sql);

		return $query;
	}

	 /**
  * Gets all by the conditions
  *
  * @param      array    $conds   The conds
  * @param      boolean  $limit   The limit
  * @param      boolean  $offset  The offset
  *
  * @return     <type>   All by.
  */
	function get_all_device_in( $conds = array(), $limit = false, $offset = false ) {

	  // where clause
	  $this->db->where_in('user_id', $conds);

	  // from table
	  $this->db->from( $this->table_name );

	  if ( $limit ) {
	  // if there is limit, set the limit
	   
	   $this->db->limit($limit);
	  }
	  
	  if ( $offset ) {
	  // if there is offset, set the offset,
	   
	   $this->db->offset($offset);
	  }
	  
	  return $this->db->get();

 }

}