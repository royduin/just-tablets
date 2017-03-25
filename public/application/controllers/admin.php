<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!in_array($this->input->ip_address(),$this->config->item('admins'))){
			show_404();
		}
		echo $this->load->view('layout/header','',TRUE);
	}

	public function index()
	{
		echo '<h1>Administration</h1>
		<ul class="inline inline-centered">
			<li><a class="" href="'.site_url().'admin/features'.'"><img src="'.site_url().'img/icons/admin/features.png"><br />Features</a></li>
			<li><a class="" href="'.site_url().'admin/missing'.'"><img src="'.site_url().'img/icons/admin/missing_specifications.png"><br />Missing specifications</a></li>
			<li><a class="" href="'.site_url().'admin/clear_cache'.'"><img src="'.site_url().'img/icons/admin/clear_page_cache.png"><br />Clear page cache</a></li>
			<li><a class="" href="'.site_url().'admin/shops'.'"><img src="'.site_url().'img/icons/admin/shops.png"><br />Shops</a></li>
			<li><a class="" href="'.site_url().'admin/add_product'.'"><img src="'.site_url().'img/icons/admin/add_product.png"><br />Add product</a></li>
			<li><a class="" href="'.site_url().'admin/passwords'.'"><img src="'.site_url().'img/icons/admin/passwords.png"><br />Passwords</a></li>
		</ul>';
		$this->load->view('layout/footer');
	}

	public function passwords()
	{
		echo '<h1>Passwords</h1>';
		echo '<div class="accordion" id="accordion2">
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
						Twitter
					</a>
				</div>
				<div id="collapseOne" class="accordion-body collapse">
					<div class="accordion-inner">
						<strong>Email</strong> info@justtablets.nl<br />
						<strong>Password</strong> *snip*
					</div>
				</div>
			</div>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
						Facebook
					</a>
				</div>
				<div id="collapseTwo" class="accordion-body collapse">
					<div class="accordion-inner">
						<strong>Email</strong> roy@justtablets.nl<br />
						<strong>Password</strong> *snip*
					</div>
				</div>
			</div>
		</div>';

		$this->load->view('layout/footer');
	}

	public function add_product()
	{
		// Load Icecat helper
		$this->load->helper('icecat_helper');

		echo '<h1>Add product</h1>';

		// Posted?
		if( $this->input->post() )
		{
			// Icecat ID
			if( $this->input->post('icecat') )
			{
				$data['id_full'] 	= $this->input->post('icecat');

			// EAN
			} elseif( $this->input->post('ean') )
			{
				$data['ean'] 		= $this->input->post('ean');

			// SKU
			} elseif( $this->input->post('brand') && $this->input->post('sku') )
			{
				$data['brand'] 		= $this->input->post('brand');
				$data['sku'] 		= $this->input->post('sku');
			}

			// Remaining credentials
			$data['language'] = config_item('icecat_language');
			$data['username'] = config_item('icecat_username');
			$data['password'] = config_item('icecat_password');

			// Get Icecat data
			$data = icecat_to_array($data);

			// More than 10 specifications?
			if(isset($data['spec']) && count($data['spec']) < 10)
			{
				$data[1] = 'There\'re to less specifications!';
				unset($data['id']);
			}

			// Check if it's a tablet
			if(isset($data['category_id']) && $data['category_id'] != 897)
			{
				$data[1] = 'It\'s not a tablet!';
				unset($data['id']);
			}

			// Information present?
			if(!isset($data['id']))
			{
				// Open alertbox
				echo '<div class="alert alert-error">';

				// Check for each error number and show the error
				if(isset($data[1]))
				{
					echo '<strong>Error 1</strong>: '.$data[1];
			 
				} elseif(isset($data[2]))
				{
					echo '<strong>Error 2</strong>: '.$data[2];
			 
				} elseif(isset($data[3]))
				{
					echo '<strong>Error 3</strong>: '.$data[3];
				}

				echo '</div><a href="'.site_url().'admin/add_product/" class="btn btn-primary">Back</a>';
				exit;
			}

			// Product information loaded succesfully!
			echo '<div class="alert alert-success"><strong>Product added!</strong> Or updated in case it was already there.</div>';
			echo '<a href="'.site_url().'admin/add_product/" class="btn btn-primary">Back</a>';

			// Call cronjob
			echo '<br /><iframe src="'.site_url().'cron/handle_product/'.$data['id'].'/" style="border:0;"></iframe>';

			// Call feeds
			echo '<iframe src="'.site_url().'cron/feeds/" style="border:0;"></iframe>';

			exit;
		}

		echo '<ul id="myTab" class="nav nav-tabs">
			<li><a href="#icecat" data-toggle="tab">Icecat ID</a></li>
			<li class="active"><a href="#ean" data-toggle="tab">EAN</a></li>
			<li><a href="#sku" data-toggle="tab">SKU</a></li>
		</ul>
		<div id="myTabContent" class="tab-content">
			<div class="tab-pane fade" id="icecat">
				'.form_open().'
					<input type="text" name="icecat" placeholder="Icecat ID">
					<br>
					<input type="submit" value="Add" class="btn btn-primary">
				</form>
			</div>
			<div class="tab-pane fade in active" id="ean">
				'.form_open().'
					<input type="text" name="ean" placeholder="EAN">
					<br>
					<input type="submit" value="Add" class="btn btn-primary">
				</form>
			</div>
			<div class="tab-pane fade" id="sku">
				'.form_open().'
					<input type="text" name="brand" placeholder="Brand">
					<input type="text" name="sku" placeholder="SKU">
					<br>
					<input type="submit" value="Add" class="btn btn-primary">
				</form>
			</div>
		</div>';

		$this->load->view('layout/footer');
	}

	public function shops()
	{
		$query = $this->db->query("SELECT * FROM tb_shop");

		if( $this->input->post() )
		{
			foreach($query->result_array() as $shop)
			{
				$this->db->query("
					UPDATE
						tb_shop 
					SET
						name=".$this->db->escape($this->input->post($shop['shop_id'].'_name')).",
						city=".$this->db->escape($this->input->post($shop['shop_id'].'_city')).",
						website=".$this->db->escape($this->input->post($shop['shop_id'].'_website')).",
						feed=".$this->db->escape($this->input->post($shop['shop_id'].'_feed')).",
						global_shipping=".$this->db->escape($this->input->post($shop['shop_id'].'_global_shipping')).",
						feed_delimiter=".$this->db->escape(($this->input->post($shop['shop_id'].'_feed_delimiter') == ' ' ? "\t" : $this->input->post($shop['shop_id'].'_feed_delimiter'))).",
						feed_sku=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_sku')).",
						feed_ean=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_ean')).",
						feed_price=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_price')).",
						feed_url=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_url')).",
						feed_stock_store=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_stock_store')).",
						feed_stock_supplier=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_stock_supplier')).",
						feed_stock_text=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_stock_text')).",
						feed_shipping=".$this->db->escape($this->input->post($shop['shop_id'].'_feed_shipping'))."
					WHERE
						shop_id=".$shop['shop_id']."
				");
			}
			echo '<div class="alert alert-success">Everything is saved! Please wait for 2 seconds...</div>';
			echo '<meta http-equiv="refresh" content="2; url='.site_url().'admin/shops/'.'">';
			exit;
		}

		echo '</div>';
		echo form_open();
		echo '<table class="table table-striped table-hover table-condensed">
				<tr>
					<th>Name</th>
					<th>City</th>
					<th>Website</th>
					<th>Feed</th>
					<th>Global shipping</th>
					<th>Delimiter</th>
					<th>SKU</th>
					<th>EAN</th>
					<th>Price</th>
					<th>URL</th>
					<th>Stock store</th>
					<th>Stock supplier</th>
					<th>Stock text</th>
					<th>Shipping</th>
					<th></th>
				</tr>';
		foreach($query->result_array() as $shop)
		{
			echo '<tr>
				<td><input class="span2" type="text" name="'.$shop['shop_id'].'_name" value="'.$shop['name'].'"></td>
				<td><input class="span2" type="text" name="'.$shop['shop_id'].'_city" value="'.$shop['city'].'"></td>
				<td><input class="span2" type="text" name="'.$shop['shop_id'].'_website" value="'.$shop['website'].'"></td>
				<td><input class="span2" type="text" name="'.$shop['shop_id'].'_feed" value="'.$shop['feed'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_global_shipping" value="'.$shop['global_shipping'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_delimiter" value="'.$shop['feed_delimiter'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_sku" value="'.$shop['feed_sku'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_ean" value="'.$shop['feed_ean'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_price" value="'.$shop['feed_price'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_url" value="'.$shop['feed_url'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_stock_store" value="'.$shop['feed_stock_store'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_stock_supplier" value="'.$shop['feed_stock_supplier'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_stock_text" value="'.$shop['feed_stock_text'].'"></td>
				<td><input class="span1" type="text" name="'.$shop['shop_id'].'_feed_shipping" value="'.$shop['feed_shipping'].'"></td>
				<td><a href="'.site_url().'cron/shops/'.$shop['shop_id'].'/TRUE" target="_blank" class="btn btn-primary btn-mini">Run!</a></td>
			</tr>';
		}
		echo '</table><input type="submit" value="Opslaan" class="btn btn-primary btn-block btn-large"></form><div>';
		$this->load->view('layout/footer');
	}

	public function clear_cache()
	{
		// Get cache path
		$path = $this->config->item('cache_path');
		
		// Check the if set else default path
		$cache_path = ($path == '') ? APPPATH.'cache/' : $path;
		
		// Open cache directory
		$handle = opendir($cache_path);

		// Loop trough the directory
		while (($file = readdir($handle))!== FALSE) 
		{
			// Leave the directory protection alone
			if ($file != '.htaccess' && $file != 'index.html')
			{
				// Remove the cache file
			   @unlink($cache_path.'/'.$file);
			}
		}

		// Close the directory
		closedir($handle);

		// Yeah! :)
		echo 'Cleared!';
	}

	public function missing()
	{
		echo '<h1><a href="'.site_url().'admin">Administration</a> / Missing specifications</h1>';
		echo '<div class="alert"><strong>Fouten sturen naar: <a href="mailto:editor@icecat.biz" target="_blank">editor@icecat.biz</a></strong></div>';

		$missing = $this->db->query("
			SELECT
				*
			FROM
				tb_spec_feature
			WHERE
				filter != 0
		");

		foreach($missing->result_array() as $mis)
		{
			if($mis['filter'] == 1)
			{
				$features = $this->db->query("
					SELECT
						tb_product.product_id,
						title,
						tb_spec_fk.value
					FROM
						tb_product
					LEFT JOIN
						tb_spec_fk
					ON
						tb_spec_fk.product_id = tb_product.product_id
						AND tb_spec_fk.feature = ".$mis['feature_id']."
					WHERE
						feature is null OR (value=18046)
				");
			}
			else
			{
				$features = $this->db->query("
					SELECT
						tb_product.product_id,
						title,
						tb_spec_fk.value
					FROM
						tb_product
					LEFT JOIN
						tb_spec_fk
					ON
						tb_spec_fk.product_id = tb_product.product_id
						AND tb_spec_fk.feature = ".$mis['feature_id']."
					LEFT JOIN
						tb_spec_value
					ON
						tb_spec_fk.value = tb_spec_value.value_id
					WHERE
						feature is null OR (value=18046) OR concat('',tb_spec_value.name * 1) != tb_spec_value.name
				");
			}
			
			$amount = $features->num_rows();

			echo '<h2 style="cursor: pointer;" title="Open feature" data-toggle="collapse" data-target="#feature_'.$mis['feature_id'].'">';
			echo '<div style="width: 100px; display: inline-block;">'.(!$amount ? '<span class="badge badge-success">OK !</span>' : ' <span class="badge badge-important">'.$amount.' errors</span>').'</div> ';
			echo '<div style="width: 100px; display: inline-block;">'.(($mis['filter'] == 2) ? ' <span class="label label-important">Slider!</span>' : ' <span class="label label-warning">Checkboxes</span>').'</div>';
			echo $mis['feature_id'].' - '.$mis['name'];
			echo '</h2>';

			if($amount){
			echo '<div id="feature_'.$mis['feature_id'].'" class="collapse">';
				echo '<table class="table table-striped table-hover table-condensed">';
				foreach($features->result_array() as $feature)
				{
					echo (($feature['value'] OR $mis['filter'] == 2) ? '<tr class="error">' : '<tr class="warning">');
					echo '<td>
								<a href="'.site_url().'tablet/'.$feature['product_id'].'">'.$feature['product_id'].'</a>
							</td>
							<td>
								<a href="'.site_url().'tablet/'.$feature['product_id'].'">'.$feature['title'].'</a>
							</td>
							<td>';
								if($feature['value'])
								{
									if($feature['value'] != 18046){
										echo 'Not numeric';
									} else {
										echo 'Empty';
									}
								} else {
									echo 'Missing';
								}
							echo '</td>
						</tr>';
				}
				echo '</table>';
			echo '</div>';
			}
		}
		$this->load->view('layout/footer');
	}

	public function features()
	{
		echo '<h1><a href="'.site_url().'admin">Administration</a> / Features</h1>';
		$features = $this->db->query("
			SELECT
				*
			FROM
				tb_spec_feature
			ORDER BY
				`default` DESC,
				CASE filter WHEN 0 THEN 1 ELSE -1 END ASC,
				`order` ASC	
		");

		$total_products = $this->db->count_all('tb_product');

		if( $this->input->post() )
		{
			foreach($features->result_array() as $feature)
			{
				if(		$feature['name'] 				!= $this->input->post($feature['feature_id'].'_name')
					|| 	$feature['filter'] 				!= $this->input->post($feature['feature_id'].'_filter')
					|| 	$feature['order'] 				!= $this->input->post($feature['feature_id'].'_order')
					|| 	$feature['default'] 			!= $this->input->post($feature['feature_id'].'_default')
					|| 	$feature['spec_filter'] 		!= $this->input->post($feature['feature_id'].'_spec_filter')
					|| 	$feature['spec_filter_default'] != $this->input->post($feature['feature_id'].'_spec_filter_default')
				)
				{
					$this->db->query("
						UPDATE
							tb_spec_feature 
						SET
							name=".$this->db->escape($this->input->post($feature['feature_id'].'_name')).",
							filter=".$this->db->escape($this->input->post($feature['feature_id'].'_filter')).",
							`order`=".$this->db->escape($this->input->post($feature['feature_id'].'_order')).",
							`default`=".($this->input->post($feature['feature_id'].'_default') ? 1 : 0).",
							spec_filter=".($this->input->post($feature['feature_id'].'_spec_filter') ? 1 : 0).",
							spec_filter_default=".$this->db->escape($this->input->post($feature['feature_id'].'_spec_filter_default'))."
						WHERE
							feature_id=".$feature['feature_id']."
					");
				}
			}
			echo '<div class="alert alert-success">Everything is saved! Please wait for 2 seconds...</div>';
			echo '<meta http-equiv="refresh" content="2; url='.site_url().'admin/features/'.'">';
			exit;
		}

		echo form_open();
		echo '<input type="submit" class="btn btn-primary pull-right" value="Opslaan">';


		echo '<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Description</th>
				<th>Filter</th>
				<th>Order</th>
				<th>Default</th>
				<th>Spec filter</th>
				<th>Spec filter default</th>
				<th>Missing?</th>
			</tr>
		<thead>
		<tbody>';
		foreach($features->result_array() as $feature)
		{
			// Search for empty values by feature
			$missing = $this->db->query("
				SELECT
					tb_product.product_id,
					title,
					tb_spec_fk.value
				FROM
					tb_product
				LEFT JOIN
					tb_spec_fk
				ON
					tb_spec_fk.product_id = tb_product.product_id
					AND tb_spec_fk.feature = ".$feature['feature_id']."
				WHERE
					feature is null OR (value=18046)
			");
			$missing = $missing->num_rows();


			echo (($feature['filter']) ? ($feature['default'] ? '<tr class="success">' : '<tr class="warning">') : '<tr>');
			echo '<td>'.$feature['feature_id'].'</td>
				<td><input type="text" name="'.$feature['feature_id'].'_name" value="'.$feature['name'].'"></td>
				<td>'.($feature['descr'] ?: '-').'</td>
				<td>
					<select class="span2" name="'.$feature['feature_id'].'_filter">
						<option value="0"></option>
						<option value="1"'.(($feature['filter'] == 1) ? ' selected' : '').'>Checkboxes</option>
						<option value="2"'.(($feature['filter'] == 2) ? ' selected' : '').'>Slider</option>
					</select>
				</td>
				<td><input class="span1" type="text" name="'.$feature['feature_id'].'_order" value="'.$feature['order'].'"></td>
				<td><input type="checkbox" name="'.$feature['feature_id'].'_default"'.($feature['default'] ? ' checked' : '').'></td>
				<td><input type="checkbox" name="'.$feature['feature_id'].'_spec_filter"'.($feature['spec_filter'] ? ' checked' : '').'></td>
				<td><input class="span1" type="text" name="'.$feature['feature_id'].'_spec_filter_default" value="'.$feature['spec_filter_default'].'"></td>
				<td>'.(!$missing ? '<span class="badge badge-success">OK</span>' : '<a href="'.site_url().'admin/missing/"><span class="badge badge-important">'.($missing == $total_products ? 'EVERYWHERE' : $missing).'</span></a>').'</td>
			</tr>';
		}
		echo '</tbody></table>
		<input type="submit" class="btn btn-primary pull-right" value="Opslaan">
		</form>';
		$this->load->view('layout/footer');
	}

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */