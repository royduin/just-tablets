<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Filter_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get all prices
	 * @return array All the prices
	 */
	public function get_prices()
	{
		$prices = $this->db->query("
			SELECT DISTINCT
				price
			FROM
				tb_product
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
		");
		// From multi to single array
		foreach($prices->result_array() as $key=>$price)
		{
			$pricesn[$key] = $price['price'];
		}
		// Add empty and zero at the beginning of the array
		// array_unshift($pricesn,'',0);
		// Just a zero for now (priceless is equal to 0)
		array_unshift($pricesn,0);
		// Remove pennies :)
		$pricesn = array_map(function($val){
			return ((strlen($val) >= 3) ? substr($val,0,-2) : $val);
		},$pricesn);
		// Strip duplicates and correct keys
		$pricesn = array_values(array_unique($pricesn));
		// Sort prices
		sort($pricesn);
		// Remove last price
		array_pop($pricesn);
		// Add highest price
		$pricesn[] = $this->get_highest_price();
		return $pricesn;
	}

	/**
	 * Get all brands
	 * @return array All the brands
	 */
	public function get_brands()
	{
		$brands 	= $this->db->query("
			SELECT 
				brand_id,
				name,
				name_url,
				(
					SELECT COUNT(product_id)
					FROM tb_product
					WHERE tb_product.brand_id = tb_brand.brand_id
				) AS brand_count
			FROM 
				tb_brand
			ORDER BY
				name ASC
		");
		return $brands->result_array();
	}

	/**
	 * Get all specification filters
	 * @return array All specification filters
	 */
	public function get_spec_filters()
	{
		$filters = $this->db->query("
			SELECT
				feature_id,
				name,
				spec_filter_default
			FROM
				tb_spec_feature
			WHERE
				spec_filter=1
			ORDER BY
				name ASC
		");
		return $filters->result_array();
	}

	/**
	 * Get all filters
	 * @return array All the filters
	 */
	public function get_filters()
	{
		$filters = $this->db->query("
			SELECT
				tb_spec_feature.feature_id,
				tb_spec_feature.name,
				tb_spec_feature.filter,
				`default`
			FROM
				tb_spec_feature
			WHERE
				filter=1 OR filter=2
			ORDER BY
				`default` DESC,
				`order` ASC,
				filter DESC
		");
		$filters = $filters->result_array();
		foreach($filters as $key=>$filter)
		{
			$filters_sub = $this->db->query("
				SELECT DISTINCT
					tb_spec_value.value_id,
					tb_spec_value.name as value_name,
					tb_spec_sign.name as sign_name
				FROM
					tb_spec_fk
				INNER JOIN
					tb_spec_value
				ON
					tb_spec_value.value_id = tb_spec_fk.value
				LEFT JOIN
					tb_spec_sign
				ON
					tb_spec_sign.sign_id = tb_spec_fk.sign
				WHERE
					feature=".$this->db->escape($filter['feature_id'])."
				ORDER BY
					ABS(tb_spec_value.name),tb_spec_value.name ASC
			");
			$filters[$key]['values'] = $filters_sub->result_array();
		}
		return $filters;
	}

	/**
	 * Get highest price
	 * @return int Higest price
	 */
	public function get_highest_price()
	{
		$prices = $this->db->query("SELECT price FROM tb_price ORDER BY price DESC LIMIT 1");
		return substr($prices->row_array()['price'],0,-2) + 1;
	}

}

/* End of file filter_model.php */
/* Location: ./application/models/filter_model.php */