<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verify extends CI_Controller 
{
	public function __construct() 
    {
        parent::__construct();

        $this->load->model('verify_model');
    }

	public function validate()
	{
		$cred_1 = $_POST['cred_1'];
        $cred_2 = $_POST['cred_2'];

        $result = $this->verify_model->validate($cred_1, $cred_2);

		if ($result == 1)
		{
			$this->session->set_userdata('is_logged_in', true);
			
			redirect('account/delete');
		}
		else
		{
			$this->session->set_flashdata('message', 'Invalid username or password.');
			redirect('verify');
		}
	}

	public function index()
	{
		$data = array();

		$data['title'] = "Delete Account";

		// Load the delete account view
		$this->load->view('verify/index', $data);
	}

	public function logout()
	{
		$this->session->set_userdata('logged_in', false);
		$this->session->unset_userdata('logged_in');
		$this->session->unset_userdata('authenticated');
		$this->session->sess_destroy();
        redirect(base_url() . 'delete_account');
	}
}
