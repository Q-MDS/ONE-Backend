<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utils extends CI_Controller 
{
	public function __construct() 
    {
        parent::__construct();

        $this->load->model('utils_model');

		// http://192.168.1.100/one_backend/reset_password?token=99083e70ffe78f6993266b6a4485ff5c47f94911
    }

	public function reset_password()
	{
		$token = $this->input->get('token');

		// Verify the token (pseudo code)
		$user = $this->utils_model->verify_reset_token($token);

		if (!$user) 
		{
			echo "Invalid or expired token";
			return;
		}

		// Load the reset password view
		$this->load->view('reset_password', array('token' => $token));
	}

	public function update_password()
	{
		$token = $this->input->post('token');
		$new_password = $this->input->post('password');

		// Verify the token and update the password (pseudo code)
		$user = $this->utils_model->verify_reset_token($token);
		if ($user) 
		{
		    $this->utils_model->update_password($user->id, sha1($new_password));
		    echo "Password has been reset successfully";
		} 
		else 
		{
		    echo "Invalid or expired token";
		}
	}

	public function delete_account_login()
	{
		$data = array();

		$data['title'] = "Delete Account";

		// Load the delete account view
		$this->load->view('delete_account/index', $data);
	}

	public function delete_account()
	{
		$token = $this->input->get('token');

		// Verify the token (pseudo code)
		$user = $this->utils_model->verify_delete_token($token);

		if (!$user) 
		{
			echo "Invalid or expired token";
			return;
		}

		// Load the delete account view
		$this->load->view('delete_account', array('token' => $token));
	}
}
