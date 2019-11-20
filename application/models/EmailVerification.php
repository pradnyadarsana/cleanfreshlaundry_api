<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailVerification extends CI_Model
{
    private $table = 'data_user';

    // public $id;
    // public $name;
    // public $phone;
    // public $email;
    // public $username;
    // public $password;
    // public $gender;
    // public $activation_status;
    // public $verif_code;
    // public $api_token;

    // public $rule = [ 
    //     [
    //         'field' => 'name',
    //         'label' => 'name',
    //         'rules' => 'required'
    //     ],
    //     [
    //         'field' => 'phone',
    //         'label' => 'phone',
    //         'rules' => 'required'
    //     ],
    //     [
    //         'field' => 'email',
    //         'label' => 'email',
    //         'rules' => 'required'
    //     ],
    //     [
    //         'field' => 'username',
    //         'label' => 'username',
    //         'rules' => 'required'
    //     ],
    //     [
    //         'field' => 'password',
    //         'label' => 'password',
    //         'rules' => 'required'
    //     ],
    //     [
    //         'field' => 'gender',
    //         'label' => 'gender',
    //         'rules' => 'required'
    //     ],
    // ];

    // public function Rules() { return $this->rule; }
   
    // public function getAll() { return 
    //     $this->db->get('data_user')->result(); 
    // } 

    // public function store($request) { 
    //     $this->name = $request->name;
    //     $this->phone = $request->phone; 
    //     $this->email = $request->email;
    //     $this->username = $request->username;
    //     $this->password = password_hash($request->password, PASSWORD_BCRYPT);
    //     $this->gender = $request->gender;
    //     $this->activation_status = 0;
    //     $this->verif_code = 0;//md5(rand(1000,9999));
        
    //     if($this->db->insert($this->table, $this)){
    //         $result = $this->db->get_where('data_user', ["username" => $this->username])->row();
    //         $id = $result->id;
    //         $encrypted_id = md5($id);
            
    //         //email
    //         $config['mailtype'] = 'html';
    //         $config['charset'] = 'utf-8';
    //         $config['protocol'] = 'smtp';
    //         $config['smtp_host'] = 'ssl://smtp.gmail.com';
    //         $config['smtp_user'] = 'Pawlaundry1@gmail.com';
    //         $config['smtp_pass'] = '#tugaspawUTS';
    //         $config['smtp_port'] = 465;
    //         $config['newline'] = "\r\n";
    //         $config['clrf'] = "\r\n";

    //         $verif_link = site_url("user/userUpdate/$encrypted_id");

    //         $message = '
    //         Thank you for join with us!
    //         Your account is registered now, you need verify your account for login to Clean Fresh Laundry

    //         ---------------------------------------
    //         Email: '.$this->email.'
    //         Username: '.$this->username.'
    //         ---------------------------------------

    //         This is your verification link: 
    //         '.$verif_link;

    //         $this->load->library('email', $config);

    //         $this->email->from('Clean Fresh Laundry', 'Clean Fresh Laundry');
    //         $this->email->to($email);
    //         $this->email->subject('VERIFICATION EMAIL | CLEAN FRESH LAUNDRY');
    //         $this->email->message($message);

    //         if($this->email->send()) {
    //             return ['msg'=>'Berhasil, silahkan cek email verifikasi','error'=>false];
    //         }
    //         else {
    //             return ['msg'=>'Gagal mengirim email verifikasi','error'=>true];
    //             //echo $this->email->print_debugger();
    //         }
    //     }
    //     return ['msg'=>'Gagal terdaftar','error'=>true];
    // }

    public function send_mail($user, $id)
    {
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_user'] = 'Pawlaundry1@gmail.com';
        $config['smtp_pass'] = '#tugaspawUTS';
        $config['smtp_port'] = 465;
        $config['newline'] = "\r\n";
        $config['clrf'] = "\r\n";

        $verif_link = site_url("user/userUpdate/$id/$user->verif_code");

        $message = '
        <h2>Thank you for join with us!</h2>
        <p>Your account is registered now, you need verify your account for login to Clean Fresh Laundry</p>
        <br></br>
        <p>---------------------------------------</p>
        <p>Email: '.$user->email.'</p>
        <p>Username: '.$user->username.'</p>
        <p>---------------------------------------</p>
        <br></br>
        <h3>This is your verification link:</h3>
        <form method="post" action="'.$verif_link.'">
        <button type="submit">Activate My Account<button>';

        $this->load->library('email', $config);

        $this->email->from('Clean Fresh Laundry', 'Clean Fresh Laundry');
        $this->email->to($user->email);
        $this->email->subject('VERIFICATION EMAIL | CLEAN FRESH LAUNDRY');
        $this->email->message($message);

        if($this->email->send()) {
            return ['msg'=>'Berhasil, silahkan cek email verifikasi','error'=>false];
        }
        else {
            return ['msg'=>'Gagal mengirim email verifikasi','error'=>true];
            //echo $this->email->print_debugger();
        }
    }
}
?>