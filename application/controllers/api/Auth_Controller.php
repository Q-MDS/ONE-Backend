<?php 

class Auth_Controller extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();
        //$this->load->library('api_auth');
        $this->load->model('api_model');
    }
    
	// √ 08022025
    function check_user()
	{
		$json = file_get_contents('php://input');
        $data = json_decode($json);
		$email = TRIM($data->email);

		// Check if user exists
		$userExists = $this->api_model->checkUserExists($email);
		if ($userExists)
		{
			$responseData = array(
				'status'=>false,
				'message' => 'User already exists',
				'data'=> []
			);
			echo json_encode($responseData);
		} 
		else 
		{
			$responseData = array(
				'status'=>true,
				'message' => 'User does not exists',
				'data'=> []
			);
			echo json_encode($responseData);
		}
	}
    
	// √ 07022025
	function register()
	{
		$json = file_get_contents('php://input');
		
		$data = json_decode($json);
		$first_name = TRIM($data->first_name);
		$last_name = TRIM($data->last_name);
		$cred_one = TRIM($data->cred_one);
		$cred_two = TRIM($data->cred_two);
		$subscribed = TRIM($data->subscribed);
		
		//$first_name = 'Peter';
		//$last_name = 'Parker';
		//$cred_one = "spider@man.com";
		//$cred_two = "123456";
		//$subscribed = '2';

		// Plan types: 0 - free, 1 - coaching

		if($cred_one != '' && $cred_two != '')
		{
			$now = time();
			
			// Create member
			$data  = array(
				'cred_one' => $cred_one,
				'cred_two'=>sha1($cred_two),
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $cred_one,
				'subscribe_status'=> $subscribed,
				'subscribe_date' => $now,
				'created_at'=> $now,
				'updated_at'=> $now
			);
			$insert_id = $this->api_model->createMember($data);

			$responseData = array(
				'status'=>true,
				'message' => 'Successfully Registerd',
				'remote_id' => $insert_id
			);

			//return $this->response($responseData,200);
			echo json_encode($responseData);
		}
		else 
		{
			$responseData = array(
				'status'=>false,
				'message' => 'fill all the required fields ',
				'remote_id' => 0
			);
			echo json_encode($responseData);
		}
	}

	// √ 08022025
	function subscribe()
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		
		$member_id = TRIM($data->remote_id);
		$purchase_info = TRIM($data->purchase_info);

		//$member_id = 12;
		//$purchase_info = '{"activeSubscriptions": ["rc_15_1m"], "allExpirationDates": {"rc_126_1y": "2025-02-07T13:45:15Z", "rc_15_1m": "2025-02-08T13:45:15Z"}, "allExpirationDatesMillis": {"rc_126_1y": 1738935915000, "rc_15_1m": 1739022315000}, "allPurchaseDates": {"rc_126_1y": "2025-02-07T13:41:04Z", "rc_15_1m": "2025-02-07T13:45:15Z"}, "allPurchaseDatesMillis": {"rc_126_1y": 1738935664000, "rc_15_1m": 1738935915000}, "allPurchasedProductIdentifiers": ["rc_15_1m", "rc_126_1y"], "entitlements": {"active": [Object], "all": [Object], "verification": "NOT_REQUESTED"}, "firstSeen": "2025-02-07T13:44:14Z", "firstSeenMillis": 1738935854000, "latestExpirationDate": "2025-02-08T13:45:15Z", "latestExpirationDateMillis": 1739022315000, "managementURL": "https://apps.apple.com/account/subscriptions", "nonSubscriptionTransactions": [], "originalAppUserId": "$RCAnonymousID:51ab6f149c7541978370770f3f1d0cb4", "originalApplicationVersion": "1.0", "originalPurchaseDate": "2025-02-05T13:41:05Z", "originalPurchaseDateMillis": 1738762865000, "requestDate": "2025-02-07T13:45:19Z", "requestDateMillis": 1738935919862}, "productIdentifier": "rc_15_1m", "transaction": {"productId": "rc_15_1m", "productIdentifier": "rc_15_1m", "purchaseDate": "2025-02-07T13:45:15Z", "purchaseDateMillis": 1738935915000, "revenueCatId": "2000000850592971", "transactionIdentifier": "2000000850592971"}}';

		// Add subscription data
		$now = time();
			
		// Create member
		$data  = array(
			'member_id' => $member_id,
			'subscription_date' => $now,
			'subscription_data' => $purchase_info
		);
		$this->api_model->addSubscription($data);

		$responseData = array(
			'status'=>true,
			'message' => 'Successfully added subscription',
		);

		echo json_encode($responseData);

	}

	// √ 23022025
	function send_profile()
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		
		$remote_id = TRIM($data->remote_id);
		$profile = TRIM($data->profile);

		$this->api_model->sendProfile($remote_id, $profile);

		$responseData = array(
			'status'=>true,
			'message' => 'Successfully updated profile',
		);

		echo json_encode($responseData);
	}

	// √ 0822025
	function login() 
	{
		$json = file_get_contents('php://input');
		$contents = json_decode($json);

		$data = $contents->data;

		$cred_one = $data->cred_one;
		$cred_two = $data->cred_two;
		$profile = $data->profile;

		//  $cred_one = 'harry1@potty.com';
		//  $cred_two = '12345678';
		//  $profile = '{"quiz_last_asked":1739713830,"last_purpose_order":4,"backup_num":0,"remote_id":3,"quiz_done":0,"first_time_use":1,"rc_id":"","first_time_login":1,"last_name":"Potter","last_backup":0,"cred_1":"harry1@potty.com","show_quiz":0,"last_restore":0,"subscribed":1,"first_name":"Harry","last_quote_order":4,"trial_expires":1741516031,"id":1,"profile_pic":"","show_noti":1,"token":"","backup_id":"","quiz_num":1,"show_quotes":0}';

		// echo "FJB FJB --- Cred1: " . $cred_one . " Cred2: " . $cred_two . " Profile: " . $profile;

		// Try to always return the stored profile record
		// It will be updated from other app processes
		// Becasuse select plan writes the profile we should never have a scenario where subscribed = 2

		if(TRIM($cred_one) != '' || TRIM($cred_two) != '')
		{
			$loginStatus = $this->api_model->checkLogin($cred_one, $cred_two);
			$bearerToken = '1234567890';
			
			
			// Status: 0 - correct creds subscription is whack, 1 - correct creds and subscribed

			if($loginStatus != false) 
			{
				$get_profile = $loginStatus['profile'];

				$responseData = array(
					'result' => true,
					'status' => 1,	
					'profile' => $get_profile,
					'message' => 'Successfully Logged In',
					'token' => $bearerToken
				);

				echo json_encode($responseData);
				/** Verify subscription status: VALID - login creds ARE ok and subscription ok */	 
				// if ($this->chkSubsctiption())
				// {
				// 	// Process profile record
				// 	$profile_data = json_decode($profile);
				// 	$subscribed = $profile_data->subscribed;

				// 	// If subscribed = 0 then send the PHP profile record back else use the APK profile and save to members record
				// 	// If subscribed = 1 update the db profile and return the given profile

				// 	if ($subscribed == 2)
				// 	{
				// 		$get_profile = $loginStatus['profile'];
						
				// 		if ($get_profile == '')
				// 		{
				// 			echo "Oh poo";
				// 			$get_profile = $profile;
				// 			$data = array('profile' => $get_profile);
				// 			$this->api_model->updateProfile($loginStatus['id'], $data);
				// 		}
				// 	}
				// 	else 
				// 	{
				// 		$get_profile = $profile;
				// 		$data = array('profile' => $get_profile);
				// 		$this->api_model->updateProfile($loginStatus['id'], $data);
				// 	}

				// 	$responseData = array(
				// 		'result' => true,
				// 		'status' => 1,	
				// 		'profile' => $get_profile,
				// 		'message' => 'Successfully Logged In',
				// 		'token' => $bearerToken
				// 	);
				// }
				// else
				// {
				// 	/** Verify subscription status: VALID - login creds ARE ok and subscription NOT ok */
				// 	$responseData = array(
				// 		'result' => true,
				// 		'status' => 0,	
				// 		'profile' => $loginStatus['profile'],
				// 		'message' => 'Subscription error',
				// 		'token' => $bearerToken
				// 	);
				// }
			}
			else 
			{
				$responseData = array(
					'result'=>false,
					'message' => 'Invalid Crendentials',
				);
				
				echo json_encode($responseData);
			}
		}
		else 
		{
			$responseData = array(
				'result'=>false,
				'status' => 0,
				'message' => 'Email Id and password is required',
			);
			echo json_encode($responseData);
		}
	}

	// √ 09022025
	function change_password()
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		$cred_one = $data->credOne;
		$cred_two = $data->credTwo;
		$cred_new = $data->credNew;

		// $cred_one = "harry@potty.com";
		// $cred_two = '12345678';
		// $cred_new = '123456';

		if(TRIM($cred_one) != '' || TRIM($cred_two) != '' || TRIM($cred_new) != '')
		{
			$data = array('cred_one'=>$cred_one,'cred_two'=> sha1($cred_two));
			// $loginStatus = $this->api_model->checkLogin($data);
			$loginStatus = $this->api_model->checkLogin($cred_one, $cred_two);
			// print_r($loginStatus);
			// die();

			if($loginStatus != false) 
			{
				$userId = $loginStatus['id'];
				$data = array('cred_two'=> sha1($cred_new));
				$result = $this->api_model->updatePassword($userId, $data);

				if($result > 0)
				{
					$responseData = array(
						'status'=> true,
						'result' => 1,
						'message' => 'Password changed successfully',
					);
					echo json_encode($responseData,200);
				}
				else
				{
					$responseData = array(
						'status'=> false,
						'result' => 2,
						'message' => 'Password not changed. New password might be the same as your current one',
					);
					echo json_encode($responseData,200);
				}
			}
			else 
			{
				$responseData = array(
					'status'=>false,
					'result' => 3,
					'message' => 'Invalid Current Password',
				);
				echo json_encode($responseData, 200);
			}
		}
		else 
		{
			$responseData = array(
				'status'=>false,
				'message' => 'Email Id and password is required',
			);
			return $this->response($responseData, 200);
		}
	}

	// x 14022025
	function close_account()
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);

		$remote_id = $data->remote_id;
		// $remote_id = 1;

		// $result = $this->api_model->deleteMember($remote_id);
		$result = 1;

		if($result > 0)
		{
			$responseData = array(
				'status'=> true,
				'message' => 'Account deleted successfully ' . remote_id,
			);
			echo json_encode($responseData,200);
		}
		else
		{
			$responseData = array(
				'status'=> false,
				'message' => 'Account not closed',
			);
			echo json_encode($responseData,200);
		}
	}

	// √ 17022025
	function forgot()
	{
		$this->load->library('email');

		// $json = file_get_contents('php://input');
		// $contents = json_decode($json);

		// $email = $contents->data->email;
		
		$email = 'quintin@pxo.co.za';
		// echo "PPP: $email";
		// echo json_encode(['message' => 'Reset link sent']);
		// die();
		
		$user = $this->api_model->get_user_by_email($email);

		if (!$user) 
		{
            echo json_encode(['message' => 'User not found']);
            return;
        }

        $token = bin2hex(random_bytes(20));
        $this->api_model->set_reset_token($user->id, $token);

		//$reset_link = base_url() . 'reset_password?token=' . $token;
		$reset_link = base_url("auth_controller/reset_password?token=" . urlencode($token));

		// echo "XXX: $reset_link";	
		// die();

		// SMTP configuration
		$config = array(
			'protocol'  => 'smtp',
			'smtp_host' => 'mail.pxo.co.za', // e.g., smtp.gmail.com
			'smtp_port' => 587, // or 465 for SSL
			'smtp_user' => 'quintin@pxo.co.za',
			'smtp_pass' => 't00314',
			'mailtype'  => 'html',
			'charset'   => 'utf-8',
			'newline'   => "\r\n"
		);
	
		$this->email->initialize($config);

		$this->email->from('quintin@pxo.co.za', 'ONE App');
        $this->email->to($email);
        $this->email->subject('Password Reset');
        $this->email->message("Click the following link to reset your password: $reset_link");

        if ($this->email->send()) 
		{
            echo json_encode(['message' => 'Reset link sent']);
        } 
		else 
		{
            echo json_encode(['message' => 'Error sending email']);
        }
	}

	function reset_password()
	{
		$token = $this->input->get('token');
        $user = $this->User_model->get_user_by_token($token);

        if (!$user || $user->reset_token_expires < time()) {
            echo json_encode(['message' => 'Invalid or expired token']);
            return;
        }

        $new_password = $this->input->post('password');
        $this->User_model->update_password($user->id, $new_password);

        echo json_encode(['message' => 'Password has been reset']);
	}

	function create_subscriber()
	{
		$json = file_get_contents('php://input');
		$contents = json_decode($json);

		// print_r($contents);


		$data = $contents[0]->data;
		$rc_id = $contents[0]->rc_id;

		
		// echo "XXX: " . $data->cred_one. " --- ";
		// print_r($data);
		// die();

		$rc_id = $contents[0]->rc_id;
		$cred_1 = TRIM($data->cred_one);
		$cred_2 = TRIM($data->cred_two);
		$first_name = TRIM($data->first_name);
		$last_name = TRIM($data->last_name);
		$email = TRIM($data->cred_one);

		$profile = json_encode($data->profile);

		$now = time();

		// die();
		// $rc_id = 'RC234243ASDA211';
		// $cred_1 = 'bruce006@gmail.com';
		// $cred_2 = '123456';
		// $first_name = 'Bruce';
		// $last_name = 'Wayne';
		// $email = 'bruc006e@gmail.com';
		// $profile = '{"quiz_last_asked":0,"last_purpose_order":0,"cred_1":"","first_time_login":0,"quiz_num":0,"backup_num":0,"remote_id":0,"quiz_done":0,"first_time_use":1,"rc_id":"","first_name":"","last_name":"","backup_id":"","last_backup":0,"id":1,"last_restore":0,"subscribed":0,"last_quote_order":0,"token":"","trial_expires":0,"profile_pic":""}';

		$insert_id = $this->api_model->createSubscriber($now, $rc_id, $cred_1, $cred_2, $first_name, $last_name, $email);

		$subscribe_profile = $this->build_profile($insert_id, $rc_id, $cred_1, $cred_2, $now, $profile, $first_name, $last_name, $email);
		
		$data = array('profile' => json_encode($subscribe_profile));
		$this->api_model->updateProfile($insert_id, $data);

		$responseData = array(
			'status' => true,
			'profile' => $subscribe_profile,
		);
		return $this->response($responseData, 200);
	}

	function build_profile($insert_id, $rc_id, $cred_1, $cred_2, $now, $profile, $first_name, $last_name, $email)
	{
		
		$trial = $now + 1814400;
		$profile_data = json_decode($profile);
		
		$profile_data->rc_id = $rc_id;
		$profile_data->first_name = $first_name;
		$profile_data->last_name = $last_name;
		$profile_data->cred_1 = $cred_1;
		$profile_data->remote_id = $insert_id;
		// $profile_data->subscribe_date = $now;
		$profile_data->subscribed = 1;
		$profile_data->trial_expires = $trial;

		return $profile_data;
	}

	function chkSubsctiption()
	{
		return true;
	}
}
