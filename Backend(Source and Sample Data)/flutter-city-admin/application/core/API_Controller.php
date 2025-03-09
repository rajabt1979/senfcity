<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Main Controller for API classes
 */
class API_Controller extends REST_Controller
{
	// model to access database
	protected $model;

	// validation rule for new record
	protected $create_validation_rules;

	// validation rule for update record
	protected $update_validation_rules;

	// validation rule for delete record
	protected $delete_validation_rules;

	// is adding record?
	protected $is_add;

	// is updating record?
	protected $is_update;

	// is deleting record?
	protected $is_delete;

	// is get record using GET method?
	protected $is_get;

	// is search record using GET method?
	protected $is_search;

	// login user id API parameter key name
	protected $login_user_key;

	// login user id
	protected $login_user_id;

	// if API allowed zero login user id,
	protected $is_login_user_nullable;

	// default value to ignore user id
	protected $ignore_user_id;

	/**
	 * construct the parent 
	 */
	function __construct($model, $is_login_user_nullable = false)
	{
		// header('Access-Control-Allow-Origin: *');
		// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

		parent::__construct();

		// set the model object
		$this->model = $this->{$model};

		// load security library
		$this->load->library('PS_Security');

		// load the adapter library
		$this->load->library('PS_Adapter');

		// set the login user nullable
		$this->is_login_user_nullable = $is_login_user_nullable;

		// login user id key
		$this->login_user_key = "login_user_id";

		// default value to ignore user id for API
		$this->ignore_user_id = "nologinuser";

		if ($this->is_logged_in()) {
			// if login user id is existed, pass the id to the adapter

			$this->login_user_id = $this->get_login_user_id();

			if (!$this->User->is_exist($this->login_user_id) && !$this->is_login_user_nullable) {
				// if login user id not existed in system,

				$this->error_response(get_msg('invalid_login_user_id'), 400);
			}

			$this->ps_adapter->set_login_user_id($this->login_user_id);
		}

		// load the mail library
		$this->load->library('PS_Mail');

		if (!$this->is_valid_api_key()) {
			// if invalid api key

			$this->response(array(
				'status' => 'error',
				'message' => get_msg('invalid_api_key')
			), 404);
		}

		// default validation rules
		$this->default_validation_rules();
	}

	/**
	 * Determines if logged in.
	 *
	 * @return     boolean  True if logged in, False otherwise.
	 */
	function is_logged_in()
	{
		// it is login user if the GET login_user_id is not null and default key
		// it is login user if the POST login_user_id is not null
		// it is login user if the PUT login_user_id is not null
		return ($this->get($this->login_user_key) != null && $this->get($this->login_user_key) != $this->ignore_user_id) ||
			($this->post($this->login_user_key) != null) ||
			($this->put($this->login_user_key) != null);
	}

	/**
	 * Gets the login user identifier.
	 */
	function get_login_user_id()
	{
		/**
		 * GET['login_user_id'] will create POST['user_id']
		 * POST['login_user_id'] will create POST['user_id'] and remove POST['login_user_id']
		 * PUT['login_user_id'] will create PUT['user_id'] and remove PUT['login_user_id']
		 */
		// if exist in get variable,
		if ($this->get($this->login_user_key) != null) {

			// get user id
			$login_user_id = $this->get($this->login_user_key);

			// replace user_id
			$this->_post_args['user_id'] = $this->get($this->login_user_key);

			return $this->get($this->login_user_key);
		}

		// if exist in post variable,
		if ($this->post($this->login_user_key) != null) {

			// get user id
			$login_user_id = $this->post($this->login_user_key);

			// replace user_id
			$this->_post_args['user_id'] = $this->post($this->login_user_key);
			unset($this->_post_args[$this->login_user_key]);

			return $login_user_id;
		}

		// if exist in put variable,
		if ($this->put($this->login_user_key) != null) {

			// get user id
			$login_user_id = $this->put($this->login_user_key);

			// replace user_id
			$this->_put_args['user_id'] = $this->put($this->login_user_key);
			unset($this->_put_args[$this->login_user_key]);

			return $login_user_id;
		}
	}

	/**
	 * Convert logged in user id to user_id
	 */
	function get_similar_key($actual, $similar)
	{
		if (empty(parent::post($actual)) && empty(parent::put($actual))) {
			// if actual key is not existed in POST and PUT, return similar

			return $similar;
		}

		// else, just return normal key
		return $actual;
	}

	/**
	 * Override Get variables
	 *
	 * @param      <type>  $key    The key
	 */
	function get($key = NULL, $xss_clean = true)
	{
		return $this->ps_security->clean_input(parent::get($key, $xss_clean));
	}

	/**
	 * Override Post variables
	 *
	 * @param      <type>  $key    The key
	 */
	function post($key = NULL, $xss_clean = true)
	{
		if ($key == 'user_id') {
			// if key is user_id and user_id is not in variable, get the similar key

			$key = $this->get_similar_key('user_id', $this->login_user_key);
		}

		return $this->ps_security->clean_input(parent::post($key, $xss_clean));
	}

	/**
	 * Override Put variables
	 *
	 * @param      <type>  $key    The key
	 */
	function put($key = NULL, $xss_clean = true)
	{
		if ($key == 'user_id') {
			// if key is user_id and user_id is not in variable, get the similar key

			$key = $this->get_similar_key('user_id', $this->login_user_key);
		}

		return $this->ps_security->clean_input(parent::put($key, $xss_clean));
	}

	/**
	 * Determines if valid api key.
	 *
	 * @return     boolean  True if valid api key, False otherwise.
	 */
	function is_valid_api_key()
	{
		$client_api_key = $this->get('api_key');

		if ($client_api_key == NULL) {
			// if API key is null, return false;

			return false;
		}

		$server_api_key = $this->Api_key->get_one('apikey1')->key;

		if ($client_api_key != $server_api_key) {
			// if API key is different with server api key, return false;

			return false;
		}

		return true;
	}

	/**
	 * Convert Object
	 */
	function convert_object(&$obj)
	{
		// convert added_date date string
		if (isset($obj->added_date)) {

			// added_date timestamp string
			$obj->added_date_str = ago($obj->added_date);
		}
	}

	/**
	 * Gets the default photo.
	 *
	 * @param      <type>  $id     The identifier
	 * @param      <type>  $type   The type
	 */
	function get_default_photo($id, $type)
	{
		$default_photo = "";

		// get all images
		$img = $this->Image->get_all_by(array('img_parent_id' => $id, 'img_type' => $type))->result();

		if (count($img) > 0) {
			// if there are images for news,

			$default_photo = $img[0];
		} else {
			// if no image, return empty object

			$default_photo = $this->Image->get_empty_object();
		}

		return $default_photo;
	}

	function all_collection_items_get($conds = array(), $limit = false, $offset = false)
	{
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		$collection_id = $this->get('id');

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty
			$data = $this->model->all_items_by_collection($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->all_items_by_collection($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->model->all_items_by_collection($conds)->result();
		}
		$this->custom_response($data, $offset);
	}

	/**
	 * Response Error
	 *
	 * @param      <type>  $msg    The message
	 */
	function error_response($msg, $code = false)
	{
		$this->response(array(
			'status' => 'error',
			'message' => $msg
		), $code);
	}

	/**
	 * Response Success
	 *
	 * @param      <type>  $msg    The message
	 */
	function success_response($msg, $code = false)
	{
		$this->response(array(
			'status' => 'success',
			'message' => $msg
		), $code);
	}

	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response($data, $offset = false, $require_convert = true)
	{

		if (empty($data)) {
			// if there is no data, return error
			$offset = $this->get('offset');
			if (empty($data) && $offset == 0) {
				$this->error_response(get_msg('record_not_found'), 404);
			} else if (empty($data) && $offset > 0) {
				$this->error_response(get_msg('record_no_pagination'), 404);
			}
		} else if ($require_convert) {
			// if there is data, return the list
			if (is_array($data)) {
				// if the data is array
				foreach ($data as $obj) {

					// convert object for each obj
					$this->convert_object($obj);
				}
			} else {

				$this->convert_object($data);
			}
		}

		$data = $this->ps_security->clean_output($data);

		$this->response($data);
	}


	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_fail_response($data, $require_convert = true, $message = "")
	{
		if (empty($data)) {
			// if there is no data, return error

			$this->error_response(get_msg('record_not_found'), 404);
		} else if ($require_convert) {
			// if there is data, return the list

			if (is_array($data)) {
				// if the data is array

				foreach ($data as $obj) {

					// convert object for each obj
					//$this->convert_object( $obj );
					$obj->trans_status = $message;
					$this->ps_adapter->convert_item($obj);
				}
			} else {
				$data->trans_status = $message;
				//$this->convert_object( $data );
				$this->ps_adapter->convert_item($data);
			}
		}

		$data = $this->ps_security->clean_output($data);

		$this->response($data);
		// $this->response( array(
		// 	'status' => $message,
		// 	'data' => $data
		// ));
	}

	/**
	 * Default Validation Rules
	 */
	function default_validation_rules()
	{
		// default rules
		$rules = array(
			array(
				'field' => $this->model->primary_key,
				'rules' => 'required|callback_id_check'
			)
		);

		// set to update validation rules
		$this->update_validation_rules = $rules;

		// set to delete_validation_rules
		$this->delete_validation_rules = $rules;
	}

	/**
	 * Id Checking
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function id_check($id, $model_name = false)
	{
		$tmp_model = $this->model;

		if ($model_name != false) {
			$tmp_model = $this->{$model_name};
		}

		if (!$tmp_model->is_exist($id)) {

			$this->form_validation->set_message('id_check', 'Invalid {field}');
			return false;
		}

		return true;
	}

	/**
	 * { function_description }
	 *
	 * @param      <type>   $conds  The conds
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function is_valid($rules)
	{
		if (empty($rules)) {
			// if rules is empty, no checking is required

			return true;
		}

		// GET data
		$user_data = array_merge($this->get(), $this->post(), $this->put());

		$this->form_validation->set_data($user_data);
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == FALSE) {
			// if there is an error in validating,

			$errors = $this->form_validation->error_array();

			if (count($errors) == 1) {
				// if error count is 1, remove '\n'

				$this->error_response(trim(validation_errors()), 400);
			}

			$this->error_response(validation_errors(), 400);
		}

		return true;
	}

	/**
	 * Returns default condition like default order by
	 * @return array custom_condition_array
	 */
	function default_conds()
	{
		return array();
	}

	/**
	 * Get all or Get One
	 */
	function get_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get id
		$id = $this->get('id');

		if ($id) {

			// if 'id' is existed, get one record only
			$data = $this->model->get_one($id);

			if (isset($data->is_empty_object)) {
				// if the id is not existed in the return object, the object is empty

				$data = array();
			}

			$this->custom_response($data);
		}

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (count($conds) == 0) {
			// if 'id' is not existed, get all	

			if (!empty($limit) && !empty($offset)) {
				// if limit & offset is not empty

				$data = $this->model->get_all($limit, $offset)->result();
			} else if (!empty($limit)) {
				// if limit is not empty

				$data = $this->model->get_all($limit)->result();
			} else {
				// if both are empty

				$data = $this->model->get_all()->result();
			}

			$this->custom_response($data, $offset);
		} else {

			if (!empty($limit) && !empty($offset)) {
				// if limit & offset is not empty

				$data = $this->model->get_all_by($conds, $limit, $offset)->result();
			} else if (!empty($limit)) {
				// if limit is not empty

				$data = $this->model->get_all_by($conds, $limit)->result();
			} else {
				// if both are empty

				$data = $this->model->get_all_by($conds)->result();
			}

			$this->custom_response($data, $offset);
		}
	}

	/**
	 * Get all or Get One
	 */
	function get_favourite_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);
		$conds['user_id'] = $this->get_login_user_id();

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			$data = $this->model->get_item_favourite($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->get_item_favourite($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->model->get_item_favourite($conds)->result();
		}

		$this->custom_response($data, $offset);
	}

	/**
	 * Get Like by user_id
	 */
	function get_like_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);
		$conds['user_id'] = $this->get('user_id');

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			$data = $this->model->get_item_like($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->get_item_like($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->model->get_item_like($conds)->result();
		}

		$this->custom_response($data, $offset);
	}

	function trending_category_get()
	{
		// add flag for default query
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			$data = $this->model->get_all_trending_category($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->get_all_trending_category($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->model->get_all_trending_category($conds)->result();
		}

		$this->custom_response($data, $offset);
	}

	function related_item_trending_get()
	{
		// add flag for default query
		$this->is_get = true;

		$current_item_id = $this->get('id');
		$current_cat_id 	= $this->get('cat_id');


		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			$data = $this->model->get_all_related_item_trending($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->model->get_all_related_item_trending($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->model->get_all_related_item_trending($conds)->result();
		}

		$this->custom_response($data, $offset);
	}

	/**
	 * Search API
	 */
	function search_post()
	{
		// add flag for default query
		$this->is_search = true;

		// add default conds
		$default_conds = $this->default_conds();
		$user_conds = $this->get();

		$conds = array_merge($default_conds, $user_conds);
		// check empty condition
		$final_conds = array();
		foreach ($conds as $key => $value) {

			if ($key != "item_status_id") {
				if (!empty($value)) {
					$final_conds[$key] = $value;
				}
			}

			if ($key == "item_status_id") {
				$final_conds[$key] = $value;
			}
		}
		$conds = $final_conds;

		$limit = $this->get('limit');
		$offset = $this->get('offset');
		//for item search
		if ($conds['item_search'] == 1) {

			if ($conds['is_paid'] == "only_paid_item") {
				$conds['is_paid'] = 1;

				if (!empty($limit) && !empty($offset)) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_paid($conds, $limit, $offset)->result();
				} else if (!empty($limit)) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_paid($conds, $limit)->result();
				} else {
					// if both are empty
					$data = $this->model->get_all_item_by_paid($conds)->result();
				}
			} elseif ($conds['is_paid'] == "paid_item_first") {
				$result = "";

				// $paid_date = $this->model->get_all_paid_item_finished( )->result();
				// // print_r($paid_date);die;
				// foreach ($paid_date as $paid) {
				//   	//update finished paid item is_paid=0 in item
				//   	$today_date = date('Y-m-d h:m:s');
				//   	$upt_result = $this->Paid_item->get_one($paid->id);
				//  	if ($upt_result != "") {
				//  		if ($today_date > $upt_result->start_date && $today_date > $upt_result->end_date) {

				// 	  		$upt_item = array(
				// 	  			"is_paid" => 0
				// 	  		);
				// 	  		$this->Item->save($upt_item,$paid->id);
				// 	  	}
				//  	}

				// }
				// //for today date paid item
				// $tmp_today_date = $this->Paid_item->get_all_item_by_paid( )->result();

				// foreach ($tmp_today_date as $tday) {
				// 	$result .= $tday->id .",";
				// }
				// $item_id_from_his = rtrim($result,",");
				// $today_date_id = explode(",", $item_id_from_his);
				// $tmp_date_result = $this->Paid_item->get_not_today_date($today_date_id)->result();
				// foreach ($tmp_date_result as $tmp) {

				//   	$tmp_result .= $tmp->item_id .",";

				// }
				// $paid_his_item_id = rtrim($tmp_result,",");
				// $not_today_date = explode(",", $paid_his_item_id);

				if (!empty($limit) && !empty($offset)) {
					// if limit & offset is not empty
					$data = $this->model->get_all_item_by_paid_date($conds, $limit, $offset)->result();
				} else if (!empty($limit)) {
					// if limit is not empty
					$data = $this->model->get_all_item_by_paid_date($conds, $limit)->result();
				} else {
					// if both are empty
					$data_paid = $this->model->get_all_item_by_paid_date($conds)->result();
				}
			} else {
				if (!empty($limit) && !empty($offset)) {
					// if limit & offset is not empty
					$data = $this->model->get_all_by($conds, $limit, $offset)->result();
				} else if (!empty($limit)) {
					// if limit is not empty
					$data = $this->model->get_all_by($conds, $limit)->result();
				} else {
					// if both are empty
					$data = $this->model->get_all_by($conds)->result();
				}
			}
		} else {
			if (!empty($limit) && !empty($offset)) {
				// if limit & offset is not empty

				$data = $this->model->get_all_by($conds, $limit, $offset)->result();
			} else if (!empty($limit)) {
				// if limit is not empty
				$data = $this->model->get_all_by($conds, $limit)->result();
			} else {
				// if both are empty
				$data = $this->model->get_all_by($conds)->result();
			}
		}

		$this->custom_response($data);
	}

	//Item_count
	function get_collection_id()
	{

		$collection_id = $this->get('collection_id');

		return $collection_id;
	}


	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response_noti($data, $require_convert = true)
	{
		if (empty($data)) {
			// if there is no data, return error

			$this->error_response(get_msg('record_not_found'));
		} else if ($require_convert) {
			// if there is data, return the list
			if (is_array($data)) {
				// if the data is array
				foreach ($data as $obj) {
					// convert object for each obj
					if ($this->get_login_user_id() != "") {
						$noti_user_data = array(
							"noti_id" => $obj->id,
							"user_id" => $this->get_login_user_id(),
							"device_token" => $this->post('device_token')
						);
						if (!$this->Notireaduser->exists($noti_user_data)) {
							$obj->is_read = 88;
						} else {
							$obj->is_read = 100;
						}
					}

					$this->convert_object($obj);
				}
			} else {
				if ($this->get_login_user_id() != "") {
					$noti_user_data = array(
						"noti_id" => $data->id,
						"user_id" => $this->get_login_user_id(),
						"device_token" => $this->post('device_token')
					);
					if (!$this->Notireaduser->exists($noti_user_data)) {
						$data->is_read = 99;
					} else {
						$data->is_read = 100;
					}
				}

				$this->convert_object($data);
			}
		}
		$data = $this->ps_security->clean_output($data);



		$this->response($data);
	}

	/**
	 * Adds a post.
	 */
	function add_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get the post data
		$data = $this->post();
		if (!$this->model->save($data)) {
			$this->error_response(get_msg('err_model'), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one($data[$this->model->primary_key]);

		$this->custom_response($obj);
	}

	/**
	 * Adds a post.
	 */
	function item_submit_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get the post data
		$data = $this->post();
		$data['item_status_id'] = '2';
		if (!$this->model->save($data)) {
			$this->error_response(get_msg('err_model'), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one($data[$this->model->primary_key]);

		$this->custom_response($obj);
	}

	/**
	 * Adds a post.
	 */
	function add_rating_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get the post data
		$data = $this->post();
		$user_id = $data['user_id'];

		$conds['user_id'] = $user_id;
		$conds['item_id'] = $data['item_id'];

		$id = $this->model->get_one_by($conds)->id;

		$rating = $data['rating'];
		if ($id) {

			$this->model->save($data, $id);

			// response the inserted object	
			$obj = $this->model->get_one($id);
		} else {
			$this->model->save($data);

			// response the inserted object	
			$obj = $this->model->get_one($data[$this->model->primary_key]);
		}

		//Need to update rating value at wallpaper
		$conds_rating['item_id'] = $obj->item_id;

		$total_rating_count = $this->Rate->count_all_by($conds_rating);
		$sum_rating_value = $this->Rate->sum_all_by($conds_rating)->result()[0]->rating;

		if ($total_rating_count > 0) {
			$total_rating_value = number_format((float) ($sum_rating_value  / $total_rating_count), 1, '.', '');
		} else {
			$total_rating_value = 0;
		}

		$item_data['overall_rating'] = $total_rating_value;
		$this->Item->save($item_data, $obj->item_id);


		$obj_rating = $this->Rate->get_one($obj->id);

		$this->ps_adapter->convert_rating($obj_rating);
		$this->custom_response($obj_rating);
	}


	/**
	 * Adds a post.
	 */
	function update_put()
	{
		// set the add flag for custom response
		$this->is_update = true;

		if (!$this->is_valid($this->update_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get the post data
		$data = $this->put();

		// get id
		$id = $this->get($this->model->primary_key);

		if (!$this->model->save($data, $id)) {
			// error in saving, 

			$this->error_response(get_msg('err_model'), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one($id);

		$this->custom_response($obj);
	}

	/**
	 * Delete the record
	 */
	function delete_delete()
	{
		// set the add flag for custom response
		$this->is_delete = true;

		if (!$this->is_valid($this->delete_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get id
		$id = $this->get($this->model->primary_key);

		if (!$this->model->delete($id)) {
			// error in saving, 

			$this->error_response(get_msg('err_model'), 500);
		}

		$this->success_response(get_msg('success_delete'), 200);
	}

	/**
	 * Get Delete Wallpaper By Date Range.
	 */
	function get_delete_item_post()
	{
		$start = $this->post('start_date');
		$end   = $this->post('end_date');

		$conds['start_date'] = $start;
		$conds['end_date']   = $end;


		$deleted_item_ids = $this->Item_delete->get_all_by($conds)->result();

		$this->custom_response($deleted_item_ids, false);
	}

	/**
	 * Convert Object
	 */
	function city_search_post()
	{
		// add flag for default query
		$this->is_search = true;

		// add default conds
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		// check empty condition
		$final_conds = array();
		foreach ($conds as $key => $value) {
			if (!empty($value)) {
				$final_conds[$key] = $value;
			}
		}
		$conds = $final_conds;
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty

			if ($conds['order_by'] == "touch_count") {

				$data = $this->model->get_city_by_touch_userid($conds, $limit, $offset)->result();
			} else if ($conds['order_by'] == "added_date") {

				$data = $this->model->get_all_by($conds, $limit)->result();
			} else {

				$data = $this->model->get_all_by($conds, $limit, $offset)->result();
			}
		} else if (!empty($limit)) {
			// if limit is not empty

			if ($conds['order_by'] == "touch_count") {

				$data = $this->model->get_city_by_touch_userid($conds, $limit)->result();
			} else if ($conds['order_by'] == "added_date") {

				$data = $this->model->get_all_by($conds, $limit)->result();
			} else {

				$data = $this->model->get_all_by($conds, $limit)->result();
			}
		} else {
			// if both are empty
			if ($conds['order_by'] == "touch_count") {

				$data = $this->model->get_city_by_touch_userid($conds)->result();
			} else if ($conds['order_by'] == "added_date") {

				$data = $this->model->get_all_by($conds, $limit)->result();
			} else {

				$data = $this->model->get_all_by($conds)->result();
			}
		}

		$this->custom_response($data);
	}

	function get_token_get()
	{

		$payment_info = $this->Paid_config->get_one('pconfig1');

		$environment = $payment_info->paypal_environment;
		$merchantId  = $payment_info->paypal_merchant_id;
		$publicKey   = $payment_info->paypal_public_key;
		$privateKey  = $payment_info->paypal_private_key;


		//echo ">>" . $environment . " - " . $merchantId . " - " . $publicKey . " - " . $privateKey; die;

		$gateway = new Braintree_Gateway([
			'environment' => $environment,
			'merchantId' => $merchantId,
			'publicKey' => $publicKey,
			'privateKey' => $privateKey
		]);

		$clientToken = $gateway->clientToken()->generate();

		//$this->custom_response( $clientToken );

		if ($clientToken != "") {
			$this->response(array(
				'status' => 'success',
				'message' => $clientToken
			));
		} else {
			$this->error_response(get_msg('token_not_round'));
		}
	}

	function submit_items_post()
	{
		$approval_enable = $this->App_setting->get_one('app1')->is_approval_enabled;
		if ($approval_enable == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		$user_id = $this->post('user_id');
		$id = $this->post('id');

		if ($id == "") {
			//Need to save inside item table 
			$item_info = array(
				'cat_id'						=> $this->post('cat_id'),
				'sub_cat_id' 					=> $this->post('sub_cat_id'),
				'item_status_id'				=> $status,
				'name' 							=> $this->post('name'),
				'description' 					=> $this->post('description'),
				'search_tag' 					=> $this->post('search_tag'),
				'highlight_information'			=> $this->post('highlight_information'),
				'is_featured' 		    		=> $this->post('is_featured'),
				'lat'							=> $this->post('lat'),
				'lng'							=> $this->post('lng'),
				'opening_hour'					=> $this->post('opening_hour'),
				'closing_hour'					=> $this->post('closing_hour'),
				'is_promotion'					=> $this->post('is_promotion'),
				'phone1'						=> $this->post('phone1'),
				'phone2'						=> $this->post('phone2'),
				'phone3'						=> $this->post('phone3'),
				'email'							=> $this->post('email'),
				'address'						=> $this->post('address'),
				'facebook'						=> $this->post('facebook'),
				'google_plus'					=> $this->post('google_plus'),
				'twitter'						=> $this->post('twitter'),
				'youtube'						=> $this->post('youtube'),
				'instagram'						=> $this->post('instagram'),
				'pinterest'						=> $this->post('pinterest'),
				'website'						=> $this->post('website'),
				'whatsapp'						=> $this->post('whatsapp'),
				'messenger'						=> $this->post('messenger'),
				'time_remark'					=> $this->post('time_remark'),
				'terms'							=> $this->post('terms'),
				'cancelation_policy'			=> $this->post('cancelation_policy'),
				'additional_info'				=> $this->post('additional_info'),
				'added_user_id'					=> $user_id
			);
			$lat = $this->post('lat');
			$lng = $this->post('lng');
			$location = location_check($lat, $lng);

			$item_id = false;

			if (!$this->Item->save($item_info)) {
				// rollback the transaction
				$this->error_response(get_msg('err_model'), 500);
			}

			$item_id = $item_info['id'];


			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->error_response(get_msg('err_model'), 500);
			} else {
				$this->db->trans_commit();
			}

			$item_info_obj = $this->Item->get_one($item_id);

			$item_info_obj->login_user_id_post = $user_id;


			$this->custom_response($item_info_obj);
		} else {
			//Need to save inside item table 
			$item_info = array(
				'cat_id'						=> $this->post('cat_id'),
				'sub_cat_id' 					=> $this->post('sub_cat_id'),
				'item_status_id'				=> $this->post('status'),
				'name' 							=> $this->post('name'),
				'description' 					=> $this->post('description'),
				'search_tag' 					=> $this->post('search_tag'),
				'highlight_information'			=> $this->post('highlight_information'),
				'is_featured' 		    		=> $this->post('is_featured'),
				'lat'							=> $this->post('lat'),
				'lng'							=> $this->post('lng'),
				'opening_hour'					=> $this->post('opening_hour'),
				'closing_hour'					=> $this->post('closing_hour'),
				'is_promotion'					=> $this->post('is_promotion'),
				'phone1'						=> $this->post('phone1'),
				'phone2'						=> $this->post('phone2'),
				'phone3'						=> $this->post('phone3'),
				'email'							=> $this->post('email'),
				'address'						=> $this->post('address'),
				'facebook'						=> $this->post('facebook'),
				'google_plus'					=> $this->post('google_plus'),
				'twitter'						=> $this->post('twitter'),
				'youtube'						=> $this->post('youtube'),
				'instagram'						=> $this->post('instagram'),
				'pinterest'						=> $this->post('pinterest'),
				'website'						=> $this->post('website'),
				'whatsapp'						=> $this->post('whatsapp'),
				'messenger'						=> $this->post('messenger'),
				'time_remark'					=> $this->post('time_remark'),
				'terms'							=> $this->post('terms'),
				'cancelation_policy'			=> $this->post('cancelation_policy'),
				'additional_info'				=> $this->post('additional_info'),
				'added_user_id'					=> $user_id,
				'id'							=> $this->post('id')
			);

			$item_id = false;

			$id = $item_info['id'];

			if (!$this->Item->save($item_info, $id)) {
				// rollback the transaction
				$this->error_response(get_msg('err_model'), 500);
			}

			$item_id = $item_info['id'];


			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->error_response(get_msg('err_model'), 500);
			} else {
				$this->db->trans_commit();
			}

			$item_info_obj = $this->Item->get_one($item_id);

			$item_info_obj->login_user_id_post = $user_id;


			$this->custom_response($item_info_obj);
		}
	}

	/**
	 * Adds a post.
	 */
	function add_touch_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		// get the post data
		$data = $this->post();
		if (!$this->model->save($data)) {
			$this->error_response(get_msg('err_model'), 500);
		}

		// response the inserted object	
		$obj = $this->model->get_one($data[$this->model->primary_key]);

		if ($obj->type_name == "item") {
			//Need to update touch count value at product
			$conds_rating['type_id'] = $obj->type_id;

			$total_touch_count = $this->Touch->count_all_by($conds_rating);

			$item_data['touch_count'] = $total_touch_count;
			$this->Item->save($item_data, $obj->type_id);
		} else if ($obj->type_name == "category") {
			//Need to update touch count value at category
			$conds_rating['type_id'] = $obj->type_id;

			$total_touch_count = $this->Touch->count_all_by($conds_rating);

			$cat_data['touch_count'] = $total_touch_count;
			$this->Category->save($cat_data, $obj->type_id);
		} else {
			//Need to update touch count value at shop
			$conds_rating['type_id'] = $obj->type_id;

			$total_touch_count = $this->Touch->count_all_by($conds_rating);

			$city_data['touch_count'] = $total_touch_count;
			$this->City->save($city_data, $obj->type_id);
		}
		$this->custom_response($obj);
	}


	/**
	 * Get Delete History By Date Range.
	 */
	function get_delete_history_post()
	{


		$start = $this->post('start_date');
		$end   = $this->post('end_date');
		$user_id = $this->post('user_id');

		$conds['start_date'] = $start;
		$conds['end_date']   = $end;

		$conds['order_by'] = 1;
		$conds['order_by_field'] = "type_name";
		$conds['order_by_type'] = "desc";


		//$deleted_his_ids = $this->Delete_history->get_all_history_by($conds)->result();
		$deleted_his_ids = $this->Delete_history->get_all_by($conds)->result();

		$this->custom_response_history($deleted_his_ids, $user_id, false);
	}


	/**
	 * Custome Response return 404 if not data found
	 *
	 * @param      <type>  $data   The data
	 */
	function custom_response_history($data, $user_id, $require_convert = true)
	{


		$version_object = new stdClass;
		$version_object->version_no           = $this->Version->get_one("1")->version_no;
		$version_object->version_force_update = $this->Version->get_one("1")->version_force_update;
		$version_object->version_title        = $this->Version->get_one("1")->version_title;
		$version_object->version_message      = $this->Version->get_one("1")->version_message;
		$version_object->version_need_clear_data      = $this->Version->get_one("1")->version_need_clear_data;
		$is_banned = $this->User->get_one($user_id)->is_banned;
		$user_status = $this->User->get_one($user_id)->status;
		$user_object = new stdClass;

		$user_data = $this->User->get_one($user_id);
		//($user_data->status);die;

		if ($user_id == "nologinuser") {
			$user_object->user_status = "nologinuser";
		} elseif ($user_data->is_empty_object == 1) {
			$user_object->user_status = "deleted";
		} elseif ($is_banned == 1) {
			$user_object->user_status = "banned";
		} elseif ($user_status == 1) {
			$user_object->user_status = "active";
		} elseif ($user_status == 2) {
			$user_object->user_status = "pending";
		} elseif ($user_status == 0) {
			$user_object->user_status = "unpublished";
		}

		// for android type at in app purchase
		$conds_android['type'] = "Android";
		$conds_android['status'] = "1";
		$app_purchased_count_android = $this->In_app_purchase->count_all_by($conds_android);

		if ($conds_android['type'] = "Android") {
			for ($i = 0; $i <  $app_purchased_count_android; $i++) {

				$app_purchased_data_android = $this->In_app_purchase->get_all_by($conds_android)->result();

				$in_app_purchased_prd_id_android .= "" . $app_purchased_data_android[$i]->in_app_purchase_prd_id . "@@" . $app_purchased_data_android[$i]->day .  "##";
			}
		}

		// for ios type at in app purchase
		$conds_ios['type'] = "IOS";
		$conds_ios['status'] = "1";
		$app_purchased_count_ios = $this->In_app_purchase->count_all_by($conds_ios);

		if ($conds_ios['type'] = "IOS") {
			for ($i = 0; $i <  $app_purchased_count_ios; $i++) {
				$app_purchased_data_ios = $this->In_app_purchase->get_all_by($conds_ios)->result();
				//print_r($app_purchased_data[0]->id);die;
				$in_app_purchased_prd_id_ios .= "" . $app_purchased_data_ios[$i]->in_app_purchase_prd_id . "@@" . $app_purchased_data_ios[$i]->day .  "##";
			}
		}

		$final_data = new stdClass;
		$final_data->version = $version_object;
		$final_data->user_info = $user_object;
		$final_data->oneday = $this->Paid_config->get_one("pconfig1")->amount;
		$final_data->currency_symbol = $this->Paid_config->get_one("pconfig1")->currency_symbol;
		$final_data->currency_short_form = $this->Paid_config->get_one("pconfig1")->currency_short_form;
		$final_data->stripe_publishable_key = $this->Paid_config->get_one("pconfig1")->stripe_publishable_key;
		$final_data->stripe_enabled = $this->Paid_config->get_one("pconfig1")->stripe_enabled;
		$final_data->paypal_enabled = $this->Paid_config->get_one("pconfig1")->paypal_enabled;
		$final_data->razor_enabled = $this->Paid_config->get_one("pconfig1")->razor_enabled;
		$final_data->razor_key = $this->Paid_config->get_one("pconfig1")->razor_key;
		$final_data->paystack_enabled = $this->Paid_config->get_one("pconfig1")->paystack_enabled;
		$final_data->paystack_key = $this->Paid_config->get_one("pconfig1")->paystack_key;
		$final_data->in_app_purchased_enabled = $this->Paid_config->get_one("pconfig1")->in_app_purchased_enabled;
		$final_data->in_app_purchased_prd_id_android = $in_app_purchased_prd_id_android;
		$final_data->in_app_purchased_prd_id_ios = $in_app_purchased_prd_id_ios;
		$final_data->delete_history = $data;


		$final_data = $this->ps_security->clean_output($final_data);


		$this->response($final_data);
	}

	/** Get City Information */

	function get_city_info_get($conds = array(), $limit = false, $offset = false)
	{

		$this->is_get = true;

		// get id
		$id = $this->get('id');

		if (!$id) {
			$city = $this->model->get_all()->result();
			$city_id = $city[0]->id;
			$data = $this->model->get_one($city_id);
		}
		$this->custom_response($data, $offset);
	}

	/** Add Item Specification */

	function add_spec_post()
	{
		// set the add flag for custom response
		$this->is_add = true;

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		$data = $this->post();
		//print_r($data['id']."DFds");die;
		$id = $data['id'];

		if ($id == "") {

			$this->Specification->save($data);
		} else {
			$this->Specification->save($data, $id);
		}

		$conds['id'] = $data['id'];
		$spec_data = $this->Specification->get_one_by($conds);
		$this->response($spec_data);
	}

	/* Get Specification by item_id */

	function item_specification_get($conds = array(), $limit = false, $offset = false)
	{
		$this->is_get = true;

		// get limit & offset
		$limit = $this->get('limit');
		$offset = $this->get('offset');

		// get search criteria
		$default_conds = $this->default_conds();
		$user_conds = $this->get();
		$conds = array_merge($default_conds, $user_conds);

		if ($limit) {
			unset($conds['limit']);
		}

		if ($offset) {
			unset($conds['offset']);
		}

		if (!empty($limit) && !empty($offset)) {
			// if limit & offset is not empty
			$data = $this->Specification->get_all_by($conds, $limit, $offset)->result();
		} else if (!empty($limit)) {
			// if limit is not empty

			$data = $this->Specification->get_all_by($conds, $limit)->result();
		} else {
			// if both are empty
			$data = $this->Specification->get_all_by($conds)->result();
		}
		//print_r($data);die;
		if (empty($data)) {

			$this->error_response(get_msg('record_not_found'), 404);
		} else {
			$this->response($data, $offset);
		}
	}

	/** Delete Item Specification */

	function delete_spec_post()
	{

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		$item_id = $this->post('item_id');
		$id = $this->post('id');

		// prep data
		$data = array('item_id' => $item_id, 'id' => $id);

		if (!$this->Specification->delete_by($data)) {
			$this->error_response(get_msg('err_model'), 500);
		} else {
			$this->success_response(get_msg('success_delete_spec'), 200);
		}
	}

	/** get item status id */

	function item_status_get($conds = array())
	{

		$this->is_get = true;
		$data = $this->Itemstatus->get_one_status($conds)->result();
		$this->response($data, $offset);
	}

	/** Delete Item Specification */

	function item_delete_post()
	{

		if (!$this->is_valid($this->create_validation_rules)) {
			// if there is an error in validation,

			return;
		}

		$id = $this->post('item_id');
		$added_user_id = $this->post('user_id');

		// prep data
		$data = array('id' => $id, 'added_user_id' => $added_user_id);

		$item_data['item_id'] = $data['id'];
		$touch_data['type_id'] = $data['id'];
		$img_data['img_parent_id'] = $data['id'];

		$this->Commentheader->delete_by($item_data);
		$this->Favourite->delete_by($item_data);
		$this->Itemcollection->delete_by($item_data);
		$this->Itemreport->delete_by($item_data);
		$this->Specification->delete_by($item_data);
		$this->Rate->delete_by($item_data);
		$this->Touch->delete_by($touch_data);
		$this->Image->delete_by($img_data);

		if (!$this->Item->delete_by($data)) {
			$this->error_response(get_msg('err_model'), 500);
		} else {
			$this->success_response(get_msg('success_delete_item'), 200);
		}
	}
}
