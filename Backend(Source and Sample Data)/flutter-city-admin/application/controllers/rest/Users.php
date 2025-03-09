<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for Users
 */
class Users extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct('User');
	}

	/**
	 * Convert Object
	 */
	function convert_object(&$obj)
	{
		// call parent convert object
		parent::convert_object($obj);

		// convert customize category object
		$this->ps_adapter->convert_user($obj);
	}

	/**
	 * Users Registration
	 */
	function add_post()
	{
		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_name',
				'rules' => 'required'
			),
			array(
				'field' => 'user_email',
				'rules' => 'required|valid_email|callback_email_check'
			),
			array(
				'field' => 'user_password',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;
		$email_verified_enable = $this->Backend_config->get_one('be1')->email_verification_enabled;
		$code = generate_random_string(5);

		if ($email_verified_enable != 1) {

			$user_data = array(
				"user_name" 	=> $this->post('user_name'),
				"user_email"    => $this->post('user_email'),
				"user_password" => md5($this->post('user_password')),
				"device_token"  => $this->post('device_token'),
				"code" =>  '',
				"email_verify" => 1,
				"status" => 1, //Need to verified status
				"added_date" =>  date("Y-m-d H:i:s")

			);
		} else {

			$user_data = array(
				"user_name" 	=> $this->post('user_name'),
				"user_email"    => $this->post('user_email'),
				"user_password" => md5($this->post('user_password')),
				"device_token"  => $this->post('device_token'),
				"code" =>  $code,
				"email_verify" => 1,
				"status" => 2,
				"added_date" =>  date("Y-m-d H:i:s")
			);
			$conds['status'] = 2;
		}


		$conds['user_email'] = $user_data['user_email'];

		$user_infos = $this->User->user_exists($conds)->result();

		if (empty($user_infos)) {
			// user email is not exist
			if (!$this->User->save($user_data)) {

				$this->error_response(get_msg('err_user_register'), 500);
			} else {

				$noti_token = array(
					"device_id" => $user_data['device_token']
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_data['user_id'],
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$subject = get_msg('user_acc_reg_label');


				if ($email_verified_enable != 1) {
					if (!send_user_register_email_without_verify($user_data['user_id'], $subject)) {

						$this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
					}
				} else {
					if (!send_user_register_email($user_data['user_id'], $subject)) {

						$this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
					}
				}
			}
		} else {
			// user email already existed and but not verify

			$user_id = $user_infos[0]->user_id;
			$subject = get_msg('user_acc_reg_label');

			if ($email_verified_enable != 1) {
				if (!send_user_register_email_without_verify($user_id, $subject)) {

					$this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
				}
			} else {
				if (!send_user_register_email($user_id, $subject)) {

					$this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
				}
			}

			$this->custom_response($this->User->get_one($user_id));
		}

		$this->custom_response($this->User->get_one($user_data["user_id"]));
	}

	/**
	 * Users Registration with Facebook
	 */
	function facebook_register_post()
	{
		$rules = array(
			array(
				'field' => 'facebook_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		//Need to check facebook_id is aleady exist or not?
		if (!$this->User->exists(array('facebook_id' => $this->post('facebook_id')))) {

			$rules = array(
				array(
					'field' => 'user_name',
					'rules' => 'required'
				)
			);

			//User not yet exist 
			$fb_id = $this->post('profile_img_id');
			$url = "https://graph.facebook.com/$fb_id/picture?width=350&height=500";

			// for uploads 

			$data = file_get_contents($url);
			$dir = "uploads/";
			$img = md5(time()) . '.jpg';
			$ch = curl_init($url);
			$fp = fopen('uploads/' . $img, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);


			//for thumbnail
			$dir = "uploads/thumbnail/";
			$ch = curl_init($url);
			$fp = fopen('uploads/thumbnail/' . $img, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			////

			$user_data = array(
				"user_name" 	=> $this->post('user_name'),
				'user_email'    => $this->post('user_email'),
				"facebook_id" 	=> $this->post('facebook_id'),
				"user_profile_photo" => $img,
				"device_token" => $this->post('device_token'),
				"role_id"	=> 4,
				"facebook_verify" => 1,
				"status" 	=> 1,
				"code"    => ' ',
				"added_date" =>  date("Y-m-d H:i:s")
			);


			$user_email = $user_data['user_email'];
			//print_r($user_email);die;

			if ($user_data['user_email'] != "") {
				//email exists
				$cond_user_existed['user_email'] = $user_data['user_email'];
				$cond_user_existed['phone_id'] = "";
				$user_infos = $this->User->get_email_phone($cond_user_existed)->result();
				$user_id = $user_infos[0]->user_id;
			}

			if ($user_id != "") {
				//user email alerady exist

				$user_name = $this->post('user_name');
				$user_email = $this->post('user_email');

				if ($user_name == "" && $user_email == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"facebook_id" 	=> $user_data['facebook_id'],
						"facebook_verify" => $user_data['facebook_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_name == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"facebook_id" 	=> $user_data['facebook_id'],
						"facebook_verify" => $user_data['facebook_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_email == "") {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"facebook_id" 	=> $user_data['facebook_id'],
						"facebook_verify" => $user_data['facebook_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"facebook_id" 	=> $user_data['facebook_id'],
						"facebook_verify" => $user_data['facebook_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				}
				$this->User->save($user_data, $user_id);

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(

							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(

						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')

					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			} else {
				//user email not exist

				if (!$this->User->save($user_data)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(

							"user_id" => $user_data['user_id'],
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_data['user_id'],
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$this->custom_response($this->User->get_one($user_data['user_id']));
			}

			$this->custom_response($this->User->get_one($user_infos[0]->user_id));
		} else {

			//User already exist in DB
			$conds['facebook_id'] = $this->post('facebook_id');
			$conds1['facebook_id'] = $this->post('facebook_id');
			$user_profile_data = $this->User->get_one_by($conds);
			$user_profile_photo = $user_profile_data->user_profile_photo;

			//Delete existing image 
			@unlink('./uploads/' . $user_profile_photo);
			@unlink('./uploads/thumbnail/' . $user_profile_photo);

			//Download again
			$fb_id = $this->post('profile_img_id');
			$url = "https://graph.facebook.com/$fb_id/picture?width=350&height=500";

			// for uploads

			$data = file_get_contents($url);
			$dir = "uploads/";
			$img = md5(time()) . '.jpg';
			$ch = curl_init($url);
			$fp = fopen('uploads/' . $img, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			// for thumbnail 

			$dir = "uploads/thumbnail/";
			$ch = curl_init($url);
			$fp = fopen('uploads/thumbnail/' . $img, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			$user_data = array(
				'user_name'    	=> $this->post('user_name'),
				'user_email'    => $this->post('user_email'),
				'user_profile_photo' => $img,
				'device_token'  => $this->post('device_token')
			);

			//for user name and user email
			$user_name = $this->post('user_name');
			$user_email = $this->post('user_email');

			if ($user_name == "" && $user_email == "") {
				$user_data = array(
					'device_token'  => $this->post('device_token'),
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else if ($user_name == "") {
				$user_data = array(
					'user_email'    => $user_data['user_email'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else if ($user_email == "") {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'user_email'    => $user_data['user_email'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			}


			$users_data = $this->User->get_one_by($conds1);
			$user_id = $users_data->user_id;

			$conds['facebook_id'] = $this->post('facebook_id');
			$user_datas = $this->User->get_one_by($conds);
			$user_id = $user_datas->user_id;

			if ($user_datas->is_banned == 1) {

				$this->error_response(get_msg('err_user_banned'), 500);
			} else {

				if (!$this->User->save($user_data, $user_id)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			}

			$this->custom_response($this->User->get_one($user_datas->user_id));
		}
	}

	/**
	 * Users Registration with Google
	 */
	function google_register_post()
	{
		$rules = array(
			array(
				'field' => 'google_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		//Need to check google_id is aleady exist or not?
		if (!$this->User->exists(
			array(
				'google_id' => $this->post('google_id')
			)
		)) {

			$rules = array(
				array(
					'field' => 'user_name',
					'rules' => 'required'
				)
			);

			//User not yet exist 
			$gg_id = $this->post('google_id');
			$url = $this->post('profile_photo_url');

			if ($url != "") {

				// for upload

				$data = file_get_contents($url);
				$dir = "uploads/";
				$img = md5(time()) . '.jpg';
				$ch = curl_init($url);
				$fp = fopen('uploads/' . $img, 'wb');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				// for thumbnail

				$dir = "uploads/thumbnail/";
				$ch = curl_init($url);
				$fp = fopen('uploads/thumbnail/' . $img, 'wb');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				$user_data = array(
					"user_name" 	=> $this->post('user_name'),
					'user_email'    => $this->post('user_email'),
					"google_id" 	=> $this->post('google_id'),
					"user_profile_photo" => $img,
					"device_token" => $this->post('device_token'),
					"role_id" => 4,
					"google_verify" => 1,
					"status" 	=> 1,
					"code"   => ' ',
					"added_date" =>  date("Y-m-d H:i:s")
				);
			} else {

				$user_data = array(
					"user_name" 	=> $this->post('user_name'),
					'user_email'    => $this->post('user_email'),
					"google_id" 	=> $this->post('google_id'),
					"device_token" => $this->post('device_token'),
					"role_id" => 4,
					"google_verify" => 1,
					"status" 	=> 1,
					"code"   => ' ',
					"added_date" =>  date("Y-m-d H:i:s")
				);
			}

			if ($user_data['user_email'] != "") {
				$cond_user_existed['user_email'] = $user_data['user_email'];
				$cond_user_existed['phone_id'] = "";
				$user_infos = $this->User->get_email_phone($cond_user_existed)->result();
				$user_id = $user_infos[0]->user_id;
			}
			if ($user_id != "") {
				//user email alerady exist

				//for user name and user email
				$user_name = $this->post('user_name');
				$user_email = $this->post('user_email');

				if ($user_name == "" && $user_email == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"google_id" 	=> $user_data['google_id'],
						"google_verify" => $user_data['google_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_name == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"google_id" 	=> $user_data['google_id'],
						"google_verify" => $user_data['google_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_email == "") {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"google_id" 	=> $user_data['google_id'],
						"google_verify" => $user_data['google_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"google_id" 	=> $user_data['google_id'],
						"google_verify" => $user_data['google_verify'],
						'user_profile_photo' => $user_data['user_profile_photo'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				}

				$this->User->save($user_data, $user_id);

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')


						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			} else {
				//user email not exist

				if (!$this->User->save($user_data)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_data['user_id'],
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$this->custom_response($this->User->get_one($user_data['user_id']));
			}

			$this->custom_response($this->User->get_one($user_infos[0]->user_id));
		} else {

			//User already exist in DB
			$conds['google_id'] = $this->post('google_id');
			$user_profile_data = $this->User->get_one_by($conds);
			$user_profile_photo = $user_profile_data->user_profile_photo;

			//Delete existing image 
			@unlink('./uploads/' . $user_profile_photo);
			@unlink('./uploads/thumbnail/' . $user_profile_photo);
			//Download again
			$fb_id = $this->post('google_id');
			$url = $this->post('profile_photo_url');

			if ($url != "") {

				// for upload

				$data = file_get_contents($url);
				$dir = "uploads/";
				$img = md5(time()) . '.jpg';
				$ch = curl_init($url);
				$fp = fopen('uploads/' . $img, 'wb');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				// for thumbnail

				$dir = "uploads/thumbnail/";
				$ch = curl_init($url);
				$fp = fopen('uploads/thumbnail/' . $img, 'wb');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);

				$user_data = array(
					'user_name'    	=> $this->post('user_name'),
					'user_email'    => $this->post('user_email'),
					'device_token'  => $this->post('device_token'),
					'user_profile_photo' => $img
				);
			} else {

				$user_data = array(
					'user_name'    	=> $this->post('user_name'),
					'user_email'    => $this->post('user_email'),
					'device_token'  => $this->post('device_token')
				);
			}

			//for user name and user email
			$user_name = $this->post('user_name');
			$user_email = $this->post('user_email');

			if ($user_name == "" && $user_email == "") {
				$user_data = array(
					'device_token'  => $this->post('device_token'),
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else if ($user_name == "") {
				$user_data = array(
					'user_email'    => $user_data['user_email'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else if ($user_email == "") {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			} else {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'user_email'    => $user_data['user_email'],
					'device_token'  => $user_data['device_token'],
					'user_profile_photo' => $user_data['user_profile_photo'],
				);
			}


			$conds['google_id'] = $this->post('google_id');
			$user_datas = $this->User->get_one_by($conds);
			$user_id = $user_datas->user_id;

			if ($user_datas->is_banned == 1) {

				$this->error_response(get_msg('err_user_banned'), 500);
			} else {

				if (!$this->User->save($user_data, $user_id)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			}

			$this->custom_response($this->User->get_one($user_datas->user_id));
		}
	}



	/**
	 * Email Checking
	 *
	 * @param      <type>  $email     The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function email_check($email)
	{

		if ($this->User->exists(array('user_email' => $email))) {

			$this->form_validation->set_message('email_check', 'Email Exist');
			return false;
		}

		return true;
	}

	/**
	 * Users Login
	 */
	function login_post()
	{
		// validation rules for user register
		$rules = array(

			array(
				'field' => 'user_email',
				'rules' => 'required|valid_email'
			),
			array(
				'field' => 'user_password',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		if ($this->User->exists(array('user_email' => $this->post('user_email'), 'user_password' => $this->post('user_password'), 'device_id' => $this->post('device_token')))) {

			$email = $this->post('user_email');
			$conds['user_email'] = $email;
			$is_banned = $this->User->get_one_by($conds)->is_banned;

			if ($is_banned == '1') {
				$this->error_response(get_msg('err_user_banned'), 500);
			} else {
				$user = $this->User->get_one_by(array("user_email" => $this->post('user_email')));
				$user_id = $user->user_id;
				
				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$this->custom_response($user);
			}
		} else {

			$this->error_response(get_msg('err_user_not_exist'), 404);
		}
	}

	/**
	 * User Reset Password
	 */
	function reset_post()
	{
		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_email',
				'rules' => 'required|valid_email'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$user_info = $this->User->get_one_by(array("user_email" => $this->post('user_email')));

		if (isset($user_info->is_empty_object)) {
			// if user info is empty,

			$this->error_response(get_msg('err_user_not_exist'), 404);
		}

		// generate code
		$code = md5(time() . 'teamps');

		// insert to reset
		$data = array(
			'user_id' => $user_info->user_id,
			'code' => $code
		);

		if (!$this->ResetCode->save($data)) {
			// if error in inserting,

			$this->error_response(get_msg('err_model'), 500);
		}

		// Send email with reset code
		$to = $user_info->user_email;
		$subject = get_msg('pwd_reset_label');
		$sender_name = $this->Backend_config->get_one('be1')->sender_name;
		$msg = "<p>" . $hi . "," . $user_info->user_name . "</p>" .
			"<p>" . get_msg('pwd_reset_link') . "<br/>" .
			"<a href='" . site_url($this->config->item('reset_url') . '/' . $code) . "'>" . get_msg('reset_link_label') . "</a></p>" .
			"<p>" . get_msg('best_regards_label') . ",<br/>" . $this->Backend_config->get_one('be1')->sender_name . "</p>";
		// send email from admin
		if (!$this->ps_mail->send_from_admin($to, $subject, $msg)) {

			$this->error_response(get_msg('err_email_not_send'), 500);
		}

		$this->success_response(get_msg('success_email_sent'), 200);
	}

	/**
	 * User Profile Update
	 */

	function profile_update_post()
	{

		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_id',
				'rules' => 'required'
			),
			array(
				'field' => 'user_name',
				'rules' => 'required'
			),
			array(
				'field' => 'user_email',
				'rules' => 'required|valid_email'
			),
			array(
				'field' => 'user_phone',
				'rules' => 'required'
			),

			array(
				'field' => 'user_about_me',
				'rules' => 'required'
			)
		);
		// print_r($rules);die;

		// exit if there is an error in validation,0
		if (!$this->is_valid($rules)) exit;

		$user_id = $this->post('user_id');
		// user email checking
		$user_email = $this->User->get_one($user_id)->user_email;
		if ($user_email == $this->post('user_email')) {
			$email = $this->post('user_email');
		} else {
			$conds['user_email'] = $this->post('user_email');
			$conds['status'] = 1;
			$user_infos = $this->User->get_one_user_email($conds)->result();
			if (empty($user_infos)) {
				$email = $this->post('user_email');
			} else {

				$this->error_response(get_msg('err_user_email_exist'), 409);
			}
		}

		// user phone checking
		$user_phone = $this->User->get_one($user_id)->user_phone;
		if ($user_phone == $this->post('user_phone')) {
			$phone = $this->post('user_phone');
		} else {
			$conds['user_phone'] = $this->post('user_phone');
			$conds['status'] = 1;
			$user_infos = $this->User->get_one_user_phone($conds)->result();
			if (empty($user_infos)) {
				$phone = $this->post('user_phone');
			} else {

				$this->error_response(get_msg('err_user_phone_exist'), 409);
			}
		}


		$user_data = array(
			"user_name"     => $this->post('user_name'),
			"user_email"    => $this->post('user_email'),
			"user_phone"    => $this->post('user_phone'),
			"user_address"  => $this->post('user_address'),
			"user_about_me" => $this->post('user_about_me'),

		);
		// print_r($user_data);die;

		if (!$this->User->save($user_data, $this->post('user_id'))) {

			$this->error_response(get_msg('err_user_update'), 500);
		}

		$this->success_response(get_msg('success_profile_update'), 201);
	}

	/**
	 * User Profile Update
	 */
	function password_update_post()
	{

		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_id',
				'rules' => 'required|callback_id_check[User]'
			),
			array(
				'field' => 'user_password',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$user_data = array(
			"user_password"     => md5($this->post('user_password'))
		);

		if (!$this->User->save($user_data, $this->post('user_id'))) {
			$this->error_response(get_msg('err_user_password_update'), 500);
		}

		$this->success_response(get_msg('success_profile_update'), 201);
	}

	/**
	 * User Verified Code
	 */
	function verify_post()
	{

		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_id',
				'rules' => 'required|callback_id_check[User]'
			),
			array(
				'field' => 'code',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$user_verify_data = array(
			"code"     => $this->post('code'),
			"user_id"  => $this->post('user_id'),
			"status"   => 2
		);

		$user_data = $this->User->get_one_user($user_verify_data)->result();

		foreach ($user_data as $user) {
			$user_id = $user->user_id;
			$code = $user->code;
		}

		if ($user_id  == $this->post('user_id')) {
			$user_data = array(
				"code"    => " ",
				"status"  => 1
			);
			$this->User->save($user_data, $user_id);
			$this->custom_response($this->User->get_one($user_id));
		} else {

			$this->error_response(get_msg('invalid_code'), 400);
		}
	}

	/**
	 * Users Request Code
	 */
	function request_code_post()
	{
		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_email',
				'rules' => 'required'
			)

		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		if (!$this->User->user_exists(array('user_email' => $this->post('user_email'), 'status' => 2))) {

			$this->error_response(get_msg('err_user_not_exist'), 404);
		} else {

			$email = $this->post('user_email');
			$conds['user_email'] = $email;
			$conds['status'] = 2;

			$user_data = $this->User->user_exists($conds)->result();

			foreach ($user_data as $user) {
				$user_id = $user->user_id;
				$code = $user->code;
			}

			if ($code == " ") {

				$resend_code = generate_random_string(5);
				$user_data_code = array(
					"code"    => $resend_code
				);
				$this->User->save($user_data_code, $user_id);
			}


			$user_data['user_id'] = $user_id;
			//print_r($user_data);die;

			$subject = get_msg('verify_code_sent');

			if (!send_user_register_email($user_data['user_id'], $subject)) {

				$this->error_response(get_msg('user_register_success_but_email_not_send'), 503);
			}

			$this->success_response(get_msg('success_email_sent'), 200);
		}
	}

	/**
	 * Users Registration with Phone
	 */
	function phone_register_post()
	{
		$rules = array(
			array(
				'field' => 'phone_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		//Need to check phone_id is aleady exist or not?
		if (!$this->User->exists(
			//new
			array(
				'phone_id' => $this->post('phone_id')
			)
		)) {

			$rules = array(
				array(
					'field' => 'user_name',
					'rules' => 'required'
				)
			);

			$user_data = array(
				"user_name" 	=> $this->post('user_name'),
				"user_phone"    => $this->post('user_phone'),
				"phone_id" 	   => $this->post('phone_id'),
				"device_token" => $this->post('device_token'),
				"role_id" => 4,
				"phone_verify" => 1,
				"status" => 1,
				"added_date" =>  date("Y-m-d H:i:s")
			);

			$conds_phone['user_phone'] = $user_data['user_phone'];
			$user_infos = $this->User->get_one_user_phone($conds_phone)->result();
			$user_id = $user_infos[0]->user_id;


			if ($user_id != "") {
				//user email alerady exist

				//for user name and user email
				$user_name = $this->post('user_name');
				$user_phone = $this->post('user_phone');

				if ($user_name == "" && $user_phone == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_phone" => $user_infos[0]->user_phone,
						"device_token"  => $user_data['device_token'],
						"phone_id" 	=> $user_data['phone_id'],
						"phone_verify" => $user_data['phone_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_name == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_phone"    => $user_phone,
						"device_token"  => $user_data['device_token'],
						"phone_id" 	=> $user_data['phone_id'],
						"phone_verify" => $user_data['phone_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_phone == "") {
					$user_data = array(
						"user_name"    => $user_name,
						"user_phone" => $user_infos[0]->user_phone,
						"device_token"  => $user_data['device_token'],
						"phone_id" 	=> $user_data['phone_id'],
						"phone_verify" => $user_data['phone_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else {
					$user_data = array(
						"user_name"    => $user_name,
						"user_phone"    => $user_phone,
						"device_token"  => $user_data['device_token'],
						"phone_id" 	=> $user_data['phone_id'],
						"phone_verify" => $user_data['phone_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				}

				$this->User->save($user_data, $user_id);

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(
							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			} else {
				//user phone not exist

				if (!$this->User->save($user_data)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_data['user_id'],
							"platform_name" => $this->post('platform_name')


						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_data['user_id'],
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_data['user_id'],
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$this->custom_response($this->User->get_one($user_data['user_id']));
			}

			$this->custom_response($this->User->get_one($user_infos[0]->user_id));
		} else {
			//update
			//User already exist in DB
			$user_data = array(
				"user_name"    	=> $this->post('user_name'),
				"user_phone"    => $this->post('user_phone'),
				"device_token" => $this->post('device_token'),
			);

			//for user name and user email
			$user_name = $this->post('user_name');
			$user_phone = $this->post('user_phone');

			if ($user_name == "" && $user_phone == "") {
				$user_data = array(
					'device_token'  => $this->post('device_token'),
				);
			} else if ($user_name == "") {
				$user_data = array(
					'user_phone'    => $user_data['user_phone'],
					'device_token'  => $user_data['device_token'],
				);
			} else if ($user_phone == "") {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'device_token'  => $user_data['device_token'],
				);
			} else {
				$user_data = array(
					'user_name'    => $user_data['user_name'],
					'user_phone'    => $user_data['user_phone'],
					'device_token'  => $user_data['device_token'],
				);
			}

			$conds['phone_id'] = $this->post('phone_id');
			$user_datas = $this->User->get_one_by($conds);
			$user_id = $user_datas->user_id;

			if ($user_datas->is_banned == 1) {

				$this->error_response(get_msg('err_user_banned'), 503);
			} else {

				if (!$this->User->save($user_data, $user_id)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')


						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			}

			$this->custom_response($this->User->get_one($user_datas->user_id));
		}
	}

	/** Registration with Apple
	 */

	function apple_register_post()
	{
		$rules = array(
			array(
				'field' => 'apple_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		//Need to check apple_id is aleady exist or not?
		if (!$this->User->exists(
			array(
				'apple_id' => $this->post('apple_id')
			)
		)) {

			$rules = array(
				array(
					'field' => 'user_name',
					'rules' => 'required'
				)
			);

			$user_data = array(
				"user_name" 	=> $this->post('user_name'),
				"user_email"    => $this->post('user_email'),
				"apple_id" 	=> $this->post('apple_id'),
				"device_token" => $this->post('device_token'),
				"role_id" => 4,
				"apple_verify" => 1,
				"status" 	=> 1,
				"code"   => ' ',
				"added_date" =>  date("Y-m-d H:i:s")
			);

			if ($user_data['user_email'] != "") {
				$cond_user_existed['user_email'] = $user_data['user_email'];
				$cond_user_existed['phone_id'] = "";
				$user_infos = $this->User->get_email_phone($cond_user_existed)->result();
				$user_id = $user_infos[0]->user_id;
			}
			if ($user_id != "") {
				//user email alerady exist
				//for user name and user email
				$user_name = $this->post('user_name');
				$user_email = $this->post('user_email');

				if ($user_name == "" && $user_email == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"apple_id" 	=> $user_data['apple_id'],
						"apple_verify" => $user_data['apple_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_name == "") {
					$user_data = array(
						"user_name" => $user_infos[0]->user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"apple_id" 	=> $user_data['apple_id'],
						"apple_verify" => $user_data['apple_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else if ($user_email == "") {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email" => $user_infos[0]->user_email,
						"device_token"  => $user_data['device_token'],
						"apple_id" 	=> $user_data['apple_id'],
						"apple_verify" => $user_data['apple_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				} else {
					$user_data = array(
						"user_name"    => $user_name,
						"user_email"    => $user_email,
						"device_token"  => $user_data['device_token'],
						"apple_id" 	=> $user_data['apple_id'],
						"apple_verify" => $user_data['apple_verify'],
						"role_id" => $user_data['role_id'],
						"status" 	=> $user_data['status']
					);
				}

				$this->User->save($user_data, $user_id);

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			} else {
				//user email not exist
				if (!$this->User->save($user_data)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}

				$this->custom_response($this->User->get_one($user_data['user_id']));
			}

			$this->custom_response($this->User->get_one($user_infos[0]->user_id));
		} else {

			$user_data = array(
				'user_name'    	=> $this->post('user_name'),
				'user_email'    => $this->post('user_email'),
				'device_token'  => $this->post('device_token')
			);

			//for user name
			$user_name = $this->post('user_name');
			$user_email = $this->post('user_email');

			if ($user_name == "" && $user_email == "") {
				$user_data = array(
					'device_token'  => $this->post('device_token'),
				);
			} else if ($user_name == "") {
				$user_data = array(
					'user_email'    => $this->post('user_email'),
					'device_token'  => $this->post('device_token'),
				);
			} else if ($user_email == "") {
				$user_data = array(
					'user_name'    => $this->post('user_name'),
					'device_token'  => $this->post('device_token'),
				);
			} else {
				$user_data = array(
					'user_name'    => $this->post('user_name'),
					'user_email'    => $this->post('user_email'),
					'device_token'  => $this->post('device_token'),
				);
			}

			$conds['apple_id'] = $this->post('apple_id');
			$user_datas = $this->User->get_one_by($conds);
			$user_id = $user_datas->user_id;

			if ($user_datas->is_banned == 1) {

				$this->error_response(get_msg('err_user_banned'), 503);
			} else {

				if (!$this->User->save($user_data, $user_id)) {
					$this->error_response(get_msg('err_user_register'), 500);
				}

				$noti_token = array(
					"device_id" => $this->post('device_token')
				);

				$noti_count = $this->Notitoken->count_all_by($noti_token);

				if ($noti_count == 1) {
					if ($this->Notitoken->exists($noti_token)) {
						$noti_id = $this->Notitoken->get_one_by($noti_token);
						$push_noti_token_id = $noti_id->id;
						$noti_data = array(

							"user_id" => $user_id,
							"platform_name" => $this->post('platform_name')

						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					} else {
						$noti_data = array(
							"user_id" => $user_id,
							"device_id" => $this->post('device_token'),
							"platform_name" => $this->post('platform_name')
						);
						$this->Notitoken->save($noti_data, $push_noti_token_id);
					}
				} else {
					$this->Notitoken->delete_by($noti_token);
					$noti_data = array(
						"user_id" => $user_id,
						"device_id" => $this->post('device_token'),
						"platform_name" => $this->post('platform_name')
					);
					$this->Notitoken->save($noti_data, $push_noti_token_id);
				}
			}

			$this->custom_response($this->User->get_one($user_datas->user_id));
		}
	}

	/*
	User Delete from Api
	*/

	function user_delete_post()
	{

		// validation rules for user register
		$rules = array(
			array(
				'field' => 'user_id',
				'rules' => 'required'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$id = $this->post('user_id');

		$conds['user_id'] = $id;

		// check user id

		$user_data = $this->User->get_one_by($conds);

		//print_r($user_data);die;


		if ($user_data->user_id == "") {

			$this->error_response(get_msg('invalid_user_id'), 400);
		} elseif ($user_data->user_is_sys_admin == 1) {
			$this->error_response(get_msg('user_not_able_delete'), 403);
		} else {
			$user_id = $this->post('user_id');
			$conds_user['user_id'] = $user_id;
			$conds_user_item['added_user_id'] = $user_id;
			$conds_user_report['reported_user_id'] = $user_id;

			// delete user
			if (!$this->User->delete_by($conds_user)) {
				// if there is an error in deleting user,

				return false;
			}

			// delete Code
			if ( !$this->Code->delete_by( $conds_user_item )) {

				return false;
			}

			// // delete ResetCode
			if ( !$this->ResetCode->delete_by( $conds_user )) {

				return false;
			}

			// delete comment header
			if (!$this->Commentheader->delete_by($conds_user)) {
				// if there is an error in deleting comment header,

				return false;
			}

			// delete comment detail
			if (!$this->Commentdetail->delete_by($conds_user)) {
				// if there is an error in deleting comment detail,

				return false;
			}

			// delete favourite
			if (!$this->Favourite->delete_by($conds_user)) {
				// if there is an error in deleting favourite,

				return false;
			}

			// delete ratings
			if (!$this->Rate->delete_by($conds_user)) {
				// if there is an error in deleting ratings,

				return false;
			}



			// delete items report
			if (!$this->Itemreport->delete_by($conds_user_report)) {
				// if there is an error in deleting items report,

				return false;
			}

			// delete noti token
			if (!$this->Notitoken->delete_by($conds_user)) {
				// if there is an error in deleting ratings,

				return false;
			}

			// delete items and others related with item

			$items_data 	= $this->Item->get_one_by($conds_user_item);

			$item_data['item_id'] = $items_data->id;
			$touch_data['type_id'] = $items_data->id;
			$img_data['img_parent_id'] = $items_data->id;

			$this->Commentheader->delete_by($item_data);
			$this->Favourite->delete_by($item_data);
			$this->Itemcollection->delete_by($item_data);
			$this->Itemreport->delete_by($item_data);
			$this->Specification->delete_by($item_data);
			$this->Rate->delete_by($item_data);
			$this->Touch->delete_by($touch_data);
			$this->Image->delete_by($img_data);


			if (!$this->Item->delete_by($conds_user_item)) {
				// if there is an error in deleting items,
				return false;
			}

			$this->success_response(get_msg('success_delete'), 200);
		}
	}

	/**
	 * Users Logout
	 */
	function logout_post()
	{
		// validation rules for user register
		$rules = array(

			array(
				'field' => 'user_id',
				'rules' => 'required|callback_id_check[User]'
			)
		);

		// exit if there is an error in validation,
		if (!$this->is_valid($rules)) exit;

		$conds['user_id'] = $this->post('user_id');
		$this->Notitoken->delete_by($conds);

		$this->success_response(get_msg('success_logout'), 200);
	}

}
