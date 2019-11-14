<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model
{
    private $table = 'data_user';

    public $id;
    public $name;
    public $phone;
    public $email;
    public $username;
    public $password;
    public $gender;
    public $activation_status;
    public $verif_code;
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
            'field' => 'password',
            'label' => 'password',
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
        $this->password = password_hash($request->password, PASSWORD_BCRYPT);
        $this->gender = $request->gender;
        $this->activation_status = 0;
        $this->verif_code = md5(rand(1000,9999));
        $this->api_token = md5(rand(1000,9999));
         
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
            'password' => password_hash($request->password, PASSWORD_BCRYPT), 
            'gender' => $request->gender
        ];
        if($this->db->where('id',$id)->update($this->table, $updateData)){
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
}
?>