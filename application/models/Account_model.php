<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model 
{
	public function delete_user_by_id($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete('members');
		
		if ($this->db->affected_rows() > 0) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}

}
