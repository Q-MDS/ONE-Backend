<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller 
{
	public function __construct() 
    {
        parent::__construct();

		$this->load->helper(['security']);

		if (!$this->session->userdata('is_logged_in')) {
            // Set a message for the user
            $this->session->set_flashdata('error_message', 'You must be logged in to access your account.');
            // Redirect to login page
            redirect('verify'); // Adjust login URL as needed
        }

        $this->load->model('Account_model');
    }

	public function delete()
	{
		$data = array();
		
		$data['title'] = "Delete Account";

		// Load the delete account view
		$this->load->view('account/delete/index', $data);
	}

	public function delete_account()
	{
		// 1. Check if it's a POST request (prevents accessing via GET)
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            // Not a POST request, redirect away (e.g., back to the confirmation page or account dashboard)
            $this->session->set_flashdata('error_message', 'Invalid request method.');
            redirect('account/delete'); // Redirect back to the page that shows the form
            return; // Stop execution
        }

        // 2. CSRF Protection Check (Handled automatically by CI if enabled,
        //    but the hidden field must be present in the form)
        //    No explicit code needed here if CSRF is globally enabled and configured.

        // 3. Validate the confirmation text
        $confirmation = $this->input->post('confirmation_text', TRUE); // TRUE enables XSS filtering

        if ($confirmation !== 'DELETE') {
            // Confirmation text does not match
            $this->session->set_flashdata('error_message', 'Confirmation text did not match. Please type "DELETE" exactly.');
            redirect('account/delete'); // Redirect back to the confirmation page
            return; // Stop execution
        }

        // 4. Get User ID from Session
        $user_id = $this->session->userdata('user_id'); // Make sure 'user_id' is the correct session key

        if (!$user_id) {
            // This shouldn't happen if the constructor check works, but as a safeguard
            log_message('error', 'User ID not found in session during account deletion attempt.');
            $this->session->set_flashdata('error_message', 'Could not identify your account. Please log in again.');
            redirect('auth/login'); // Redirect to login
            return;
        }

        // 5. Attempt to delete the user via the model
        if ($this->Account_model->delete_user_by_id($user_id)) {
            // Deletion Successful

            // 6. Destroy the session (log the user out)
            $this->session->sess_destroy();

            // 7. Set success message (will be shown after redirect)
            // Note: Since the session is destroyed, we can't use regular flashdata easily.
            // A common workaround is to redirect with a query parameter, or set a cookie,
            // or redirect to a specific "account deleted" page.
            // Let's redirect to login with a success message (assuming login page can show it).
            // Or, more simply, redirect to the homepage.
            // For simplicity here, we'll redirect to the base URL. A dedicated "goodbye" page is often better.
            redirect(site_url('delete_account')); // Redirect to homepage or a dedicated page

        } else {
            // Deletion Failed (model returned false)
            log_message('error', 'Failed to delete account for user ID: ' . $user_id);
            $this->session->set_flashdata('error_message', 'There was a problem deleting your account. Please try again later or contact support.');
            redirect('account/delete'); // Redirect back to the confirmation page
        }
	}
}
