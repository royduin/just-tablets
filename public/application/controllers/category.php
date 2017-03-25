<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load pagination library
		$this->load->library('pagination');

		// Load models
		$this->load->model('product_model');
		$this->load->model('filter_model');
	}

	public function index($hash = FALSE)
	{
		// Page?
		if($this->input->get('page') )
		{
			// Get current filter options
			$query = $this->db->query("SELECT filters FROM tb_filter WHERE filter_mid=".$this->db->escape($this->input->get('filter')));
			$filter = unserialize($query->row_array()['filters']);

			$filter['page'] 	= $this->input->get('page');
			$filter 			= serialize($filter);

			// Get hash and insert new filter
			$hash = md5($filter);
			$this->db->query("INSERT IGNORE INTO tb_filter (filter_mid,filters) VALUES (".$this->db->escape($hash).",".$this->db->escape($filter).")");

			// Redirect
			redirect(site_url().'tablets-vergelijken/#filter:'.$hash,'location',301);
		}

		// Sorting?
		if($this->input->get('sort') AND $this->input->get('sort_dir'))
		{
			// Filter set?
			if($this->input->get('filter'))
			{
				// Get current filter options
				$query = $this->db->query("SELECT filters FROM tb_filter WHERE filter_mid=".$this->db->escape($this->input->get('filter')));
				$filter = unserialize($query->row_array()['filters']);
			} else {
				// Set defaults
				$filter['buyable'] 	= true;
			}

			// Specification sorting set?
			if($this->input->get('sort_spec')){
				$filter['sort_spec'] = $this->input->get('sort_spec');
			}

			// Set sorting settings
			$filter['sort'] 	= $this->input->get('sort');
			$filter['sort_dir']	= $this->input->get('sort_dir');
			$filter 			= serialize($filter);

			// Get hash and insert new filter
			$hash = md5($filter);
			$this->db->query("INSERT IGNORE INTO tb_filter (filter_mid,filters) VALUES (".$this->db->escape($hash).",".$this->db->escape($filter).")");

			// Redirect
			redirect(site_url().'tablets-vergelijken/#filter:'.$hash,'location',301);
		}

		// Set pagination
		$config['uri_segment'] 		= 3;
		$config['per_page'] 		= 25;
		$config['base_url'] 		= site_url().'tablets-vergelijken/pagina';
		$config['first_url']		= site_url().'tablets-vergelijken/';
		$config['total_rows'] 		= $this->product_model->count_total_products();
		$offset 					= calculate_offset($this->uri->segment($config['uri_segment']),$config);
		$this->pagination->initialize($config);
		
		// Set data for view
		$data = array(
			'tablets' 		=> $this->product_model->get_products($offset,$config['per_page']),
			'brands'		=> $this->filter_model->get_brands(),
			'filters'		=> $this->filter_model->get_filters(),
			'spec_filters'	=> $this->filter_model->get_spec_filters(),
			'pagination'	=> $this->pagination->create_links(),
			'total_amount'	=> $config['total_rows'],
			'total_pages'	=> ceil($config['total_rows'] / $config['per_page']),
			'prices'		=> $this->filter_model->get_prices(),
			'total'			=> $this->db->count_all('tb_product')
		);

		// echo '<pre>'.print_r($data['tablets'],TRUE).'</pre>';

		// Set pagination info (title, description, canocial, next and prev)
		$data = array_merge(
			$data,
			pagination_info(
				$this->uri->segment($config['uri_segment']),
				$config,
				'Tablets vergelijken',
				'Bekijk en vergelijk de prijzen en specificaties eenvoudig van alle '.$data['total'].' tablets bij Just Tablets.',
				'tablets welke momenteel te koop zijn.'
			)
		);

		// Enable caching
		$this->output->cache( $this->config->item('cache_cron') );

		// Load views
		$this->load->view('layout/header',$data);
		$this->load->view('category',$data);
		$this->load->view('layout/footer');
	}

}

/* End of file tablets.php */
/* Location: ./application/controllers/tablets.php */