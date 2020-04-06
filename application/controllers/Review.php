<?php
use chriskacerguis\RestServer\RestController; 
Class Review extends RestController{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('ReviewModel');
        $this->load->library('form_validation');
    }

    public function index_get(){
        return $this->returnData($this->db->get('data_review')->result(), false);
    }

    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->ReviewModel->rules();
        if($id == null){
            array_push($rule,
                [
                    'field' => 'rate',
                    'label' => 'rate',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'category',
                    'label' => 'category',
                    'rules' => 'required'
                ]
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'rate',
                    'label' => 'rate',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'category',
                    'label' => 'category',
                    'rules' => 'required'
                ]
            );
        }
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }
        $review = new ReviewData();
        $review->username = $this->post('username');
        $review->rate = $this->post('rate');
        $review->category = $this->post('category');
        $review->description = $this->post('description');

        if($id == null){
            $response = $this->ReviewModel->store($review);
        }else{
            $response = $this->ReviewModel->update($review,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->ReviewModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class ReviewData{
    public $username;
    public $rate;
    public $category;
    public $description;
}