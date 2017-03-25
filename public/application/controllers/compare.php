<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Compare extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		// Load model
		$this->load->model('filter_model');
	}

	public function index()
	{
		$products 		= $this->uri->segment(2);
		$products_array = explode('-',$products);
		foreach($products_array as $id)
		{
			// Not numeric one?
			if(!is_numeric($id))
			{
				show_404();
			}
		}
		$products = implode(',',$products_array);

		$compare = $this->db->query("
			SELECT DISTINCT
				tb_product.product_id,
				tb_product.name,
				title,
				title_url,
				sku,
				(
					SELECT GROUP_CONCAT(ean)
					FROM tb_ean
					WHERE tb_ean.product_id=tb_product.product_id
				) AS ean,
				`release`,
				img_thumb,
				tb_product.url,
				warranty,
				round(tb_product.popularity * 100 / (
					SELECT popularity
					FROM tb_product
					ORDER BY popularity DESC
					LIMIT 1
				)) as popularity,
				tb_brand.brand_id,
				tb_brand.name as brand_name,
				tb_brand.name_url as brand_name_url,
				price
			FROM
				tb_product
			LEFT JOIN
				tb_brand
			ON 
				tb_product.brand_id = tb_brand.brand_id
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
				tb_product.product_id IN (".$products.")
			ORDER BY
				tb_product.product_id ASC
		");

		$specs = $this->db->query("
			SELECT
				tb_spec_fk.product_id,
				tb_spec_group.name AS cat,
				tb_spec_feature.name AS name,
				CONCAT_WS(' ',tb_spec_value.name,tb_spec_sign.name) AS value
			FROM
				tb_spec_fk
			INNER JOIN
				tb_spec_group
			ON
				tb_spec_group.group_id = tb_spec_fk.group
			INNER JOIN
				tb_spec_feature
			ON
				tb_spec_feature.feature_id = tb_spec_fk.feature
			INNER JOIN
				tb_spec_value
			ON
				tb_spec_value.value_id = tb_spec_fk.value
			LEFT JOIN
				tb_spec_sign
			ON
				tb_spec_sign.sign_id = tb_spec_fk.sign
			WHERE
				tb_spec_fk.product_id IN (".$products.")
			ORDER BY tb_spec_group.name,tb_spec_feature.name ASC
		");

		$products = $compare->result_array();

		// No products?
		if(!count($products)){
			show_404();
		}

		// Set page title
		$ids = array_map(function($item) { return $item['title']; }, $products);
		$title = 'De '.implode(', ', $ids).' met elkaar vergeleken.';
		$title = preg_replace('/(.*),/','$1 en de',$title);

		// Set data for the views
		$data = [
			'page_title'			=> 'Tablets vergelijken',
			'page_description'		=> $title,
			'products'				=> $products,
			'specs'					=> $specs->result_array(),
			'brands'				=> $this->filter_model->get_brands()
		];

		// Enable caching
		$this->output->cache( $this->config->item('cache_day') );

		// Load the views
		$this->load->view('layout/header', $data);
		$this->load->view('compare', $data);
		$this->load->view('layout/footer');
	}

}

/* End of file compare.php */
/* Location: ./application/controllers/compare.php */