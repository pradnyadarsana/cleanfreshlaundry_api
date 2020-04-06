<?php
use chriskacerguis\RestServer\RestController; 
Class Pricelist extends RestController{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('PricelistModel');
        $this->load->library('form_validation');
    }

    public function index_get(){
        return $this->returnData($this->db->get('data_pricelist')->result(), false);
    }

    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->PricelistModel->rules();
        if($id == null){
            array_push($rule,
                [
                    'field' => 'category',
                    'label' => 'category',
                    'rules' => 'required|alpha_numeric|is_unique[data_pricelist.category]'
                ],
                [
                    'field' => 'duration',
                    'label' => 'duration',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'price',
                    'label' => 'price',
                    'rules' => 'required|integer'
                ]
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'category',
                    'label' => 'category',
                    'rules' => 'required|alpha_numeric'
                ],
                [
                    'field' => 'duration',
                    'label' => 'duration',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'price',
                    'label' => 'price',
                    'rules' => 'required|integer'
                ]
            );
        }
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }
        $pricelist = new PricelistData();
        $pricelist->category = $this->post('category');
        $pricelist->duration = $this->post('duration');
        $pricelist->price = $this->post('price');
        if($id == null){
            $response = $this->PricelistModel->store($pricelist);
        }else{
            $response = $this->PricelistModel->update($pricelist,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->PricelistModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class PricelistData{
    public $category;
    public $duration;
    public $price;
}