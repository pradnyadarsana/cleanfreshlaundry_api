<?php
use chriskacerguis\RestServer\RestController; 
Class Employee extends RestController{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('EmployeeModel');
        $this->load->library('form_validation');
        $this->load->helper(['jwt','authorization']);
    }

    public function index_get(){
        return $this->returnData($this->db->get('data_employee')->result(), false);
    }

    public function oneEmployee_get($username){
        return $this->returnData($this->db->get_where('data_employee', ["username" => $username])->result(), false);
    }

    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->EmployeeModel->rules();
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
                    'rules' => 'required|valid_email|is_unique[data_employee.email]'
                ],
                [
                    'field' => 'username',
                    'label' => 'username',
                    'rules' => 'required|alpha_numeric|is_unique[data_employee.username]'
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
                    'rules' => 'required|numeric'
                ],
                [
                    'field' => 'email',
                    'label' => 'email',
                    'rules' => 'required|valid_email'
                ],
                [
                    'field' => 'username',
                    'label' => 'username',
                    'rules' => 'required|alpha_numeric'
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
        $employee = new EmployeeData();
        $employee->name = $this->post('name');
        $employee->phone = $this->post('phone');
        $employee->email = $this->post('email');
        $employee->username = $this->post('username');
        $employee->password = $this->post('password');
        $employee->gender = $this->post('gender');
        if($id == null){
            $response = $this->EmployeeModel->store($employee);
        }else{
            $response = $this->EmployeeModel->update($employee,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function updatePassword_post($id){
        $employee = new EmployeeData();
        $employee->password = $this->post('password');
        $response = $this->EmployeeModel->updatePassword($employee, $id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->EmployeeModel->destroy($id);
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

Class EmployeeData{
    public $name;
    public $phone;
    public $email;
    public $username;
    public $password;
    public $gender;
}