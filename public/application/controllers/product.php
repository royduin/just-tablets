<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load models
		$this->load->model('product_model');
		$this->load->model('filter_model');
	}

	public function index($id = FALSE)
	{
		// No ID?
		if(!$this->uri->segment(2))
		{
			// Redirect to the comparison page
			redirect(site_url().'tablets-vergelijken/','location',301);
		}

		// Get product data
		$product_data = $this->product_model->get_product($id);

		// No product data?
		if(!$product_data)
		{
			// Maybe an old product/Icecat ID?
			$old = $this->product_model->get_product_icecat($id);

			// Still nothting?
			if(empty($old))
			{
				// 404
				show_404();

			} else
			{
				// Redirect to the new url
				redirect(site_url().'tablet/'.$old['product_id'].'/'.$old['title_url'].'/','location',301);
			}
		}

		// No name?
		if(!$this->uri->segment(3))
		{
			// Redirect to the url with name
			redirect(site_url().'tablet/'.$product_data['product_id'].'/'.$product_data['title_url'].'/','location',301);
		}

		// Set data for view
		$data 						= $product_data;
		$data['features'] 			= $this->product_model->get_product_features($id);
		$data['specs'] 				= $this->product_model->get_product_specs($id);
		$data['prices'] 			= $this->product_model->get_product_prices($id);
		$data['reviews'] 			= $this->product_model->get_product_reviews($id);
		$data['page_title'] 		= $data['title'];
		$data['page_description']	= $data['descr_short'];
		$data['page_canonical']		= site_url().'tablet/'.$product_data['product_id'].'/'.$product_data['title_url'].'/';
		$data['brands']				= $this->filter_model->get_brands();

		// Enable caching
		$this->output->cache( $this->config->item('cache_cron') );

		// Load the views
		$this->load->view('layout/header',$data);
		$this->load->view('product',$data);
		$this->load->view('layout/footer');
	}

}

/* End of file tablet.php */
/* Location: ./application/controllers/tablet.php */