<?php 

class Data_Controller extends RestApi_Controller 
{
    function __construct() 
    {
        parent::__construct();
        $this->load->library('api_auth');
        $this->load->model('api_model');
    }

	function get_questions()
    {
		$questions = $this->api_model->getQuestions();

		$result = array(
			'status'=>true,
			'message'=>'successfully fetched quotes',
			'data'=>$questions
		);
		
		$this->response(json_encode($result),200);
    }

	function get_links()
    {
	    echo "Hello world";
	    die();
		$links = $this->api_model->getLinks();

		$err = array(
			'status'=>true,
			'message'=>'successfully fetched links',
			'data'=>$links
		);
		
		$this->response(json_encode($err),200);
    }

	public function backup()
	{
		$result = array(
			'message'=>'Backup is in progress',
			'backup_id' => rand(1000,9999),
			'backup_num' => 1,
			'result' => true
		);
		
		$this->response(json_encode($result),200);
	}

	public function shopper_profile_pic()
	{
		$timestamp = strtotime(date('Y-m-d H:i:s'));

		$base_path = FCPATH . "pictures/shopper_profile/";

		$shopper_id = $_POST['shopper_id'];
		$file_type = $_POST['image_type'];
		$file_ext = $this->get_extention($file_type);
		$image_string = $_POST['image'];
		$file_name = $shopper_id . '_sp_' . $timestamp . $file_ext;

		$ifp = fopen( $base_path . $file_name, 'wb' );
		$success = fwrite( $ifp, base64_decode( $image_string ) );
		fclose( $ifp );

		if ($success)
		{
			// Generate a url
			$file_link = base_url() . 'pictures/shopper_profile/' . $file_name;
			// Update the profile_pic field in table
			$this->api_upload_model->updShopperProfilePic($shopper_id, $file_link);

			$responseData = array(
				'status'=>true,
				'message'=>'File successfully uploaded',
				'data'=>$file_link
			);
		} 
		else 
		{
			$responseData = array(
				'status'=>false,
				'message'=>'There was an error uploading the file',
				'data'=>[]
			);
		}

		return $this->response($responseData, 200);
	}
}
