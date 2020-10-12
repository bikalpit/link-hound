<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function login()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p style="color:red;">', '</p>');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('auth/login');
		} else {
			$email = $this->input->post('email');
			$password = md5($this->input->post('password'));
			$result = $this->db->query("SELECT * FROM users WHERE email = '$email' AND password = '$password'");

			if (sizeof($result->result()) == 1) {
				$this->session->set_userdata('user', $result->result());
				$user = $this->session->userdata('user')[0];
				if($user->role == 'admin'){
					redirect('/dashboard');
				}
				redirect('/client');
			} else {
				$this->session->set_flashdata('error', 'Email or Password is wrong');
				redirect('/welcome/login');
			}
		}
	}
	public function register()
	{	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p style="color:red;">', '</p>');
		$this->form_validation->set_rules('name', 'User Name', 'required|min_length[3]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'required|matches[c_password]');
		$this->form_validation->set_rules('c_password', 'Confirm Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('auth/register',$this->input->post);
		} else {
			// var_dump($this->input->post);
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$password = md5($this->input->post('password'));
			$c_password = md5($this->input->post('c_password'));

			if($password == $c_password){

				$result = $this->db->query("INSERT INTO users(name,email,password,role) VALUES('$name','$email','$password','client')");
				
				if ($result) {
					$result = $this->db->query("SELECT * FROM users WHERE email = '$email' AND password = '$password'");
					$this->session->set_userdata('user', $result->result());
					$user = $this->session->userdata('user')[0];
					if($user){
						$this->load->view('my_stripe');
					}
					// $this->session->set_flashdata('success', 'Successfully register done.');
					// redirect('/welcome/login');

				}else{
					$this->session->set_flashdata('error', 'Something was wrong');
					redirect('/welcome/register');
				}
			}else{
				$this->session->set_flashdata('error', 'Password and confirm password does not match');
				redirect('/welcome/register');
			}
		}
	}
}
