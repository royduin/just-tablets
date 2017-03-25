<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load models
		$this->load->model('news_model');
		$this->load->model('filter_model');
	}

	public function index($id = FALSE)
	{
		if($id AND $id != 'pagina')
		{
			$data = $this->news_model->get_item($id);
			if(empty($data)){
				show_404();
			} else {
				$data 						= $data[0];
				$data['page_title'] 		= $data['title'];
				$data['page_description']	= $data['description'];
				$data['page_canonical']		= site_url().'tablet-nieuws/'.$data['news_id'].'/'.$data['title_url'].'/';
				$view = 'news_item';
			}

			// Enable caching
			$this->output->cache( $this->config->item('cache_week') );

		} else {

			//Load pagination library
			$this->load->library('pagination');

			// Set pagination
			$config['uri_segment'] 		= 3;
			$config['per_page'] 		= 10;
			$config['base_url'] 		= site_url().'tablet-nieuws/pagina';
			$config['first_url']		= site_url().'tablet-nieuws/';
			$config['total_rows'] 		= $this->db->count_all('tb_news');
			$offset 					= calculate_offset($this->uri->segment($config['uri_segment']),$config);
			$this->pagination->initialize($config);

			// Set data for view
			$data['pagination']			= $this->pagination->create_links();
			$data['items'] = $this->news_model->get_items($offset,$config['per_page']);
			$view = 'news';

			// Set pagination info (title, description, canocial, next and prev)
			$data = array_merge(
				$data,
				pagination_info(
					$this->uri->segment($config['uri_segment']),
					$config,
					'Tablets nieuws',
					'Alle nieuwtes over tablets!',
					'nieuws over tablets'
				)
			);

			// Enable caching
			$this->output->cache( $this->config->item('cache_cron') );

		}

		$data['brands'] = $this->filter_model->get_brands();

		//Load views
		$this->load->view('layout/header',$data);
		$this->load->view($view,$data);
		$this->load->view('layout/footer');
	}

}

/* End of file nieuws.php */
/* Location: ./application/controllers/nieuws.php */