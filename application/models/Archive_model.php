<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model 
{
	public function addBackup($member_id, $backup_by, $backup_date, $filename)
	{
		$this->db->query("
		INSERT INTO 
			`archive` 
		SET 
			`member_id` = " . $this->db->escape($member_id) . ",  
			`backup_by` = " . $this->db->escape($backup_by) . ",  
			`backup_date` = " . $this->db->escape($backup_date) . ", 
			`filename` = " . $this->db->escape($filename) . "
		");
		
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}

	public function getBackups($member_id)
	{
		$query = $this->db->query("
		SELECT 
			`id`, 
			`backup_by`, 
			`backup_date`, 
			`filename` 
		FROM 
			`archive` 
		WHERE 
			`member_id` = " . $this->db->escape($member_id) . "
		ORDER BY
			`backup_date` DESC
		");

		$result = $query->result_array();

		return $result;
	}
}