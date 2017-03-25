<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load models
		$this->load->model('filter_model');
		$this->load->model('ajax_model');
	}

	public function brand($brand = FALSE)
	{
		if(!$brand){
			show_404();
		}

		$brand 				= $this->db->query("SELECT * FROM tb_brand WHERE name_url=".$this->db->escape($brand));
		$data['brand'] 		= $brand->row_array();

		if(!count($data['brand'])){
			show_404();
		}

		$products = $this->db->query("
			SELECT DISTINCT
					tb_product.product_id,
					title,
					title_url,
					img_thumb,
					img_low,
					descr_short,
					round(tb_product.popularity * 100 / (
						SELECT popularity
						FROM tb_product
						ORDER BY popularity DESC
						LIMIT 1
					)) as popularity,
					price
				FROM
					tb_product
				LEFT JOIN
					tb_ean
				ON
					tb_ean.product_id = tb_product.product_id
				LEFT JOIN
					tb_price
				ON
					tb_price.product_id = tb_product.product_id
				AND
					tb_price.price = 
					(
						SELECT min(price)
						FROM tb_price
						WHERE tb_price.product_id = tb_product.product_id
					)
				WHERE
					brand_id=".$this->db->escape($data['brand']['brand_id'])."
				ORDER BY
					price DESC, popularity DESC
		");

		$data['products']	= $products->result_array();

		$families = $this->db->query("
			SELECT DISTINCT
				tb_family.name
			FROM
				tb_product
			INNER JOIN
				tb_family
			ON
				tb_family.family_id = tb_product.family_id	

			WHERE
				brand_id=".$this->db->escape($data['brand']['brand_id'])."
		");

		// Create description
		$families = array_map(function($item) { return $item['name']; }, $families->result_array());
		if(!count($families)){
			$description = $data['brand']['name'].'.';
		} elseif(count($families) == 1){
			$description = $data['brand']['name'].' heeft de '.$families[0].' tablets.';
		} else {
			$description = implode(', ', $families);
			$description = preg_replace('/(.*),/','$1 en de',$description);
			$description = $data['brand']['name'].' heeft '.count($families).' verschillende tablet series. De '.$description.'.';
		}
		$description .= ' Bij Just Tablets vindt je al deze tablets! Bekijk en vergelijk ze eenvoudig.';

		// Set page information
		$data['page_title'] 		= $data['brand']['name'].' tablets';
		$data['page_description']	= $description.' '.$data['brand']['descr'];

		// Load brands for the footer
		$data['brands'] = $this->filter_model->get_brands();

		// Hash for comparison
		$data['hash'] 	= $this->ajax_model->set_filter(array('brands' => array($data['brand']['brand_id'])));

		//Load views
		$this->load->view('layout/header',$data);
		$this->load->view('page',$data);
		$this->load->view('layout/footer');
	}

}

/* End of file pages.php */
/* Location: ./application/controllers/pages.php */