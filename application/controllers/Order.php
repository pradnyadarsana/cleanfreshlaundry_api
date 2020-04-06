<?php
use chriskacerguis\RestServer\RestController; 
Class Order extends RestController{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('OrderModel');
        $this->load->library('form_validation');
        $this->load->helper(['jwt','authorization']);
    }

    public function index_get(){
        return $this->returnData($this->db->get('data_order')->result(), false);
    }

    public function userOrder_get($username){
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        if($data['status'] == 401){
            return $this->returnData($data['msg'],true);
        }
        return $this->returnData($this->db->get_where('data_order', ["username" => $username])->result(), false);
    }

    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->OrderModel->rules();
        if($id == null){
            array_push($rule,[
                    'field' => 'address',
                    'label' => 'address',
                    'rules' => 'required'
                ],
                [
                    'field' => 'price_cat',
                    'label' => 'price_cat',
                    'rules' => 'required'
                ],
                [
                    'field' => 'weight',
                    'label' => 'weight',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required'
                ]
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'address',
                    'label' => 'address',
                    'rules' => 'required'
                ],
                [
                    'field' => 'price_cat',
                    'label' => 'price_cat',
                    'rules' => 'required'
                ],
                [
                    'field' => 'weight',
                    'label' => 'weight',
                    'rules' => 'required|integer'
                ],
                [
                    'field' => 'status',
                    'label' => 'status',
                    'rules' => 'required'
                ]
            );
        }
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }
        $order = new OrderData();
        $order->username = $this->post('username');
        $order->phone = $this->post('phone');
        $order->address = $this->post('address');
        $order->price_cat = $this->post('price_cat');
        $order->weight = $this->post('weight');
        $order->price = $this->post('price');
        $order->status = $this->post('status');
        if($id == null){
            $response = $this->OrderModel->store($order);
        }else{
            $response = $this->OrderModel->update($order,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->OrderModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }

    private function verify_request()
    {
        // Get all the headers
        $headers = $this->input->request_headers();
        // Extract the token
        if(isset($headers['Authorization'])){
            $header = $headers['Authorization'];
        }else
        {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            return $response;
        }
        
        //$token = explode(".", $header)[1];
        // Use try-catch
        // JWT library throws exception if the token is not valid
        try {
            // Validate the token
            // Successfull validation will return the decoded user data else returns false
            $data = AUTHORIZATION::validateToken($header);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
            } else {
                $response = ['status' => 200, 'msg' => $data];
            }
            return $response;
        } catch (Exception $e) {
            // Token is invalid
            // Send the unathorized access message
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            //$this->response($response, $status);
            return $response;
        }
    }
}

Class OrderData{
    public $username;
    public $phone;
    public $address;
    public $price_cat;
    public $weight;
    public $price;
    public $status;
}