<?php 

class Api_model extends CI_Model 
{
	function checkUserExists($email)
	{
		$this->db->where('cred_one', $email);
		$query = $this->db->get('users');
		if($query->num_rows() > 0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	function createMember($data)
	{
		$this->db->insert('members', $data);

		return $this->db->insert_id();
	}

    function registerUser($data)
    {
        $this->db->insert('users', $data);

		return $this->db->insert_id();
    }


    function checkLogin($cred_one, $cred_two)
	{
		$data = array();
		$chk_cred_two = sha1($cred_two);
		$num_recs = 0;
		
		$query = $this->db->query("SELECT * FROM users WHERE cred_one = " . $this->db->escape($cred_one) . " AND cred_two = " . $this->db->escape($chk_cred_two) . "");
		
		if ($query->num_rows() > 0)
		{
			$num_recs = $query->num_rows();
		
			foreach ($query->result_array() as $row)
			{
				$data[] = $row;
			}
		}
		$query->free_result();
		
		if ($num_recs > 0)
		{
			return $data;
		}
		else
		{
			return false;
		}
    }

	public function updatePlanType($userId, $data)
	{
		$this->db->where('id', $userId);
		$this->db->update('members', $data);

		return $this->db->affected_rows();
	}

	function updatePassword($userId, $data)
	{
		$this->db->where('id', $userId);
		$this->db->update('users', $data);

		return $this->db->affected_rows();
	}

	function getMember($memberId)
	{
		$this->db->where('id', $memberId);
		$query = $this->db->get('members');
		return $query->row();
	}	

    function getProfile($userId)
    {
        $this->db->select('name,email');
        $this->db->where(['id'=>$userId]);
        $query = $this->db->get('users');
        return $query->row();
    }

    function getQuotes()
    {
        $data = array();
		
		$query = $this->db->query("SELECT * FROM list_quotes");
		
		if ($query->num_rows() > 0)
		{
			$num_recs = $query->num_rows();
		
			foreach ($query->result_array() as $row)
			{
				$data[] = $row;
			}
		}
		$query->free_result();
		
		return $data;
    }

    function getPurposes()
    {
        $query = $this->db->query("SELECT * FROM list_purpose");
		return $query->result_array();
    }

	function getQuestions()
    {
        $data = array();
		
		$query = $this->db->query("SELECT * FROM list_quiz");
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$data[] = $row;
			}
		}
		$query->free_result();
		
		return $data;
    }

}
