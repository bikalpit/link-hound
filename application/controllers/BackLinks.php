<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BackLinks extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->helper('common_helper');
       
        include APPPATH . 'third_party/RestClient.php';
        
    }

    public function index()
    {
       $this->load->view('backlinks/index');
    }

    public function buttontempary()
    {
       $this->load->view('backlinks/backlinks');
    }

    public function data(){
    	$data = array('');
    	// $this->load->view('backlinks/backlinks',$data);
    	echo json_encode($data);
    }
}
?>