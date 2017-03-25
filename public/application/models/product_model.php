<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get product reviews
	 * @param  int $id Product ID
	 * @return array     Product reviews
	 */
	public function get_product_reviews($id)
	{
		$reviews = $this->db->query("
			SELECT
				*
			FROM
				tb_review
			WHERE
				product_id = ".$this->db->escape($id)."
			ORDER BY
				updated DESC
		");
		return $reviews->result_array();
	}

	/**
	 * Get product prices
	 * @param  int $id Product ID
	 * @return array     Product prices
	 */
	public function get_product_prices($id)
	{
		$prices = $this->db->query("
			SELECT
				tb_price.*,
				tb_shop.*
			FROM
				tb_price
			INNER JOIN
				tb_shop
			ON
				tb_shop.shop_id = tb_price.shop_id
			WHERE
				product_id = ".$this->db->escape($id)."
		");
		return $prices->result_array();
	}

	/**
	 * Get product specifications
	 * @param  int $id Product ID
	 * @return array     Product specifications
	 */
	public function get_product_specs($id)
	{
		$specs = $this->db->query("
			SELECT
				tb_spec_feature.feature_id,
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
				tb_spec_fk.product_id = ".$this->db->escape($id)."
			ORDER BY tb_spec_group.name,tb_spec_feature.name ASC
		");
		return $specs->result_array();
	}

	/**
	 * Get product features
	 * @param  int $id Product ID
	 * @return array     Product features
	 */
	public function get_product_features($id)
	{
		$features = $this->db->query("
			SELECT 
				tb_featurelogo.image,
				tb_featurelogo.descr
			FROM
				tb_featurelogo_fk
			INNER JOIN
				tb_featurelogo
			ON
				tb_featurelogo.featurelogo_id = tb_featurelogo_fk.featurelogo_id
			WHERE
				product_id=".$this->db->escape($id)."
		");
		return $features->result_array();
	}

	/**
	 * Get product by Icecat ID
	 * @param  int $iid Product ID
	 * @return array      Product information
	 */
	public function get_product_icecat($iid)
	{
		$query = $this->db->query("SELECT product_id,title_url FROM tb_product WHERE product_iid=".$this->db->escape($iid)."");
		return $query->row_array();
	}

	/**
	 * Get product
	 * @param  int $id Product ID
	 * @return array     Product information
	 */
	public function get_product($id)
	{
		// 1024 is to low for group_concat for product images
		$this->db->query("SET SESSION group_concat_max_len = 2048");
		
		$product = $this->db->query("
			SELECT 
				tb_product.*,
				tb_brand.*,
				tb_family.name as family_name,
				(
					SELECT GROUP_CONCAT(ean)
					FROM tb_ean
					WHERE tb_ean.product_id=tb_product.product_id
				) AS ean,
				(
					SELECT GROUP_CONCAT(image_thumb)
					FROM tb_image
					WHERE tb_image.product_id=tb_product.product_id
				) AS images_thumb,
				(
					SELECT GROUP_CONCAT(image_high)
					FROM tb_image
					WHERE tb_image.product_id=tb_product.product_id
				) AS images_high
			FROM 
				tb_product
			INNER JOIN
				tb_brand
			ON 
				tb_product.brand_id = tb_brand.brand_id
			LEFT JOIN
				tb_family
			ON 
				tb_product.family_id = tb_family.family_id
			WHERE 
				tb_product.product_id=".$this->db->escape($id)."
		");
		return $product->row_array();
	}

	/**
	 * Get products
	 * @param  int $offset Offset for pagination
	 * @param  int $limit  Limit for pagination
	 * @return array         All products
	 */
	// TODO: Apply default feature ID's dynamically
	public function get_products($offset,$limit,$order = 'popularity DESC, title ASC',$price = 'INNER')
	{
		$products 	= $this->db->query("
			SELECT DISTINCT
				tb_product.product_id,
				tb_product.img_thumb,
				tb_product.img_low,
				tb_product.title,
				tb_product.title_url,
				tb_product.descr_short,
				round(tb_product.popularity * 100 / (
					SELECT popularity
					FROM tb_product
					ORDER BY popularity DESC
					LIMIT 1
				)) as popularity,
				tb_price.price,
				(
					SELECT
						CONCAT_WS('',tb_spec_value.name,tb_spec_sign.name)
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
						feature = 10
						AND product_id = tb_product.product_id
				) as spec_1,
				(
					SELECT
						CONCAT_WS('',tb_spec_value.name,tb_spec_sign.name)
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
						feature = 160054
						AND product_id = tb_product.product_id
				) as spec_2,
				(
					SELECT
						CONCAT_WS('',tb_spec_value.name,tb_spec_sign.name)
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
						feature = 33
						AND product_id = tb_product.product_id
				) as spec_3
			FROM
				tb_product
			".$price." JOIN
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
			ORDER BY 
				".$order."
			LIMIT 
				".$this->db->escape( $offset ).",
				".$this->db->escape( $limit )."
		");
		return $products->result_array();
	}

	/**
	 * Get count of total products
	 * @return int Amount of all products
	 */
	public function count_total_products()
	{
		$total_products	= $this->db->query("
			SELECT 
				count(DISTINCT tb_product.product_id) as aantal
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
		return $total_products->row_array()['aantal'];
	}

}

/* End of file product_model.php */
/* Location: ./application/models/product_model.php */