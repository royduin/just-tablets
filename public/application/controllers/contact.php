<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load model
		$this->load->model('filter_model');
	}

	public function index()
	{
		// Set view data
		$data['page_title'] 		= 'Contact';
		$data['page_description']	= 'Neem contact op met justtablets.nl';
		$data['brands']				= $this->filter_model->get_brands();

		// Enable caching
		$this->output->cache( $this->config->item('cache_day') );

		// Load the views
		$this->load->view('layout/header',$data);
		$this->load->view('contact',$data);
		$this->load->view('layout/footer');
	}

}

/* End of file contact.php */
/* Location: ./application/controllers/contact.php */