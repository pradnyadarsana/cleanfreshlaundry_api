<?php
use chriskacerguis\RestServer\RestController; 
Class User extends RestController{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->library('form_validation');
        $this->load->helper(['jwt','authorization']);
    }

    public function index_get(){
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        if($data['status'] == 401){
            return $this->returnData($data['msg'],true);
        }
        return $this->returnData($this->db->get('data_user')->result(), false);
    }

    public function oneUser_get($username){
        return $this->returnData($this->db->get_where('data_user', ["username" => $username])->result(), false);
    }

    public function userByToken_get($token){
        return $this->returnData($this->db->get_where('data_user', ["api_token" => $token])->result(), false);
    }

    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->UserModel->rules();
        if($id == null){
            array_push($rule,[
                    'field' => 'name',
                    'label' => 'name',
                    'rules' => 'required'
                ],
                [
                    'field' => 'phone',
                    'label' => 'phone',
                    'rules' => 'required|numeric'
                ],
                [
                    'field' => 'email',
                    'label' => 'email',
                    'rules' => 'required|valid_email|is_unique[data_user.email]'
                ],
                [
                    'field' => 'username',
                    'label' => 'username',
                    'rules' => 'required|alpha_numeric|is_unique[data_user.username]'
                ],
                [
                    'field' => 'password',
                    'label' => 'password',
                    'rules' => 'required'
                ],
                [
                    'field' => 'gender',
                    'label' => 'gender',
                    'rules' => 'required'
                ],
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'name',
                    'label' => 'name',
                    'rules' => 'required'
                ],
                [
                    'field' => 'phone',
                    'label' => 'phone',
                    'rules' => 'required'
                ],
                [
                    'field' => 'email',
                    'label' => 'email',
                    'rules' => 'required'
                ],
                [
                    'field' => 'username',
                    'label' => 'username',
                    'rules' => 'required'
                ],
                [
                    'field' => 'gender',
                    'label' => 'gender',
                    'rules' => 'required'
                ],
            );
        }
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }
        $user = new UserData();
        $user->name = $this->post('name');
        $user->phone = $this->post('phone');
        $user->email = $this->post('email');
        $user->username = $this->post('username');
        $user->password = $this->post('password');
        $user->gender = $this->post('gender');
        if($id == null){
            $response = $this->UserModel->store($user);
        }else{
            $response = $this->UserModel->update($user,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function updatePassword_post($id){
        $user = new UserData();
        $user->password = $this->post('password');
        $response = $this->UserModel->updatePassword($user, $id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function userUpdate_post($id, $key){
        $response = $this->UserModel->userUpdate($id, $key);
        //return $this->returnData($response['msg'], $response['error']);
        return $this->load->view('verification_success');
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->UserModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
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

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class UserData{
    public $name;
    public $phone;
    public $email;
    public $username;
    public $password;
    public $gender;
    public $verif_code;
}