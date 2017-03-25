<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get highest and lowest filter values
	 * @return array           Min and max
	 */
	public function get_filters_minmax()
	{
		$query = $this->db->query("
			SELECT
				feature_id,
				MAX(ABS(tb_spec_value.name)) AS max,
				MIN(ABS(tb_spec_value.name)) AS min
			FROM
				tb_spec_feature
			JOIN 
				tb_spec_fk
			ON 
				feature_id = feature
			JOIN 
				tb_spec_value
			ON 
				value = value_id
			WHERE 
				filter=2
			GROUP BY 
				feature_id
		");

		// Set feature_id as key
		foreach($query->result_array() as $values)
		{
			$result[$values['feature_id']] = ['max' => $values['max'], 'min' => $values['min']];
		}

		return $result;
	}

	/**
	 * Get highest and lowest filter value
	 * @param  int  $feature Feature ID
	 * @return array           Min and max
	 */
	// public function get_filter_minmax($feature)
	// {
	// 	$query = $this->db->query("
	// 		SELECT
	// 			MIN(ABS(name)) as min,
	// 			MAX(ABS(name)) as max
	// 		FROM 
	// 			tb_spec_fk
	// 		LEFT JOIN
	// 			tb_spec_value
	// 		ON
	// 			tb_spec_value.value_id = tb_spec_fk.value
	// 		WHERE
	// 			tb_spec_fk.feature=".$this->db->escape($feature)."
	// 	");
	// 	return $query->row_array();
	// }

	/**
	 * Get all default specification filters
	 * @return array All default specification filters
	 */
	public function get_spec_default_filters()
	{
		$query = $this->db->query("
			SELECT
				feature_id,
				spec_filter_default
			FROM
				tb_spec_feature
			WHERE
				spec_filter_default != 0
		");
		return $query->result_array();
	}

	/**
	 * Get filters
	 * @param  string $hash The hash
	 * @return array       The POST array
	 */
	public function get_filter($hash)
	{
		$query = $this->db->query("SELECT filters FROM tb_filter WHERE filter_mid=".$this->db->escape($hash)."");
		$row = $query->row_array();
		if(isset($row['filters']))
		{
			$input = @unserialize($row['filters']);
			if(!$input){
				$input = [];
			}
			return $input;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Save filters
	 * @param array $input The POST array
	 * @return string      The calculated hash
	 */
	public function set_filter($input)
	{
		$input 	= serialize($input);
		$hash 	= md5($input);
		$this->db->query("INSERT IGNORE INTO tb_filter (
			filter_mid,
			filters
		) VALUES (
			".$this->db->escape($hash).",
			".$this->db->escape($input)."
		)");
		return $hash;
	}

}

/* End of file ajax_model.php */
/* Location: ./application/models/ajax_model.php */