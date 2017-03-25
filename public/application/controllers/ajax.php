<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// if(!$this->input->is_ajax_request()){
		// 	show_404();
		// }
		
		$this->load->model('filter_model');
		$this->load->model('ajax_model');
	}

	public function filter($hash = FALSE,$justhash = FALSE)
	{
		// Load pagination library
		$this->load->library('pagination');

		// Hash given?
		if($hash)
		{
			// Get input by hash
			$input = $this->ajax_model->get_filter($hash);

			// No filters?
			if(!$input)
			{
				// Error!
				echo 'Deze gekozen filter bestaat niet!';
				exit;
			}
		}
		else
		{
			// Set POST as input
			$input 	= $this->input->post();

			// Save filters and get hash
			$hash 	= $this->ajax_model->set_filter( $input );

			// Just hash?
			if($justhash)
			{
				echo $hash;
				exit;
			}
		}

		// For debugging
		log_message('info','------------------------------------------------------');
		log_message('info','Ajax request start');
		log_message('info','Hash: '.$hash);
		log_message('info','Input:');
		log_message('info',print_r($input,TRUE));
		
		// Filter specifications set?
		if(!isset($input['spec1']) OR !isset($input['spec2']) OR !isset($input['spec3']))
		{
			// Set default filter specifications
			foreach($this->ajax_model->get_spec_default_filters() as $default)
			{
				$input['spec'.$default['spec_filter_default']] = $default['feature_id'];
			}

			// Debug
			log_message('info','No filter specifications given, so defaults set (spec1: '.$input['spec1'].', spec2: '.$input['spec2'].', spec3: '.$input['spec3'].')');
		}

		// Split option and checkbox filters
		if($input)
		{
			foreach($input as $key=>$value)
			{
				switch($key[0])
				{
					case 'f':
						$filters[substr($key,1)] = $value;
						break;
					case 'o':
						$ofilters[substr($key,1)] = $value;
						break;
				}
			}
		}

		// Filters present?
		if(isset($filters))
		{
			// Generate SQL WHERE query
			foreach($filters as $feature_id=>$values)
			{
				// Set all values to integers
				$values = array_map('floatval', $values);

				// Set query part
				$sql[] = "(feature=".$this->db->escape($feature_id)." AND value IN(".implode(',',$values)."))";
			}
			$sql = implode(' OR ',$sql);
			log_message('info','Checkbox filters are set');
			log_message('info',$sql);
		}

		// Option filters present?
		if(isset($ofilters))
		{
			// Get the default values
			$defaults = $this->ajax_model->get_filters_minmax();

			// Generate SQL WHERE query
			foreach($ofilters as $feature_id=>$values)
			{
				// Not the default filters values? (highest and lowest selected)
				if($defaults[$feature_id]['min'] != $values[0] OR $defaults[$feature_id]['max'] != $values[1])
				{
					$osql[] = "(feature=".$this->db->escape($feature_id)." AND tb_spec_value.name BETWEEN ".floatval($values[0])." AND ".floatval($values[1]).")";

				// Unset if they are
				} else
				{
					unset($ofilters[$feature_id]);

					// Unset for the input to, so Javascript can define if the filter block has to be opened
					unset($input['o'.$feature_id]);
				}
			}

			// Are there still option filters?
			if(count($ofilters))
			{
				$osql = implode(' OR ',$osql);
				// Debug
				log_message('info','Option filters are set');
				log_message('info',$osql);
			}
			else
			{
				// Unset all option filters
				unset($ofilters);
				unset($osql);
			}
		}

		// Filters present? And so there is a SQL query part?
		if(isset($sql) OR isset($osql))
		{
			// Normal and options filters set
			if(isset($ofilters) AND isset($filters))
			{
				$where = $sql.' OR '.$osql;
				$count = count($filters) + count($ofilters);
			}
			// Just option filters set
			elseif(isset($ofilters))
			{
				$where = $osql;
				$count = count($ofilters);
			}
			// Just normal filters set
			else
			{
				$where = $sql;
				$count = count($filters);
			}

			// The query
			$sql = "
				SELECT 
					product_id 
				FROM 
					tb_spec_fk 
				LEFT JOIN
					tb_spec_value
				ON
					tb_spec_value.value_id = tb_spec_fk.value
				WHERE 
					".$where."
				GROUP BY 
					product_id 
				HAVING 
					count(product_id) = ".$this->db->escape($count);

			// Execute and get product ids
			$query = $this->db->query($sql);
			$products = [];
			foreach($query->result_array() as $product)
			{
				$products[] = $product['product_id'];
			}
			// Debug
			log_message('info','Mached products by filters: '.(isset($products) ? count($products) : 'NONE!'));
		}

		// First part of the final query
		$sql = '
		SELECT DISTINCT SQL_CALC_FOUND_ROWS
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
					CONCAT_WS("",tb_spec_value.name,tb_spec_sign.name)
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
					feature = '.$this->db->escape($input['spec1']).'
					AND product_id = tb_product.product_id
			) as spec_1,
			(
				SELECT
					CONCAT_WS("",tb_spec_value.name,tb_spec_sign.name)
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
					feature = '.$this->db->escape($input['spec2']).'
					AND product_id = tb_product.product_id
			) as spec_2,
			(
				SELECT
					CONCAT_WS("",tb_spec_value.name,tb_spec_sign.name)
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
					feature = '.$this->db->escape($input['spec3']).'
					AND product_id = tb_product.product_id
			) as spec_3
		FROM 
			tb_product ';

		// Buyable checked?
		if(isset($input['buyable'])){
			$sql .= 'INNER JOIN ';
		} else {
			$sql .= 'LEFT JOIN ';
		}
		
		// Proceed with the query
		$sql .= 'tb_price 
		ON 
			tb_price.product_id = tb_product.product_id
		AND
			tb_price.price = 
			(
				SELECT min(price)
				FROM tb_price
				WHERE tb_price.product_id = tb_product.product_id
			)';

		// Filters or brands set?
		if(isset($products) OR isset($input['brands']))
		{
			$sql .= ' WHERE ';
		}

		// Filters set?
		if(isset($products))
		{
			// Products found by filters?
			if(!empty($products))
			{
				// Put found product ids in query
				$sql .= "tb_product.product_id IN (".implode(',',$products).")";

			} else 
			{
				// Stop it because there aren't any products found by filters
				$sql = '';
			}
		}

		// Brand set? And still $sql
		if(isset($input['brands']) && $sql)
		{
			$sql .= ((isset($products) AND !empty($products)) ? 'AND ' : '')."tb_product.brand_id IN (".implode(',',$input['brands']).")";
		}

		
		// Price from/to
		if(!empty($input['price_from']) AND !empty($input['price_to']))
		{
			$sql_price = "tb_price.price BETWEEN ".$this->db->escape((int)($input['price_from'].'00'))." AND ".$this->db->escape((int)($input['price_to'].'00')).PHP_EOL;

		// Only price from
		} elseif(!empty($input['price_from']))
		{
			$sql_price = "tb_price.price >= ".$this->db->escape((int)($input['price_from'].'00')).PHP_EOL;

		// Only price to
		} elseif(!empty($input['price_to']))
		{
			// Check if the price isn't equal to the highest price
			if($input['price_to'] != $this->filter_model->get_highest_price())
			{
				$sql_price = "tb_price.price <= ".$this->db->escape((int)($input['price_to'].'00')).PHP_EOL;
			}
		}

		// Price from/to and still $sql set?
		if(isset($sql_price) AND $sql)
		{
			// Already a where set?
			if(strstr($sql,'WHERE tb_product') == TRUE){
				$sql .= " AND ".$sql_price;
			} else {
				$sql .= " WHERE ".$sql_price;
			}
		}

		// Still everyhing cool?
		if($sql)
		{
			// Ordering the results
			$sql .= ' ORDER BY ';

			// Sorting option selected?
			if(isset($input['sort']))
			{
				// Which one is set?
				switch($input['sort'])
				{
					case 'popularity':
						$sql .= 'tb_product.popularity';
						break;

					case 'price':
						$sql .= 'tb_price.price';
						break;

					case 'spec':
						// Spec can be a string and integer, so apply it two times, first with ABS, second without
						// Referring to my cool blog post: http://royduineveld.nl/blog/mysql/178/tekst-en-nummers-sorteren/
						$sql .= 'ABS(spec_'.(isset($input['sort_spec']) && in_array($input['sort_spec'],array(1,2,3)) ? $input['sort_spec'] : 1).')';
						$sql .= ((isset($input['sort_dir']) AND $input['sort_dir'] == 'asc') ? ' ASC' : ' DESC');
						$sql .= ', spec_'.(isset($input['sort_spec']) && in_array($input['sort_spec'],array(1,2,3)) ? $input['sort_spec'] : 1);
						break;

					default:
						$sql .= 'tb_product.popularity';
				}
			} else {
				$sql .= 'tb_product.popularity';
			}

			// Sorting direction?
			$sql .= ((isset($input['sort_dir']) AND $input['sort_dir'] == 'asc') ? ' ASC' : ' DESC');

			// After the selected order, order by product title
			$sql .= ', tb_product.title ASC';

			// The limit for the pagination
			$sql .= ' LIMIT '.((isset($input['page']) ? calculate_offset($input['page'],array('per_page' => 25)) : 0)).',25';
		}
		
		// Again, still everyhing cool?
		if($sql)
		{
			// Finally! Executed it!
			$query = $this->db->query($sql);
			foreach($query->result_array() as $key=>$product)
			{
				// Re-set some values
				$arr[$key] 					= $product;
				$arr[$key]['price'] 		= price_nice($arr[$key]['price']);
				$arr[$key]['spec_1_title']	= col_spec_value($arr[$key]['spec_1'],TRUE);
				$arr[$key]['spec_1']		= col_spec_value($arr[$key]['spec_1']);
				$arr[$key]['spec_2_title']	= col_spec_value($arr[$key]['spec_2'],TRUE);
				$arr[$key]['spec_2']		= col_spec_value($arr[$key]['spec_2']);
				$arr[$key]['spec_3_title']	= col_spec_value($arr[$key]['spec_3'],TRUE);
				$arr[$key]['spec_3']		= col_spec_value($arr[$key]['spec_3']);
			}
		}

		// Get total amount
		// From my other cool blog post: http://royduineveld.nl/blog/mysql/311/totaal-ophalen-bij-het-gebruik-van-limit/
		$arr['total_amount']	= ($sql ? $this->db->query("SELECT FOUND_ROWS() as amount")->row_array()['amount'] : 0);

		// Count total pages
		$arr['total_pages']		= ( $arr['total_amount'] == 0 ? 0 : ceil($arr['total_amount'] / 25) );

		// Just returning what is given
		$arr['hash'] 			= $hash;
		$arr['input']			= $input;

		// Set current page
		$config['cur_page'] = (isset($input['page']) ? (int)$input['page'] : 1);

		// Enable query strings so normal non-rewriten get's are set
		$config['page_query_string'] 	= TRUE;

		// Set the query string segment (?page=)
		$config['query_string_segment'] = 'page';

		// Set some other pagination stuff
		$config['uri_segment'] 			= 3;
		$config['per_page'] 			= 25;
		$config['base_url'] 			= site_url().'tablets-vergelijken/';
		$config['first_url']			= site_url().'tablets-vergelijken/?page=1';
		$config['total_rows'] 			= $arr['total_amount'];

		// Initialise it and set it
		$this->pagination->initialize($config);
		$arr['pagination']				= $this->pagination->create_links();
		$arr['current_page']			= $config['cur_page'];

		// Yeah, we've made it! The end!
		echo json_encode($arr);

		// Just some last debugging stuff
		log_message('info','Output');
		log_message('info',print_r($arr,TRUE));
	}
}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */