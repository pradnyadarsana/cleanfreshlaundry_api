<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReviewModel extends CI_Model
{
    private $table = 'data_review';

    public $id;
    public $username;
    public $rate;
    public $category;
    public $description;
    public $created_at;

    public $rule = [ 
        [
            'field' => 'rate',
            'label' => 'rate',
            'rules' => 'required|integer'
        ],
        [
            'field' => 'category',
            'label' => 'category',
            'rules' => 'required'
        ],
    ];

    public function Rules() { return $this->rule; }
   
    public function getAll() { return 
        $this->db->get('data_review')->result(); 
    } 

    public function store($request) { 
        $this->username = $request->username; 
        $this->rate = $request->rate; 
        $this->category = $request->category;
        $this->description = $request->description;

        if($this->db->insert($this->table, $this)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function update($request,$id) { 
        $updateData = [
            'rate' =>$request->rate, 
            'category' =>$request->category, 
            'description' =>$request->description
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