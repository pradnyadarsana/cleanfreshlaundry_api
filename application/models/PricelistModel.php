<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PricelistModel extends CI_Model
{
    private $table = 'data_pricelist';

    public $id;
    public $category;
    public $duration;
    public $price;

    public $rule = [ 
        [
            'field' => 'category',
            'label' => 'category',
            'rules' => 'required|alpha_numeric'
        ],
        [
            'field' => 'duration',
            'label' => 'duration',
            'rules' => 'required|integer'
        ],
        [
            'field' => 'price',
            'label' => 'price',
            'rules' => 'required|integer'
        ],
    ];

    public function Rules() { return $this->rule; }
   
    public function getAll() { return 
        $this->db->get('data_pricelist')->result(); 
    } 

    public function store($request) { 
        $this->category = $request->category; 
        $this->duration = $request->duration; 
        $this->price = $request->price; 
        if($this->db->insert($this->table, $this)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function update($request,$id) { 
        $updateData = [
            'category' => $request->category, 
            'duration' => $request->duration,
            'price' => $request->price,
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