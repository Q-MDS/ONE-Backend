<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive extends CI_Controller {

	function __construct() 
    {
        parent::__construct();

		$this->load->helper('file');
        $this->load->model('archive_model');
    }

	public function backup()
	{
		// $input = json_decode(file_get_contents('php://input'), true);


		$json = file_get_contents('php://input');
		$contents = json_decode($json);

		$input = $contents->data;

		if (isset($input->file) && isset($input->remoteId) && isset($input->backupId))
		{
			$backupDate = time();
        	$fileData = base64_decode($input->file);
			$remoteId = $input->remoteId;
        	$backupId = $input->backupId;

			if ($backupId == '') 
			{
				$backupId =  bin2hex(random_bytes(6));
			}
			
			$base_path = FCPATH . "archive/$backupId";

			// Ensure the directory exists
			if (!is_dir($base_path)) 
			{
				mkdir($base_path, 0755, true);
			}
			
			$fileName = 'one_' . $backupDate . '.db';
        	$filePath = $base_path . '/' . $fileName;
			if (write_file($filePath, $fileData)) 
			{
				$response = array('status' => 'success', 'message' => 'File uploaded successfully', 'backupId' => $backupId, 'backupDate' => $backupDate);
				$this->archive_model->addBackup($remoteId, $backupId, $backupDate, $fileName);
			} 
			else 
			{
				$response = array('status' => 'error', 'message' => 'Failed to write file');
			}
		} 
		else 
		{

			$response = array('status' => 'error', 'message' => 'No file data received');
		}
		
		echo json_encode($response);
	}

	public function restore_list()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		
		if (isset($input['remoteId'])) 
		{
			$remoteId = $input['remoteId'];
			// $remoteId = 111;
			$backups = $this->archive_model->getBackups($remoteId);
			$response = array('status' => 'success', 'backups' => $backups);
		} 
		else 
		{
			$response = array('status' => 'error', 'message' => 'No remoteId received');
		}
		echo json_encode($response);
	}

	

}