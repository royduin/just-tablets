<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generates a product description
 * @param  array $data Product array (product,prices and specs)
 * @return string       The generated description
 */
function generate_description($data)
{
	// Generate description
	$descr = 'Een ';

	// Price available?
	if(!empty($data['prices']))
	{
		// Get prices only
		foreach($data['prices'] as $price){
			$prices[] = $price['price'];
		}

		// Sort prices
		sort($prices);

		// Cheap one?
		if($prices[0] < 20000){
			$descr .= 'goedkope';
		} else {
			$descr .= 'snelle';
		}
	} else {
		// Older then 6 months?
		if( strtotime($data['release']) < strtotime('-6 months') ){
			$descr .= 'wat oudere';
		} else {
			$descr .= 'nog te verschijnen';
		}
	}

	$descr .= ' tablet van '.$data['name'];

	// Family name available?
	if($data['family_name']){
		$descr .= ' uit de '.$data['family_name'].' serie';
	}

	$descr .= ' met een ';

	// Add specifications
	foreach($data['specs'] as $spec){
		if(in_array('10',$spec)){
			$descr .= strtolower($spec['name']).' van '.$spec['value'];
		}
		if(in_array('6',$spec)){
			$descr .= ', een '.strtolower($spec['name']).' van '.$spec['value'];
		}
		if(in_array('34',$spec)){
			$descr .= ', als '.strtolower($spec['name']).' '.$spec['value'];
		}
		if(in_array('33',$spec)){
			$descr .= ' en in de '.strtolower($spec['name']).' '.strtolower($spec['value']).'.';
		}
	}
	return $descr;
}


/**
 * Download image
 * @param  string $id   Image ID
 * @param  string $file Image URL
 * @param  string $name Image name
 * @param  string $type Image type
 * @return string       New image URL
 */
function download_image($id,$file,$name,$type)
{
	if($file)
	{
		// $file_name = 'img/tablet/'.$id.'-'.$name.'-'.$type.'.'.pathinfo($file, PATHINFO_EXTENSION);
		$file_name = 'img/tablet/'.$id.'-'.$type.'.'.pathinfo($file, PATHINFO_EXTENSION);
		if( ! file_exists($file_name) )
		{
			@file_put_contents($file_name, file_get_contents($file));
		}
		return site_url().$file_name;
	}
	return '';
}


/**
 * Create pretty spec values for spec columns
 * @param  string  $value Input
 * @param  boolean $title Title?
 * @return string         Output
 */
function col_spec_value($value,$title = FALSE)
{
	if(!$value AND !$title){
		$value = '-';
	}

	if(!$title){
		$value = str_replace(',',',<br />',$value);
	}

	if(!$value AND $title){
		$value = 'Specificatie is onbekend';
		$value = html_escape($value);
	}

	return $value;
}


/**
 * Create pretty spec values
 * @param  string $value Input
 * @return string        Output
 */
function value($value,$commas = FALSE)
{
	if($value == 'Ja' OR $value == 'Y'){
		$value = '<i class="icon-ok"></i>';
	}

	if($value == 'Nee' OR $value == 'N'){
		$value = '<i class="icon-remove"></i>';
	}

	if($value == 'O'){
		$value = 'Optioneel';
	}

	if(!$commas){
		if(strstr($value,',')){
			$value = '<ul><li>'.str_replace(',','</li><li>',$value).'</li></ul>';
		}
	}

	return $value;
}

/**
 * Add comma to prices
 * @param  string $price Input
 * @return string        Output
 */
function price_nice($price)
{
	if(!$price){
		$price = '-';
	} else {
		$price_voor = substr($price, 0, -2);
		if(!$price_voor){
			$price_voor = 0;
		}
		$price_achter = substr($price, -2);
		$price = $price_voor.','.$price_achter;
	}
	return $price;	
}


/**
 * Set pagination info (title, description, canocial, next and prev)
 * @param  string $url         uri segment
 * @param  array $config      pagination array
 * @param  string $title       Page title
 * @param  string $description Page description
 * @param  string $items       Description of items on the page
 * @return array              Array to send to the view
 */
function pagination_info($url,$config,$title,$description,$items)
{
	$last_page = ceil($config['total_rows'] / $config['per_page']);

	//First page?
	if(!$url OR $url == 1)
	{
		$data['page_title'] 		= $title;
		$data['page_description']	= $description;
		$data['page_canonical'] 	= str_replace('pagina','',$config['base_url']);
		$data['page_next']			= $config['base_url'].'/2/';
	}
	else
	{
		$data['page_title'] 		= $title.' (overzicht pagina '.$url.'/'.$last_page.')';
		$data['page_description']	= $description.' Pagina '.$url.' van de '.$last_page.' met '.$items;
		$data['page_canonical'] 	= current_url().'/';

		//Second page?
		if(($url - 1) == 1){
			$data['page_prev'] 		= str_replace('pagina','',$config['base_url']);
		} else {
			$data['page_prev'] 		= $config['base_url'].'/'.($url - 1).'/';
		}

		//Not the last page?
		if( $last_page != $url ){
			$data['page_next']		= $config['base_url'].'/'.($url + 1).'/';
		}
	}
	return $data;
}

/**
 * Calculates the offset for the select query
 * @param  string $url    Url segment with current page
 * @param  array $config Pagination array
 * @return int         The offset
 */
function calculate_offset($url,$config)
{
	$offset = (int)($url ? ($url - 1) : 0) * $config['per_page'];
	if($offset == '-25'){
		$offset = 0;
	}
	return $offset;
}