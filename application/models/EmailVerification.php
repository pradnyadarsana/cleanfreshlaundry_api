<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailVerification extends CI_Model
{
    private $table = 'data_user';

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
        }
    }
}
?>