<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utils_model extends CI_Model 
{
	public function verify_reset_token($token)
	{
		$this->db->where('reset_token', $token);
		$query = $this->db->get('members');
		$user = $query->row();
		if ($user) 
		{
			// Check if the token has expired
			if (time() > $user->reset_token_expires) 
			{
				// Token has expired
				return false;
			}
			return $user;
		}
		return false;
	}

	public function update_password($user_id, $password)
	{
		$data = [
			'cred_two' => $password,
			'reset_token' => null,
			'reset_token_expires' => null
		];
		$this->db->where('id', $user_id);
		$this->db->update('members', $data);
	}
}