<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if($this->input->is_cli_request() OR in_array($this->input->ip_address(),$this->config->item('admins'))){
			
		} else {
			show_404();
		}

		// Load product model
		$this->load->model('product_model');
	}

	public function index()
	{
		echo '<h1>Just Tablets updates</h1>';
		echo '<ul>
			<li><a href="'.site_url().'cron/sitemap">Sitemap</a></li>
			<li><a href="'.site_url().'cron/products">Products</a></li>
			<li><a href="'.site_url().'cron/shops">Shops</a></li>
			<li><a href="'.site_url().'cron/news">News</a></li>
			<li><a href="'.site_url().'cron/statistics">Statistics</a></li>
			<li><a href="'.site_url().'cron/reviews">Reviews</a></li>
			<li><a href="'.site_url().'cron/feeds">Feeds</a></li>
			<li><a href="'.site_url().'cron/reindex">Reindex (for search)</a></li>
		</ul>';
	}

	public function reindex()
	{
		// Truncate records
		$this->db->query('TRUNCATE TABLE tb_product_search');

		// Insert everything present in tb_product
		$this->db->query('INSERT INTO tb_product_search SELECT * FROM tb_product');
	}

	public function feeds()
	{
		// Set current directory
		// $_SERVER doesn't work when running trough CLI
		// So for CLI requests use dirname
		$current_directory = ( isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : dirname(dirname(__DIR__)) );
		// $current_directory = '/home/royduin/domains/justtablets.nl/public_html';

		// News feed
		
			// Load news model
			$this->load->model('news_model');

			// Start feed
			$feed =("<rss version=\"2.0\">\n");
			$feed .=("<channel>\n"); 
			$feed .=("<title>Just Tablets - Nieuws</title>\n"); 
			$feed .=("<link>http://justtablets.nl</link>\n"); 
			$feed .=("<description>Wij draaien de tablets om! Bekijk en vergelijk eenvoudig alle tablets! Geen idee welke tablet bij u past? Doorloop onze eenvoudige tablet keuze wizard!</description>\n"); 
			$feed .=("<language>nl-NL</language>\n");

			// Get and loop trough news items
			foreach( $this->news_model->get_items(0,15) as $item )
			{
				$feed .=("<item>\n"); 
					$feed .=("\t<title>".$item['title']."</title>\n"); 
					$feed .=("\t<link>".site_url()."tablet-nieuws/".$item['news_id']."/".$item['title_url']."/</link>\n"); 
					$feed .=("\t<description><![CDATA[".strip_tags($item['description'])."]]></description>\n"); 
				$feed .=("</item>\n"); 
			}

			// Close feed
			$feed .=("</channel>\n"); 
			$feed .=("</rss>");

			// Save feed
			file_put_contents($current_directory.'/nieuws.rss', $feed);

		// Tablet feed
			
			// Start feed
			$feed =("<rss version=\"2.0\">\n");
			$feed .=("<channel>\n"); 
			$feed .=("<title>Just Tablets - Tablets</title>\n"); 
			$feed .=("<link>http://justtablets.nl</link>\n"); 
			$feed .=("<description>Wij draaien de tablets om! Bekijk en vergelijk eenvoudig alle tablets! Geen idee welke tablet bij u past? Doorloop onze eenvoudige tablet keuze wizard!</description>\n"); 
			$feed .=("<language>nl-NL</language>\n");

			// Get and loop trough tablets
			foreach( $this->product_model->get_products(0,15,'product_id DESC','LEFT') as $item )
			{
				$feed .=("<item>\n"); 
					$feed .=("\t<title>".$item['title']."</title>\n"); 
					$feed .=("\t<link>".site_url()."tablet/".$item['product_id']."/".$item['title_url']."/</link>\n"); 
					$feed .=("\t<description>Vanaf heden is ook de ".$item['title']." te vergelijken bij Just Tablets!</description>\n"); 
				$feed .=("</item>\n"); 
			}

			// Close feed
			$feed .=("</channel>\n"); 
			$feed .=("</rss>");

			// Save feed
			file_put_contents($current_directory.'/tablets.rss', $feed);
	}

	public function statistics()
	{
		// Load GAPI library
		$this->load->library('gapi',array('email' => $this->config->item('gapi_username'),'password' => $this->config->item('gapi_password')));

		// Get Analytics data
		$this->gapi->requestReportData(
			$this->config->item('gapi_report'), // Raport ID
			array('pagePath'), // Dimension
			array('uniquePageviews'), // Metrics
			array('-uniquePageviews'), // Sorting
			'pagePath =@ /tablet/', // Filtering
			date('Y-m-d',strtotime('-1 month')), // Start date
			date('Y-m-d'), // End date
			1, // Start index
			1000 // Max results
		);

		// Loop trough the returned data
		foreach($this->gapi->getResults() as $result)
		{
			// Concert object to array
			$result = (array)$result;

			// Loop again
			foreach($result as $key=>$value)
			{
				// Is it set?
				if(isset($value['uniquePageviews']))
				{
					// Set views
					$views = $value['uniquePageviews'];
				}
				// Is it set?
				elseif(isset($value['pagePath']))
				{
					// Set product id
					$product = $value['pagePath'];
					// Strip first and last slash
					$product = trim($product,'/');
					// Split into an array by slashes
					$product = explode('/',$product);
					// Second array item set?
					if(isset($product[2]))
					{
						// Set product id
						$product = $product[1];
					} else {
						$product = FALSE;
					}
				}
			}
			
			// Product id set?
			if($product)
			{
				// Insert into new array
				$products[$product] = $views;
			}

		}
		
		// Loop trough new array
		if(isset($products))
		{
			foreach($products as $product_id=>$popularity)
			{
				// Update popularity in tb_product table
				$this->db->query("
					UPDATE
						tb_product
					SET
						popularity = ".$this->db->escape($popularity)."
					WHERE
						product_id = ".$this->db->escape($product_id)."
				");
			}
		}

		// Yeah!
		echo 'Done!';
	}

	public function news()
	{
		// The feeds to check
		$feeds = array(
			'Hardware.info' 	=> 'http://nl.hardware.info/updates/all.rss',
			'Tweakers' 			=> 'http://feeds.feedburner.com/tweakers/nieuws',
			'PCM' 				=> 'http://feeds.feedburner.com/pcmweb_nieuws',
			'Techzine' 			=> 'http://feeds.techzine.nl/techzine/mixed',
			'Windowsinfo' 		=> 'http://www.windowsinfo.nl/updates/all.rss',
			'Tablet.nl'			=> 'http://www.tablet.nl/feed/'
		);

		// Loop trough feeds
		foreach($feeds as $supplier=>$feed)
		{
			// Get feed
			$xml = file_get_contents($feed);

			// Valid XML?
			if(@simplexml_load_string($xml))
			{
				// Initialize feed
				$x = new SimpleXmlElement($xml);

				// Loop trough items
				foreach($x->channel->item as $item)
				{
					// Tablet in title?
					if(stristr($item->title,'tablet') !== FALSE OR $supplier == 'Tablet.nl')
					{
						// Insert!
						$this->db->query("
							INSERT IGNORE INTO tb_news 
							(
								title,
								title_url,
								link,
								description,
								image,
								date,
								supplier
							) VALUES (
								".$this->db->escape((string)$item->title).",
								".$this->db->escape((string)url_title($item->title,'-',TRUE)).",
								".$this->db->escape((string)trim($item->link)).",
								".$this->db->escape(strip_tags((string)$item->description)).",
								".$this->db->escape((string)($item->enclosure ? $item->enclosure->attributes() : '') ).",
								".$this->db->escape((string)date('Y-m-d H:i:s', strtotime($item->pubDate)) ).",
								".$this->db->escape((string)$supplier)."
							)
						");
					}
				}
			}
		}

		// Call the feed generator
		$this->feeds();

		// Reindex
		$this->reindex();
	}

	public function shops($id = FALSE,$troubleshooting=FALSE)
	{
		if(!$id)
		{
			$query = $this->db->query("SELECT * FROM tb_shop");
		}
		else
		{
			$query = $this->db->query("SELECT * FROM tb_shop WHERE shop_id=".$this->db->escape($id)."");
		}
		foreach($query->result_array() as $shop){
			$this->handle_shop($shop,$troubleshooting);
		}
	}

	public function handle_shop($shop,$troubleshooting=FALSE)
	{
		echo '<h1>Import '.$shop['name'].'</h1><hr>';

		//Remove old
		$this->db->query("DELETE FROM tb_price WHERE shop_id=".$this->db->escape($shop['shop_id'])."");

		//Get all SKU's in a array
		$sku = $this->db->query('SELECT product_id,sku FROM tb_product');
		$list_sku = array();
		foreach($sku->result_array() as $product)
		{
			$list_sku[$product['product_id']] = $product['sku'];
		}

		//Get all EAN's in a array
		$ean = $this->db->query('SELECT product_id,ean FROM tb_ean');
		$list_ean = array();
		foreach($ean->result_array() as $product)
		{
			$list_ean[$product['ean']] = $product['product_id'];
		}

		//Detect type
		if(stristr($shop['feed'],'csv') !== FALSE)
		{
			echo 'CSV detected<br />';

			$feed 	= file(str_replace(' ','%20',$shop['feed']));
			$total 	= 0;
			$prices = 0;
			foreach($feed as $line=>$product)
			{
				$total++;
				$item = explode($shop['feed_delimiter'], $product);
				$item = array_map(function($val){ return trim(trim($val),'"'); },$item);

				if($troubleshooting AND $line == 0)
				{
					echo '<hr /><h2>Troubleshooting:</h2><hr />';
					echo '<strong>SKU:</strong> '.(isset($item[$shop['feed_sku']]) ? $item[$shop['feed_sku']] : '-').'<br />';
					echo '<strong>EAN:</strong> '.(isset($item[$shop['feed_ean']]) ? $item[$shop['feed_ean']] : '-').'<br />';
					echo '<strong>Price:</strong> '.$item[$shop['feed_price']].'<br />';
					echo '<strong>URL:</strong> '.$item[$shop['feed_url']].'<br />';
					echo '<strong>Stock store:</strong> '.(isset($item[$shop['feed_stock_store']]) ? $item[$shop['feed_stock_store']] : '-').'<br />';
					echo '<strong>Stock supplier:</strong> '.(isset($item[$shop['feed_stock_supplier']]) ? $item[$shop['feed_stock_supplier']] : '-').'<br />';
					echo '<strong>Stock text:</strong> '.(isset($item[$shop['feed_stock_text']]) ? $item[$shop['feed_stock_text']] : '-').'<br />';
					echo '<strong>Shipping:</strong> '.(isset($item[$shop['feed_shipping']]) ? $item[$shop['feed_shipping']] : '-').'<br />';
					echo '<hr />';
				}

				if($line > 0)
				{
					$product_id = '';
					if(in_array( (isset($item[$shop['feed_sku']]) ? $item[$shop['feed_sku']] : '!!!') ,$list_sku))
					{
						$product_id = array_keys($list_sku,$item[$shop['feed_sku']])[0];

						if($troubleshooting){
							echo 'SKU: '.$item[$shop['feed_sku']].' -> '.$product_id.'<br />';							
						}

					} elseif(array_key_exists( (isset($item[$shop['feed_ean']]) ? $item[$shop['feed_ean']] : '!!!') ,$list_ean))
					{
						$product_id = $list_ean[$item[$shop['feed_ean']]];

						if($troubleshooting){
							echo 'EAN: '.$item[$shop['feed_ean']].' -> '.$product_id.'<br />';							
						}

					} elseif(array_key_exists( (isset($item[$shop['feed_ean']]) ? '0'.$item[$shop['feed_ean']] : '!!!') ,$list_ean))
					{
						$product_id = $list_ean['0'.$item[$shop['feed_ean']]];

						if($troubleshooting){
							echo 'EAN: 0'.$item[$shop['feed_ean']].' -> '.$product_id.'<br />';
						}

					}

					// Detect new products
					//////////////////////
					// if(!$product_id AND $item[2] == 'Tablet')
					// {
					// 	echo 'NEW: (EAN: '.(isset($item[$shop['feed_ean']]) ? $item[$shop['feed_ean']] : '-').') - (SKU: '.(isset($item[$shop['feed_sku']]) ? $item[$shop['feed_sku']] : '-').')<br />';
					// }

					if($product_id)
					{
						$price = preg_split('/[.,]/',$item[$shop['feed_price']]);
						if(!isset($price[1]) OR !strlen($price[1])){
							$price = $item[$shop['feed_price']].'.00';
						} elseif(strlen($price[1]) == 1){
							$price = $item[$shop['feed_price']].'0';
						} else {
							$price = $item[$shop['feed_price']];
						}

						$price = str_replace(array(',','.'),'',$price);
						if($shop['feed_shipping'] != '-1'){
							$shipping = str_replace(array(',','.'),'',$item[$shop['feed_shipping']]);
						} else {
							$shipping = '';
						}

						//For Centralpoint
						if($shop['shop_id'] == 7)
						{
							if($item[$shop['feed_stock_text']] == 'Op voorraad'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For iCentre
						} elseif($shop['shop_id'] == 9)
						{
							if($item[$shop['feed_stock_store']] == 1){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For Redcoon
						} elseif($shop['shop_id'] == 10)
						{
							if($item[$shop['feed_stock_text']] == 'direct leverbaar'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For Pixmania
						} elseif($shop['shop_id'] == 11)
						{
							if($item[$shop['feed_stock_text']] == 'op voorraad'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For Yourtablet
						} elseif($shop['shop_id'] == 12)
						{
							if($item[$shop['feed_stock_text']] == 'Op werkdagen voor 21:00 besteld, morgen in huis'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For itsOnline
						} elseif($shop['shop_id'] == 14)
						{
							if($item[$shop['feed_stock_text']] == 'Op werkdagen voor 14:00 uur besteld, morgen in huis'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For Laptop Online
						} elseif($shop['shop_id'] == 15)
						{
							if($item[$shop['feed_stock_store']] == 'true'){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For MailaMac
						} elseif($shop['shop_id'] == 16)
						{
							if($item[$shop['feed_stock_store']] == 1){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//For Medionshop
						} elseif($shop['shop_id'] == 17)
						{
							if($item[$shop['feed_stock_store']] == 5){
								$stock_store = 1;
							} else {
								$stock_store = 0;
							}

						//All others
						} else {
							$stock_store = ($shop['feed_stock_store'] != '-1' ? $item[$shop['feed_stock_store']] : '1');
						}

						

						$sql = "
						INSERT INTO tb_price (
							product_id,
							shop_id,
							price,
							url,
							stock_store,
							stock_supplier,
							stock_text,
							shipping
						) VALUES (
							".$this->db->escape($product_id).",
							".$this->db->escape($shop['shop_id']).",
							".$this->db->escape($price).",
							".$this->db->escape($item[$shop['feed_url']]).",
							".$this->db->escape( $stock_store ).",
							".$this->db->escape( ($shop['feed_stock_supplier'] != '-1' ? $item[$shop['feed_stock_supplier']] : '') ).",
							".$this->db->escape( ($shop['feed_stock_text'] != '-1' ? $item[$shop['feed_stock_text']] : '') ).",
							".$this->db->escape($shipping)."
						)";
						$this->db->query($sql);

						$prices++;
					}
				}
			}
			echo '<hr>';
			echo 'Total products: '.($total - 1).'<br />';
			echo 'Total prices: '.$prices.'<br />';

		} elseif(stristr($feed,'xml') !== FALSE)
		{
			echo 'XML detected<br />';
			// TODO: Add support for XML files
			
		} else
		{
			echo 'Unknown format!';
		}
	}


	public function sitemap()
	{
		define('PHP_TAB', "\t");

		// Genarate
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

		// Pages
		$xml .= '<url>'.PHP_EOL;
			$xml .= PHP_TAB.'<loc>'.site_url().'</loc>'.PHP_EOL;
			$xml .= PHP_TAB.'<priority>1.0</priority>'.PHP_EOL;
			$xml .= PHP_TAB.'<changefreq>daily</changefreq>'.PHP_EOL;
		$xml .= '</url>'.PHP_EOL;
		$xml .= '<url>'.PHP_EOL;
			$xml .= PHP_TAB.'<loc>'.site_url().'tablets-vergelijken/</loc>'.PHP_EOL;
			$xml .= PHP_TAB.'<priority>0.9</priority>'.PHP_EOL;
			$xml .= PHP_TAB.'<changefreq>daily</changefreq>'.PHP_EOL;
		$xml .= '</url>'.PHP_EOL;

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

		for($i = 2; $i <= (ceil($total_products->row_array()['aantal'] / 25)); $i++){
		$xml .= '<url>'.PHP_EOL;
			$xml .= PHP_TAB.'<loc>'.site_url().'tablets-vergelijken/pagina/'.$i.'/</loc>'.PHP_EOL;
			$xml .= PHP_TAB.'<priority>0.9</priority>'.PHP_EOL;
			$xml .= PHP_TAB.'<changefreq>daily</changefreq>'.PHP_EOL;
		$xml .= '</url>'.PHP_EOL;
		}

		$xml .= '<url>'.PHP_EOL;
			$xml .= PHP_TAB.'<loc>'.site_url().'tablet-kiezen/</loc>'.PHP_EOL;
			$xml .= PHP_TAB.'<priority>0.9</priority>'.PHP_EOL;
			$xml .= PHP_TAB.'<changefreq>weekly</changefreq>'.PHP_EOL;
		$xml .= '</url>'.PHP_EOL;
		$xml .= '<url>'.PHP_EOL;
			$xml .= PHP_TAB.'<loc>'.site_url().'tablet-nieuws/</loc>'.PHP_EOL;
			$xml .= PHP_TAB.'<priority>0.9</priority>'.PHP_EOL;
			$xml .= PHP_TAB.'<changefreq>daily</changefreq>'.PHP_EOL;
		$xml .= '</url>'.PHP_EOL;

		// Products
		$products = $this->db->query('SELECT product_id,title_url FROM tb_product');
		foreach($products->result_array() as $product){
			$xml .= '<url>'.PHP_EOL;
				$xml .= PHP_TAB.'<loc>'.site_url().'tablet/'.$product['product_id'].'/'.$product['title_url'].'/</loc>'.PHP_EOL;
				$xml .= PHP_TAB.'<priority>0.8</priority>'.PHP_EOL;
				$xml .= PHP_TAB.'<changefreq>hourly</changefreq>'.PHP_EOL;
			$xml .= '</url>'.PHP_EOL;
		}

		// Brands
		$brands = $this->db->query('SELECT brand_id,name_url FROM tb_brand');
		foreach($brands->result_array() as $brand){
			$xml .= '<url>'.PHP_EOL;
				$xml .= PHP_TAB.'<loc>'.site_url().$brand['name_url'].'-tablets/</loc>'.PHP_EOL;
				$xml .= PHP_TAB.'<priority>0.7</priority>'.PHP_EOL;
				$xml .= PHP_TAB.'<changefreq>weekly</changefreq>'.PHP_EOL;
			$xml .= '</url>'.PHP_EOL;
		}

		// News
		$news = $this->db->query('SELECT news_id,title_url FROM tb_news');
		foreach($news->result_array() as $item){
			$xml .= '<url>'.PHP_EOL;
				$xml .= PHP_TAB.'<loc>'.site_url().'tablet-nieuws/'.$item['news_id'].'/'.$item['title_url'].'/</loc>'.PHP_EOL;
				$xml .= PHP_TAB.'<priority>0.6</priority>'.PHP_EOL;
				$xml .= PHP_TAB.'<changefreq>never</changefreq>'.PHP_EOL;
			$xml .= '</url>'.PHP_EOL;
		}

		// End
		$xml .= '</urlset>'.PHP_EOL;

		// Save!
		file_put_contents('sitemap.xml',$xml);
		echo $xml;
	}

	public function search_new()
	{


		exit;

		$file = 'http://xml.ds1.nl/update/?wi=180977&xid=1443&si=1794&f=internal_id;brand;ean_code&type=csv&encoding=UTF-8&general=false&nospecialchars=true';

		$total = 0;
		$total_sku_url = 0;
		$total_ean_url = 0;
		$icecat_ids = [];

		foreach(file($file) as $line=>$product)
		{
			if($line > 0)
			{
				$total++;
				$item = explode(';', $product);
				$item = array_map(function($val){ return trim(trim($val),'"'); },$item);

				if($item[0] AND $item[1]){
					$sku_url = 'http://'.config_item('icecat_username').':'.config_item('icecat_password').'@data.Icecat.biz/xml_s3/xml_server3.cgi?prod_id='.$item[0].';vendor='.$item[1].';lang='.config_item('icecat_language').';output=productxml';
					$total_sku_url++;
				}

				if($item[2]){
					$ean_url = 'http://'.config_item('icecat_username').':'.config_item('icecat_password').'@data.Icecat.biz/xml_s3/xml_server3.cgi?ean_upc='.$item[2].';lang='.config_item('icecat_language').';output=productxml';
					$total_ean_url++;
				}

				$xml = @file_get_contents($sku_url);
				if(strlen($xml) < 1000){
					$xml = @file_get_contents($ean_url);
					if(strlen($xml) < 1000){
						continue;
					}
				}

				$xml 				= new SimpleXMLElement($xml);
				$product 			= $xml->xpath("/ICECAT-interface/Product");
				$product_attr 		= $product[0]->attributes();

				$cat 				= $xml->xpath("/ICECAT-interface/Product/Category/Name");
				$cat_attr			= $cat[0]->attributes();

				if($cat_attr['Value'] == 'tablets'){
					array_push($icecat_ids,(int)$product_attr['ID']);
				}
			}
		}

		echo 'Totaal: '.$total.'<br /><hr />';
		echo 'SKU: '.$total_sku_url.'<br />';
		echo 'EAN: '.$total_ean_url.'<br />';
		echo 'Total SKU/EAN: '.($total_sku_url + $total_ean_url).'<br /><hr />';

		echo '<pre>'.print_r($icecat_ids,TRUE).'</pre><hr>';
		echo implode(',',$icecat_ids);
		
	}

	public function reviews()
	{
		// Get all product id's
		$query = $this->db->query("SELECT product_iid FROM tb_product");
		foreach($query->result_array() as $id){
			$ids[] = $id['product_iid'];
		}

		// Loop trough and update each
		foreach($ids as $id){
			$this->handle_review($id);
		}
	}

	public function handle_review($product_iid)
	{
		// Get and load the xml
		$xml = 'http://'.config_item('icecat_username').':'.config_item('icecat_password').'@data.icecat.biz/reviews.cgi?product_id='.$product_iid.';lang='.config_item('icecat_language');
		$xml = @file_get_contents($xml);

		if($xml)
		{
			// Get product ID
			$product 	= $this->db->query("SELECT product_id FROM tb_product WHERE product_iid=".$this->db->escape($product_iid)." LIMIT 1");
			$product_id = $product->row_array()['product_id'];

			// Extract XML
			$xml 				= new SimpleXMLElement($xml);

			// Remove all reviews
			$this->db->query("DELETE FROM tb_review WHERE product_id=".$this->db->escape($product_iid)."");

			// Loop trough reviews
			foreach($xml->xpath("/ICECAT-interface/Reviews/Product/Review") as $review)
			{
				$review_attr = $review->attributes();

				if($review_attr['LangID'] == 2)
				{
					// Insert review into the database
					$this->db->query("
						INSERT IGNORE INTO tb_review (
							review_id,
							product_id,
							lang_id,
							supplier,
							score,
							url,
							logo,
							added,
							updated,
							award_name,
							award_pic_high,
							award_pic_low,
							value,
							value_good,
							value_bad,
							value_bottom
						) VALUES(
							".$this->db->escape( (int)$review_attr['ID'] ).",
							".$this->db->escape( (int)$product_id ).",
							".$this->db->escape( (int)$review_attr['LangID'] ).",
							".$this->db->escape( (string)$review_attr['Code'] ).",
							".$this->db->escape( (int)$review_attr['Score'] ).",
							".$this->db->escape( (string)$review_attr['URL'] ).",
							".$this->db->escape( (string)$review_attr['LogoPic'] ).",
							".$this->db->escape( (string)$review_attr['DateAdded'] ).",
							".$this->db->escape( (string)$review_attr['Updated'] ).",
							".$this->db->escape( (string)$review_attr['AwardName'] ).",
							".$this->db->escape( (string)$review_attr['AwardHighPic'] ).",
							".$this->db->escape( (string)$review_attr['AwardLowPic'] ).",
							".$this->db->escape( (string)$review->Value ).",
							".$this->db->escape( (string)$review->ValueGood ).",
							".$this->db->escape( (string)$review->ValueBad ).",
							".$this->db->escape( (string)$review->BottomLine )."
						)
					");
				}
				
			}
			
		}
	}

	public function products()
	{
		// Get all product id's
		$query = $this->db->query("SELECT product_iid FROM tb_product");
		foreach($query->result_array() as $id){
			$ids[] = $id['product_iid'];
		}

		// Loop trough and update each
		foreach($ids as $id){
			$this->handle_product($id);
		}

		// Call the feed generator
		$this->feeds();

		// Reindex
		$this->reindex();

	}

	public function handle_product($id)
	{
		// Get and load the xml
		$xml 				= 'http://'.config_item('icecat_username').':'.config_item('icecat_password').'@data.icecat.biz/export/level4/'.config_item('icecat_language').'/'.$id.'.xml';
		$xml 				= @file_get_contents($xml);

		// Valid XML?
		if(@simplexml_load_string($xml) == FALSE){
			$xml = FALSE;
		}

		if($xml)
		{
			$xml 				= new SimpleXMLElement($xml);

			// Set xpaths
			$product 			= $xml->xpath("/ICECAT-interface/Product");
			$product_attr 		= $product[0]->attributes();

			$description 		= $xml->xpath("/ICECAT-interface/Product/ProductDescription");
			$description_attr 	= $description[0]->attributes();

			$supplier			= $xml->xpath("/ICECAT-interface/Product/Supplier");
			$supplier_attr		= $supplier[0]->attributes();

			$images 			= $xml->xpath("/ICECAT-interface/Product/ProductGallery");

			$eans				= $xml->xpath("/ICECAT-interface/Product/EANCode");

			$featurelogo		= $xml->xpath("/ICECAT-interface/Product/FeatureLogo");

			$spec_group			= $xml->xpath("/ICECAT-interface/Product/CategoryFeatureGroup");
			$spec_item			= $xml->xpath("/ICECAT-interface/Product/ProductFeature");

			$families			= $xml->xpath("/ICECAT-interface/Product/ProductFamily");

			// Reconnect in case MySQL server has gone away
			$this->db->reconnect();

			// Family present?
			if(isset($families[0]->Name['Value']))
			{
				// New family? Inerst!
				$this->db->query("INSERT IGNORE INTO tb_family (
					family_iid,
					name,
					name_url
				) VALUES (
					".$this->db->escape((int)$families[0]->Name['ID']).",
					".$this->db->escape((string)$families[0]->Name['Value']).",
					".$this->db->escape(url_title((string)$families[0]->Name['Value'],'-',TRUE))."
				)");
				// Get family ID
				$family_id = $this->db->insert_id();
				if(!$family_id){
					$query = $this->db->query("SELECT family_id FROM tb_family WHERE family_iid=".$this->db->escape((int)$families[0]->Name['ID'])."");
					$row = $query->row_array();
					$family_id = $row['family_id'];
				}
			} else {
				$family_id = 0;
			}

			// New brand? Insert!
			$this->db->query("INSERT IGNORE INTO tb_brand (
				brand_iid,
				name,
				name_url
			) VALUES (
				".$this->db->escape((int)$supplier_attr['ID']).",
				".$this->db->escape((string)$supplier_attr['Name']).",
				".$this->db->escape(url_title((string)$supplier_attr['Name'],'-',TRUE))."
			)");
			// Get brand ID
			$brand_id = $this->db->insert_id();
			if(!$brand_id){
				$query = $this->db->query("SELECT brand_id FROM tb_brand WHERE brand_iid=".$this->db->escape((int)$supplier_attr['ID'])."");
				$row = $query->row_array();
				$brand_id = $row['brand_id'];
			}

			// New product? Insert!
			$this->db->query("
				INSERT INTO tb_product (
					product_iid,
					brand_id,
					family_id,
					name,
					title,
					title_url,
					sku,
					`release`,
					img_thumb,
					img_low,
					img_mid,
					img_high,
					pdf_spec,
					pdf_manual,
					descr_long,
					descr_short,
					url,
					warranty
				) VALUES (
					".$this->db->escape((int)$product_attr['ID']).",
					".$this->db->escape($brand_id).",
					".$this->db->escape($family_id).",
					".$this->db->escape((string)$product_attr['Name']).",
					".$this->db->escape((string)$product_attr['Title']).",
					".$this->db->escape(url_title((string)$product_attr['Title'],'-',TRUE)).",
					".$this->db->escape((string)$product_attr['Prod_id']).",
					".$this->db->escape((string)$product_attr['ReleaseDate']).",
					".$this->db->escape( download_image( 'p'.(int)$product_attr['ID'] , (string)$product_attr['ThumbPic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'thumb' ) ).",
					".$this->db->escape( download_image( 'p'.(int)$product_attr['ID'] , (string)$product_attr['LowPic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'low' ) ).",
					".$this->db->escape( download_image( 'p'.(int)$product_attr['ID'] , (string)$product_attr['Pic500x500'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'mid' ) ).",
					".$this->db->escape( download_image( 'p'.(int)$product_attr['ID'] , (string)$product_attr['HighPic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'high' ) ).",
					".$this->db->escape((string)$description_attr['PDFURL']).",
					".$this->db->escape((string)$description_attr['ManualPDFURL']).",
					".$this->db->escape(str_replace('\n','<br />',(string)$description_attr['LongDesc'])).",
					".$this->db->escape((string)$description_attr['ShortDesc']).",
					".$this->db->escape((string)$description_attr['URL']).",
					".$this->db->escape(str_replace('\n','<br />',(string)$description_attr['WarrantyInfo']))."
				)
				ON DUPLICATE KEY UPDATE 
				product_id=LAST_INSERT_ID(product_id),
				brand_id=VALUES(brand_id),
				family_id=VALUES(family_id),
				name=VALUES(name),
				title=VALUES(title),
				title_url=VALUES(title_url),
				sku=VALUES(sku),
				`release`=VALUES(`release`),
				img_thumb=VALUES(img_thumb),
				img_low=VALUES(img_low),
				img_mid=VALUES(img_mid),
				img_high=VALUES(img_high),
				pdf_spec=VALUES(pdf_spec),
				pdf_manual=VALUES(pdf_manual),
				descr_long=VALUES(descr_long),
				descr_short=VALUES(descr_short),
				url=VALUES(url),
				warranty=VALUES(warranty)
			");
		
			// Get product ID
			$product_id = $this->db->insert_id();

			// Remove all for a fresh insert
			$this->db->query("DELETE FROM tb_spec_fk WHERE product_id=".$this->db->escape($product_id)."");
			$this->db->query("DELETE FROM tb_image WHERE product_id=".$this->db->escape($product_id)."");
			$this->db->query("DELETE FROM tb_ean WHERE product_id=".$this->db->escape($product_id)."");
			$this->db->query("DELETE FROM tb_featurelogo_fk WHERE product_id=".$this->db->escape($product_id)."");

			//New images? Insert!
			foreach($images[0] as $image)
			{
				$image_attr		= $image->attributes();

				$this->db->query("INSERT IGNORE INTO tb_image (
					image_id,
					product_id,
					image_thumb,
					image_low,
					image_mid,
					image_high
				) VALUES (
					".$this->db->escape((int)$image_attr['ProductPicture_ID']).",
					".$this->db->escape((int)$product_id).",
					".$this->db->escape( download_image( (int)$image_attr['ProductPicture_ID'] , (string)$image_attr['ThumbPic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'thumb' ) ).",
					".$this->db->escape( download_image( (int)$image_attr['ProductPicture_ID'] , (string)$image_attr['LowPic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'low' ) ).",
					".$this->db->escape( download_image( (int)$image_attr['ProductPicture_ID'] , (string)$image_attr['Pic500x500'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'mid' ) ).",
					".$this->db->escape( download_image( (int)$image_attr['ProductPicture_ID'] , (string)$image_attr['Pic'] , url_title((string)$product_attr['Title'],'-',TRUE) , 'high' ) )."
				)");
			}

			//New EANs? Insert!
			foreach($eans as $ean)
			{
				if((string)$ean[0]['EAN'])
				{
					$this->db->query("INSERT IGNORE INTO tb_ean (
						product_id,
						ean
					) VALUES (
						".$this->db->escape((int)$product_id).",
						".$this->db->escape((string)$ean[0]['EAN'])."
					)");
				}
			}

			//New Featurelogo? Insert!
			foreach($featurelogo as $logo)
			{
				$logo_attr		= $logo->attributes();

				$this->db->query("INSERT INTO tb_featurelogo (
					featurelogo_iid,
					image,
					descr
				) VALUES (
					".$this->db->escape((int)$logo_attr['Feature_ID']).",
					".$this->db->escape( download_image( 'f'.(int)$logo_attr['Feature_ID'] , (string)$logo_attr['LogoPic'] , 'feature' , 'logo' ) ).",
					".$this->db->escape(trim((string)$logo->Descriptions->Description))."
				)
				ON DUPLICATE KEY UPDATE 
				featurelogo_id=LAST_INSERT_ID(featurelogo_id),
				image=VALUES(image),
				descr=VALUES(descr)
				");
				// Get product ID
				$featurelogo_id = $this->db->insert_id();
				// if(!$featurelogo_id){
				// 	$query = $this->db->query("SELECT featurelogo_id FROM tb_featurelogo WHERE featurelogo_iid=".$this->db->escape((int)$logo_attr['Feature_ID'])."");
				// 	$row = $query->row_array();
				// 	$featurelogo_id = $row['featurelogo_id'];
				// }

				$this->db->query("INSERT IGNORE INTO tb_featurelogo_fk (
					product_id,
					featurelogo_id
				) VALUES (
					".$this->db->escape((int)$product_id).",
					".$this->db->escape((int)$featurelogo_id)."
				)");
			}

			//New Spec group? Insert!
			foreach($spec_group as $group)
			{
				//TODO: Search at ID instead of name! So we can change the name ;)
				$query = $this->db->query("SELECT group_iids FROM tb_spec_group WHERE name=".$this->db->escape((string)$group->FeatureGroup->Name[0]['Value'])."");
				$row = $query->row_array();
				if(isset($row['group_iids']))
				{
					if(strstr($row['group_iids'],'!'.(int)$group[0]['ID'].'!') === FALSE){
						$this->db->query("UPDATE tb_spec_group SET
							group_iids=CONCAT(group_iids,".$this->db->escape('!'.(int)$group[0]['ID'].'!').")
							WHERE name=".$this->db->escape((string)$group->FeatureGroup->Name[0]['Value'])."
						");
					}
				} else {
					$this->db->query("INSERT INTO tb_spec_group (
						group_iids,
						name
					) VALUE (
						".$this->db->escape('!'.(int)$group[0]['ID'].'!').",
						".$this->db->escape((string)$group->FeatureGroup->Name[0]['Value'])."
					)");
				}
			}

			//Alle specs...
			foreach($spec_item as $item)
			{
				if($item[0]['Value'] != 'Icecat.biz')
				{
					//Spec_feature
					$this->db->query("INSERT IGNORE INTO tb_spec_feature (
						feature_iid,
						name
					) VALUES (
						".$this->db->escape((int)$item->Feature->Name[0]['ID']).",
						".$this->db->escape((string)$item->Feature->Name[0]['Value'])."
					)");
					// Get feature ID
					$feature_id = $this->db->insert_id();
					if(!$feature_id){
						$query = $this->db->query("
							SELECT feature_id 
							FROM tb_spec_feature 
							WHERE feature_iid=".$this->db->escape((int)$item->Feature->Name[0]['ID'])."
						");
						$row = $query->row_array();
						$feature_id = $row['feature_id'];
					}

					//Spec_value
					$this->db->query("INSERT IGNORE INTO tb_spec_value (
						value_mid,
						name
					) VALUES (
						MD5(".$this->db->escape(str_replace('\n','<br />',(string)$item[0]['Value']))."),
						".$this->db->escape(str_replace('\n','<br />',(string)$item[0]['Value']))."
					)");
					// Get value ID
					$value_id = $this->db->insert_id();
					if(!$value_id){
						$query = $this->db->query("SELECT value_id FROM tb_spec_value WHERE value_mid=MD5(".$this->db->escape(str_replace('\n','<br />',(string)$item[0]['Value'])).")");
						$row = $query->row_array();
						$value_id = $row['value_id'];
					}

					//Spec_signs
					$sign_id = FALSE;
					if((string)$item->Feature->Measure->Signs->Sign)
					{
						$this->db->query("INSERT IGNORE INTO tb_spec_sign (
							name
						) VALUES (
							".$this->db->escape((string)$item->Feature->Measure->Signs->Sign)."
						)");
						// Get sign ID
						$sign_id = $this->db->insert_id();
						if(!$sign_id){
							$query = $this->db->query("SELECT sign_id FROM tb_spec_sign WHERE name=".$this->db->escape((string)$item->Feature->Measure->Signs->Sign)."");
							$row = $query->row_array();
							$sign_id = $row['sign_id'];
						}
					}

					//Get group ID
					$qgroup = $this->db->query("
						SELECT group_id 
						FROM tb_spec_group 
						WHERE group_iids LIKE '%".$this->db->escape_like_str('!'.(int)$item[0]['CategoryFeatureGroup_ID'].'!')."%'
					");
					$qrow = $qgroup->row_array();
					$group_id = $qrow['group_id'];

					//Spec_feature
					$this->db->query("INSERT IGNORE INTO tb_spec_fk (
						product_id,
						`group`,
						feature,
						value,
						sign
					) VALUES (
						".$this->db->escape((int)$product_id).",
						".$this->db->escape((int)$group_id).",
						".$this->db->escape((int)$feature_id).",
						".$this->db->escape((int)$value_id).",
						".$this->db->escape((int)( ($sign_id) ? $sign_id : 0 ))."
					)");

				}
				
			}

			// Gete data to generate a new short description
			$data 			= $this->product_model->get_product($product_id);
			$data['prices'] = $this->product_model->get_product_prices($product_id);
			$data['specs'] 	= $this->product_model->get_product_specs($product_id);

			// Update short description
			$this->db->query("UPDATE tb_product SET descr_short=".$this->db->escape(generate_description($data))." WHERE product_id=".$this->db->escape($product_id));

		}
	}

}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */