<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		// Load model
		$this->load->model('filter_model');

		// Load Taggly library
		$this->load->library('Taggly');
	}

	public function index()
	{
		// Get the history
		$query = $this->db->query("SELECT word,COUNT(word) as amount FROM tb_search_history GROUP BY word");
		
		// Generate taggly array
		foreach($query->result_array() as $result)
		{
			$taggly[] = [$result['amount'],$result['word'],site_url().'zoeken/'.$result['word'].'/'];
		}

		// Set taggly
		$data['taggly'] = $this->taggly->cloud($taggly);

		// Posted?
		if($this->input->post('s'))
		{
			// Insert in history
			$this->db->query("INSERT INTO tb_search_history (word) VALUES (".$this->db->escape(str_replace('-',' ',url_title($this->input->post('s'),'-',TRUE))).")");

			// Redirect
			redirect( site_url().'zoeken/'.url_title($this->input->post('s'),'-',TRUE) , 'location' , 301 );

		} else {

			// Replace dashes with spaces
			$data['s']	= str_replace('-',' ',$this->uri->segment(2));

			// Something set?
			if($data['s'])
			{
				$query = $this->db->query("
					SELECT DISTINCT
						tb_product_search.product_id,
						title,
						title_url,
						img_thumb,
						img_low,
						descr_short,
						round(tb_product_search.popularity * 100 / (
							SELECT popularity
							FROM tb_product_search
							ORDER BY popularity DESC
							LIMIT 1
						)) as popularity,
						price
					FROM
						tb_product_search
					LEFT JOIN
						tb_ean
					ON
						tb_ean.product_id = tb_product_search.product_id
					LEFT JOIN
						tb_price
					ON
						tb_price.product_id = tb_product_search.product_id
					AND
						tb_price.price = 
						(
							SELECT min(price)
							FROM tb_price
							WHERE tb_price.product_id = tb_product_search.product_id
						)
					WHERE
						tb_product_search.product_id = ".$this->db->escape($data['s'])." OR 
						sku = ".$this->db->escape($data['s'])." OR 
						MATCH (name,title,descr_long,descr_short,tb_product_search.url) AGAINST (".$this->db->escape($data['s']).") OR 
						ean = ".$this->db->escape($data['s'])."
				");

				// Get results
				$data['count'] 	= $query->num_rows();
				$data['result'] = $query->result_array();

				// Remove history items with no results
				if(!$data['count'])
				{
					$this->db->query("DELETE FROM tb_search_history WHERE word=".$this->db->escape($data['s']));
				}
			}

			// Enable caching
			$this->output->cache( $this->config->item('cache_cron') );

			// Brands for the footer
			$data['brands'] = $this->filter_model->get_brands();

			// Load the views
			$this->load->view('layout/header',$data);
			$this->load->view('search',$data);
			$this->load->view('layout/footer');

		}
	}

}

/* End of file search.php */
/* Location: ./application/controllers/search.php */