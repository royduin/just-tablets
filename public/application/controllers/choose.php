<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Choose extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load filter model
		$this->load->model('filter_model');
	}

	public function index()
	{
		$data['page_title'] 		= 'Tablets kiezen';
		$data['page_description']	= 'Geen idee welke tablet bij je past? Doorloop de tablet keuze wizard en vindt de tablet die bij je past!';

		$data['filters'] 			= $this->filter_model->get_filters();
		$data['brands'] 			= $this->filter_model->get_brands();
		$data['prices']				= $this->filter_model->get_prices();

		// Enable caching
		$this->output->cache( $this->config->item('cache_day') );

		// Load the views
		$this->load->view('layout/header',$data);
		$this->load->view('choose',$data);
		$this->load->view('layout/footer');
	}

}

/* End of file keuze.php */
/* Location: ./application/controllers/keuze.php */