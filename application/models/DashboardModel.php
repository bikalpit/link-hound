<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DashboardModel extends CI_Model {

  public $table_related_keyword="related_keyword";
  
  function __construct(){
    parent::__construct();
  }

  public function select($order_by = ''){ 

    $this->db->order_by($order_by,'desc');
    $query = $this->db->get($this->table_related_keyword);
    return $query->result_array();
  }
  
  public function select_one($where=[]){   
    $this->db->where($where);
    $this->db->limit(1);
    return $this->db->get($this->table_related_keyword)->row_array();
  }

  public function select_where($where=[],$order_by=[]){ 
    $this->db->order_by($order_by);  
    $this->db->where($where);
    return $this->db->get($this->table_related_keyword)->result_array();
  }

  public function insert($insertArr=[]){
    $this->db->insert($this->table_related_keyword,$insertArr);
    return $this->db->insert_id();
  }

  public function update($arrFilede=[]){
    $this->db->where($where);
    $this->db->limit(1);
    return $this->db->update($this->table_related_keyword, $arrFilede);
  }

  public function delete($where=[]){
    $this->db->where($where);
    return $this->db->delete($this->table_related_keyword);
  }

}