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
		$links = $this->api_model->getLinks();

		$err = array(
			'status'=>true,
			'message'=>'successfully fetched links',
			'data'=>$links
		);
		
		$this->response(json_encode($err),200);
    }
}
