<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model
{
    private $table = 'data_order';

    public $id;
    public $username;
    public $phone;
    public $address;
    public $price_cat;
    public $weight;
    public $price;
    public $status;
    public $created_at;

    public $rule = [ 
        [
            'field' => 'address',
            'label' => 'address',
            'rules' => 'required'
        ],
        [
            'field' => 'price_cat',
            'label' => 'price_cat',
            'rules' => 'required'
        ],
        [
            'field' => 'weight',
            'label' => 'weight',
            'rules' => 'required|integer'
        ],
        [
            'field' => 'status',
            'label' => 'status',
            'rules' => 'required'
        ]
        
    ];

    public function Rules() { return $this->rule; }
   
    public function getAll() { return 
        $this->db->get('data_order')->result(); 
    } 

    public function store($request) {
        $this->username = $request->username;
        $this->phone = $request->phone; 
        $this->address = $request->address; 
        $this->price_cat = $request->price_cat;
        $this->weight = $request->weight;
        $this->price = $request->price;
        $this->status = $request->status; 

        if($this->db->insert($this->table, $this)){
            return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }

    public function update($request,$id) { 
        $updateData = [
            'address' =>$request->address, 
            'price_cat' =>$request->price_cat,
            'weight' => $request->weight,
            'price' => $request->price,
            'status' => $request->status,
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