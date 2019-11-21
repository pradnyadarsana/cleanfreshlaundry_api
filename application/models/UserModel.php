<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include('EmailVerification.php');

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

        //generate simple random code
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle($set), 0, 20);
        $this->verif_code = $code;//md5(rand(1000,9999));
        
        if($this->db->insert($this->table, $this)){
            $result = $this->db->get_where('data_user', ["username" => $this->username])->row();
            $id = $result->id;
            //$encrypted_id = md5($id);
            $mailMan = new EmailVerification();
            $result = $mailMan->send_mail($this, $id);
            return $result;
        }
        return ['msg'=>'Gagal terdaftar','error'=>true];
    }

    public function userUpdate($id, $key) {
        $user = $this->db->get_where('data_user', ["id" => $id])->row();
        if($user->verif_code == $key){
            $updateData = [ 
                'activation_status' => '1'
            ];
            if($this->db->where('id',$id)->update($this->table, $updateData)){
                return ['msg'=>'Berhasil','error'=>false];
            }
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
            'password' => password_hash($request->password, PASSWORD_BCRYPT)
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
        if(!empty($user) && password_verify($request->password, $user['password'])){
            if($user['activation_status'] == 1){
                return $user;
            }
            return false;
        }else{
            return false;
        }
    }
}
?>