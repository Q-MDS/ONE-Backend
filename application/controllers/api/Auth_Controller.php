<?php 

class Auth_Controller extends RestApi_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->model('api_model');
    }

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
			return $this->response($responseData);
		} 
		else 
		{
			$responseData = array(
				'status'=>true,
				'message' => 'User does not exists',
				'data'=> []
			);
			return $this->response($responseData);
		}
	}

    function register()
    {
		echo "Bob";
    	/*$json = file_get_contents('php://input');
        $data = json_decode($json);
        $first_name = TRIM($data->first_name);
        $last_name = TRIM($data->last_name);
        $cred_one = TRIM($data->cred_one);
        $cred_two = TRIM($data->cred_two);*/
        // $notifications = TRIM($data->notifications);
        // $quotes = TRIM($data->quotes);
        // $quiz_mode = TRIM($data->quiz_mode);
        // $accept_tc = TRIM($data->accept_tc);
        // $one_package = TRIM($data->one_package);

		// $one_package == 1 ? $trial_check = 1 : $trial_check = 0;
		$trial_expires = date('Y-m-d H:i:s', strtotime('+21 days'));

		$first_name = 'Peter';
		$last_name = 'Parker';
		$cred_one = "spider@man.com";
		$cred_two = "123456";

		// Plan types: 0 - free, 1 - coaching
    
        if($cred_one != '' && $cred_two != '')
        {
			// Create member
			$data  = array(
				'register_date' => date('Y-m-d H:i:s'),
				'trial_expires' => $trial_expires,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $cred_one,
				'plan_type'=> 1,
				'subscribed'=> 0,
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s')
			);
			$insert_id = $this->api_model->createMember($data);

			// Create User
			$data = array(
				'name' => $first_name . ' ' . $last_name,
				'user_id' => $insert_id,
				'cred_one'=>$cred_one,
				'cred_two'=>sha1($cred_two),
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s')
			);
			$this->api_model->registerUser($data);

             $responseData = array(
                'status'=>true,
                'message' => 'Successfully Registerd',
				'remote_id' => $insert_id,
                'data'=> []
             );

             return $this->response($responseData,200);
        }
        else 
        {
            $responseData = array(
                'status'=>false,
                'message' => 'fill all the required fields ',
                'data'=> []
             );
             return $this->response($responseData);
        }
    }

    function login() 
    {
    	$json = file_get_contents('php://input');
        $data = json_decode($json);
        $cred_one = $data->credOne;
		$cred_two = $data->credTwo;
        //  $cred_one = 'Dbruce@gmail.com';
		//  $cred_two = '123456';
		
		if(TRIM($cred_one) != '' || TRIM($cred_two) != '')
		{
             $data = array('cred_one'=>$cred_one,'cred_two'=> sha1($cred_two));
             $loginStatus = $this->api_model->checkLogin($data);

             if($loginStatus != false) 
             {
				 $userId = $loginStatus->user_id;
				 $bearerToken = $this->api_auth->generateToken($userId);

				 $member = $this->api_model->getMember($userId);

				 // Check in how many days the trial expires
				 $trial_expires = $member->trial_expires;
				 $subscribed = $member->subscribed;
				 $plan_type = $member->plan_type;
				 $today = date('Y-m-d H:i:s');
				 $days_left = (strtotime($trial_expires) - strtotime($today)) / (60 * 60 * 24);
				 $days_left = round($days_left);

				 // If the days left is < 0 - change the plan_type to 0
				 if ($days_left < 0)
				 {
					 $data = array('plan_type' => 0);
					 $this->api_model->updatePlanType($userId, $data);
					 $plan_type = 0;
				 }
				
				$responseData = array(
					'status'=> true,
					'message' => 'Successfully Logged In',
					'days_left' => $days_left,
					'subscribed' => $subscribed,
					'plan_type' => $plan_type,
					'token'=> $bearerToken
                 );
				 
                 return $this->response($responseData,200);
             }
             else 
             {
                $responseData = array(
                    'status'=>false,
                    'message' => 'Invalid Crendentials',
                 );
				 
                 return $this->response($responseData, 200);
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

	function change_password()
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		$cred_one = $data->credOne;
		$cred_two = $data->credTwo;
		$cred_new = $data->credNew;

		// $cred_one = "bruce@gmail.com";
		// $cred_two = '123456';
		// $cred_new = '1234567';

		if(TRIM($cred_one) != '' || TRIM($cred_two) != '' || TRIM($cred_new) != '')
		{
			$data = array('cred_one'=>$cred_one,'cred_two'=> sha1($cred_two));
			$loginStatus = $this->api_model->checkLogin($data);

			if($loginStatus != false) 
			{
				$userId = $loginStatus->user_id;
				$data = array('cred_two'=> sha1($cred_new));
				$result = $this->api_model->updatePassword($userId, $data);

				if($result > 0)
				{
					$responseData = array(
						'status'=> true,
						'result' => 1,
						'message' => 'Password changed successfully',
					);
					return $this->response($responseData,200);
				}
				else
				{
					$responseData = array(
						'status'=> false,
						'result' => 2,
						'message' => 'Password not changed. New password might be the same as your current one',
					);
					return $this->response($responseData,200);
				}
			}
			else 
			{
				$responseData = array(
					'status'=>false,
					'result' => 3,
					'message' => 'Invalid Current Password',
				);
				return $this->response($responseData, 200);
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

	function get_questions()
    {
		$this->load->model('api_model');
		$questions = $this->api_model->getQuestions();

		$result = array(
			'status'=>true,
			'message'=>'successfully fetched quotes',
			'data'=>$questions
		);
		
		$this->response(json_encode($result),200);
    }

	function http_test()
	{
		$result = array(
			'message'=>'HTTP is accessable',
		);
		
		$this->response(json_encode($result),200);
	}
}
