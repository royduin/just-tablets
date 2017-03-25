<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		// Load model
		$this->load->model('filter_model');
		
		$best_tablet = $this->db->query("
			SELECT 
				tb_product.product_id,
				tb_product.brand_id,
				tb_product.title,
				tb_product.title_url,
				tb_product.img_mid,
				tb_product.img_high,
				tb_product.descr_short,
				tb_price.price,
				tb_brand.name as brand,
				tb_brand.name_url as brand_url
			FROM
				tb_product
			INNER JOIN
				tb_price
			ON
				tb_price.product_id = tb_product.product_id
			AND
				tb_price.price = (
					SELECT MIN(price)
					FROM tb_price
					WHERE tb_price.product_id = tb_product.product_id
				)
			INNER JOIN
				tb_brand
			ON
				tb_brand.brand_id = tb_product.brand_id
			ORDER BY
				popularity DESC
			LIMIT
				1
		");

		$buyable = $this->db->query("
			SELECT
				COUNT(DISTINCT tb_product.product_id) as total
			FROM
				tb_product
			INNER JOIN
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
		");

		$news = $this->db->query("
			SELECT
				*
			FROM
				tb_news
			ORDER BY
				`date` DESC
			LIMIT
				1
		");

		$review = $this->db->query("
			SELECT
				*
			FROM
				tb_review
			JOIN
				tb_product
			ON
				tb_product.product_id = tb_review.product_id
			INNER JOIN
				tb_price
			ON
				tb_price.product_id = tb_product.product_id
			AND
				tb_price.price = (
					SELECT MIN(price)
					FROM tb_price
					WHERE tb_price.product_id = tb_product.product_id
				)
			ORDER BY
				added DESC
			LIMIT
				1
		");

		// Set data for the view
		$data = array(
			'review'		=> $review->row_array(),
			'total_reviews' => $this->db->count_all('tb_review'),
			'best_tablet' 	=> $best_tablet->row_array(),
			'total'			=> $this->db->count_all('tb_product'),
			'total_buyable'	=> $buyable->row_array()['total'],
			'shops'			=> $this->db->count_all('tb_shop'),
			'prices'		=> $this->db->count_all('tb_price'),
			'brands_count'	=> $this->db->count_all('tb_brand'),
			'news'			=> $news->row_array(),
			'brands'		=> $this->filter_model->get_brands()
		);

		// Enable caching
		$this->output->cache( $this->config->item('cache_cron') );

		// Load the views
		$this->load->view('layout/header');
		$this->load->view('home',$data);
		$this->load->view('layout/footer');
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */