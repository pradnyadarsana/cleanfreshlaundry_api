<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeModel extends CI_Model
{
    private $table = 'data_employee';

    public $id;
    public $name;
    public $phone;
    public $email;
    public $username;
    public $password;
    public $gender;
    public $api_token;

    public $rule = [ 
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
    ];

    public function Rules() { return $this->rule; }
   
    public function getAll() { return 
        $this->db->get('data_user')->result(); 
    } 

    public function store($request) { 
        $this->name = $request->name;
        $this->phone = $request->phone; 
        $this->email = $request->email;
        $this->username = $request->username;
        $this->password = $request->password;
        $this->gender = $request->gender;
         
        if($this->db->insert($this->table, $this)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function update($request,$id) { 
        $updateData = [ 
            'name' => $request->name, 
            'phone' => $request->phone,
            'email' => $request->email, 
            'username' => $request->username, 
            'gender' => $request->gender
        ];
        if($this->db->where('id',$id)->update($this->table, $updateData)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function updatePassword($request,$id) { 
        $updateData = [  
            'password' => $request->password
        ];
        if($this->db->where('id',$id)->update($this->table, $updateData)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function updateToken($token, $username){
        $updateData = [
            'api_token' => $token
        ];
        $this->db->where('username',$username)->update($this->table, $updateData);
    }

    public function deleteToken($token){
        $updateData = [
            'api_token' => NULL
        ];
        if($this->db->where('api_token',$token)->update($this->table, $updateData)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }
    
    public function destroy($id){
        if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) return ['msg'=>'Id tidak ditemukan','error'=>true];
        
        if($this->db->delete($this->table, array('id' => $id))){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function verify($request){
        $user = $this->db->select('*')->where(array('username' => $request->username))->get($this->table)->row_array();
        if(!empty($user) && $request->password==$user['password']){
            return $user;
        }else{
            return false;
        }
    }
}
?>