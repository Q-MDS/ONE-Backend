<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verify_model extends CI_Model 
{
	public function validate($chk_cred_one, $chk_cred_two)
    {
        $result = '0';
        $cred_list = $this->getCreds();

        $cred_hash = sha1($chk_cred_two);
        
        foreach ($cred_list as $cred)
        {
            $id = $cred['id'];
            $cred_one = $cred['cred_one'];
            $cred_two = $cred['cred_two'];

			if ($chk_cred_one == $cred_one)
			{
				if ($cred_two == $cred_hash)
				{
					$this->session->set_userdata('user_id', $id);
					$this->session->set_userdata('authenticated', '1');

					return 1;
				}
			} 
        }
        
        return 0;
    }

	private function getCreds()
    {
        $data = array();
        
        $query = $this->db->query("SELECT * FROM `members` ORDER BY `cred_one` ASC");
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                $id = $row['id'];
                $cred_one = $row['cred_one'];
                $cred_two = $row['cred_two'];
                
                $data[] = array('id' => $id, 'cred_one' => $cred_one, 'cred_two' => $cred_two);
            }
        }
        $query->free_result();
       
        return $data;
    }
}
?>
