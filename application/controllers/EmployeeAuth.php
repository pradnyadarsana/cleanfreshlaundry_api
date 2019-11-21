<?php 
use Restserver \Libraries\REST_Controller ; 

Class EmployeeAuth extends REST_Controller{ 
    
    public function __construct(){
        header('Access-Control-Allow-Origin: *');         
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");         
        header("Access-Control-Allow-Headers: Authorization, Content-Type, ContentLength, Accept-Encoding");         
        parent::__construct();         
        $this->load->model('EmployeeModel');         
        $this->load->library('form_validation');     
        $this->load->helper(['jwt', 'authorization']);   
    }    

    public $rule = [  
        [                     
            'field' => 'username',                     
            'label' => 'username',                     
            'rules' => 'required'                 
        ],
        [                     
            'field' => 'password',                     
            'label' => 'password',                     
            'rules' => 'required'                 
        ]
    ];     
    
    public function Rules() { return $this->rule; }     

    public function index_post(){
        $validation = $this->form_validation;         
        $rule = $this->Rules();            
        $validation->set_rules($rule);         
        if (!$validation->run()) {             
            return $this->response($this->form_validation->error_array());         
        }        

        $employee = new EmployeeData();
        $employee->username = $this->post('username');
        $employee->password = $this->post('password');

        if($result = $this->EmployeeModel->verify($employee)){
            
            $token = AUTHORIZATION::generateToken(['ID' => $result['id'],'username' => $result['username']]);
            //set new api token to database
            $this->EmployeeModel->updateToken($token,$employee->username);
            //return user data
            $data = [
                'token' => $token,
                'user' => $result
            ];
            // Set HTTP status code
            $status = parent::HTTP_OK;
            // Prepare the response
            $response = ['status' => $status, 'data' => $data];
            // REST_Controller provide this method to send responses
            return $this->response($response, $status);
    
        }else{
            return $this->response('Username or password is wrong');
        }
    }

    public function index_get(){
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
        
        $CI =& get_instance();
        return JWT::decode($header, $CI->config->item('jwt_key'));
        
    }

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }

    public function deleteToken_post($token){
        $response = $this->EmployeeModel->deleteToken($token);
        return $this->returnData($response['msg'], $response['error']);
    }

} 

Class EmployeeData{     
    public $name;     
    public $password;     
    public $username; 
}