<div class="well">
	<h1>Tablets vergelijken</h1>
	<p>Bekijk en vergelijk de prijzen en specificaties eenvoudig van alle <?=$total;?> tablets bij Just Tablets.</p>
</div>
<div class="row">
	<div class="span2">
		<div class="well well-small filter_container">
			<? echo form_open(); ?>
				<h3>Filters</h3>
				<div class="filter_options">
					
					<div class="filter_block">
						<strong>Status</strong>
						<label for="buyable" class="checkbox">
							<input type="checkbox" name="buyable" id="buyable" value="true" checked="checked">
							Te koop
						</label>
					</div>
					
					<div class="filter_block">
						<strong>Prijs</strong>
						<div class="filter_select">
							<label for="price_from">Van</label>
							<select name="price_from" class="span1" id="price_from">
								<? foreach($prices as $price): ?>
									<option value="<?=$price;?>"><?=(($price !== '') ? '&euro;'.$price : 'Prijsloos');?></option>
								<? endforeach; ?>
							</select>
							<label for="price_to">Tot</label>
							<select name="price_to" class="span1" id="price_to">
								<? foreach($prices as $key=>$price): ?>
									<option value="<?=$price;?>"<?=(($key + 1 == count($prices)) ? ' selected' : '')?>>&euro;<?=$price;?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					
					<div class="filter_block">
						<strong>Merk</strong>
						<ul>
						<? foreach($brands as $key=>$brand): ?>
							<? if($key == 6): ?>
								</ul>
								<ul class="hide toggle_brands">
							<? endif; ?>
							<li>
								<label for="b<?=$brand['brand_id'];?>" class="checkbox">
									<input type="checkbox" name="brands" id="b<?=$brand['brand_id'];?>" value="<?=$brand['brand_id'];?>">
									<?=$brand['name'];?>
									<span class="filter_count">(<?=$brand['brand_count'];?>)</span>
								</label>
							</li>
						<? endforeach; ?>
						</ul>
						<? if(count($brands) > 6): ?>
							<a href="#" class="toggler" data-to="brands"><i class="icon-chevron-down"></i> Alle opties</a>
						<? endif; ?>
					</div>
					
					<? foreach($filters as $key=>$filter): ?>
						
					<div class="filter_block">

						<? if($filter['default']): ?>
							<strong><?=$filter['name'];?></strong>
						<? else: ?>
							<strong class="notdefault_title"><i class="icon-chevron-right"></i> <?=$filter['name'];?></strong>
						<? endif; ?>
						
						<div<?=($filter['default'] ? '' : ' class="notdefault_div"');?>>
						

						<? if($filter['filter'] == 1): ?>
							<ul>
							<? foreach($filter['values'] as $key=>$item): ?>

								<? if($key == 6): ?>
									</ul>
									<ul class="hide toggle_<?=$filter['feature_id'];?>">
								<? endif; ?>

								<li>
									<label for="f<?=$filter['feature_id'];?>_<?=$item['value_id'];?>" class="checkbox">
										<input type="checkbox" name="f<?=$filter['feature_id'];?>" id="f<?=$filter['feature_id'];?>_<?=$item['value_id'];?>" value="<?=$item['value_id'];?>">
										<?=value($item['value_name'],TRUE);?> <small><?=(strlen($item['sign_name']) <= 2 ? strtoupper($item['sign_name']) : strtolower($item['sign_name']));?></small>
										<span class="filter_count"></span>
									</label>
								</li>
							<? endforeach; ?>
							</ul>
							<? if(count($filter['values']) > 6): ?>
								<a href="#" class="toggler" data-to="<?=$filter['feature_id'];?>"><i class="icon-chevron-down"></i> Alle opties</a>
							<? endif; ?>
						<? else: ?>
							<div class="filter_select">
								<label for="o<?=$filter['feature_id'];?>f">Van</label>
								<select name="o<?=$filter['feature_id'];?>" class="span1" id="o<?=$filter['feature_id'];?>f">
									<? foreach($filter['values'] as $item): ?>
										<option value="<?=value($item['value_name'],TRUE);?>"><?=value($item['value_name'],TRUE);?><?=(strlen($item['sign_name']) <= 2 ? strtoupper($item['sign_name']) : strtolower($item['sign_name']));?></option>
									<? endforeach; ?>
								</select>
								<label for="o<?=$filter['feature_id'];?>t">Tot</label>
								<select name="o<?=$filter['feature_id'];?>" class="span1" id="o<?=$filter['feature_id'];?>t">
									<? foreach($filter['values'] as $key=>$item): ?>
										<option value="<?=value($item['value_name'],TRUE);?>"<?=(($key + 1 == count($filter['values'])) ? ' selected' : '')?>><?=value($item['value_name'],TRUE);?><?=(strlen($item['sign_name']) <= 2 ? strtoupper($item['sign_name']) : strtolower($item['sign_name']));?></option>
									<? endforeach; ?>
								</select>
							</div>
						<? endif; ?>

						</div>

					</div>
					<? endforeach; ?>
										
				</div>
			</form>
		</div>
	</div>
	<div class="span10">
		<ul class="breadcrumb" id="sorting">
			<li>Sortering:</li>
			<li>
				<a href="<?=site_url().'tablets-vergelijken/?sort=popularity&sort_dir=asc';?>" id="order_popularity" rel="nofollow" rel="tooltip_clickable" title="Oplopend sorteren op populariteit" class="sort current desc">
					Populariteit
				</a>
			</li>
			<li><span class="divider">/</span></li>
			<li>
				<a href="<?=site_url().'tablets-vergelijken/?sort=price&sort_dir=desc';?>" id="order_price" rel="nofollow" rel="tooltip_clickable" title="Aflopend sorteren op prijs" class="sort">
					Prijs
				</a>
			</li>

			<?
			// Sort by spec_filter_default
			$spec_filterss = $spec_filters;
			usort($spec_filterss,function($a,$b){
				return $a["spec_filter_default"] - $b["spec_filter_default"];
			});
			?>

			<? foreach($spec_filterss as $key=>$value): ?>
				<? if($value['spec_filter_default'] != 0): ?>
					<li><span class="divider">/</span></li>
					<li>
						<a href="<?=site_url().'tablets-vergelijken/?sort=spec&sort_dir=desc&sort_spec='.$value['spec_filter_default'];?>" id="order_spec<?=$value['spec_filter_default'];?>" rel="nofollow" rel="tooltip_clickable" title="Aflopend sorteren op <?=$value['name'];?>" class="sort">
							<?=$value['name'];?>
						</a>
					</li>
				<? endif; ?>
			<? endforeach; ?>

			<li class="pull-right">
				<span id="total_amount"><?=$total_amount;?></span> tablet(s) gevonden 
				<span class="divider">/</span> 
				Pagina <span id="current_page">1</span> van <span id="total_pages"><?=$total_pages;?></span>
			</li>
		</ul>
		<div id="table">
		<table class="table table-striped table-hover compare">
			<thead>
				<tr>
					<th></th>
					<th><a href="<?=site_url().'tablets-vergelijken/';?>" class="btn btn-primary btn-small" id="compare_button" >Vergelijken</a></th>
					<th></th>
					<th class="col_center">
						<select id="spec1" name="spec1" class="span1">
							<? foreach($spec_filters as $key=>$filter): ?>
								<option value="<?=$filter['feature_id'];?>"<?=($filter['spec_filter_default'] == 1 ? ' selected' : '');?>><?=$filter['name'];?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="col_center">
						<select id="spec2" name="spec2" class="span1">
							<? foreach($spec_filters as $key=>$filter): ?>
								<option value="<?=$filter['feature_id'];?>"<?=($filter['spec_filter_default'] == 2 ? ' selected' : '');?>><?=$filter['name'];?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="col_center">
						<select id="spec3" name="spec3" class="span1">
							<? foreach($spec_filters as $key=>$filter): ?>
								<option value="<?=$filter['feature_id'];?>"<?=($filter['spec_filter_default'] == 3 ? ' selected' : '');?>><?=$filter['name'];?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="col_center">Prijs</th>
				</tr>
			</thead>
			<tbody>
				<? foreach($tablets as $tablet): ?>
				<? $url = site_url().'tablet/'.$tablet['product_id'].'/'.$tablet['title_url'].'/';?>
					<tr>
						<td class="col_compare">
							<input type="checkbox" name="compare[]" class="compare_item" value="<?=$tablet['product_id'];?>" />
						</td>
						<td class="col_img">
							<a href="<?=$url;?>" title="<?=html_escape($tablet['title']);?>" rel="popover" data-img="<?=$tablet['img_low'];?>">
								<img src="<?=$tablet['img_thumb'];?>" alt="<?=html_escape($tablet['title']);?>" />
							</a>
						</td>
						<td class="col_name">
							<h2><a href="<?=$url;?>" title="<?=html_escape($tablet['title']);?>"><?=html_escape($tablet['title']);?></a></h2>
							<p><?=$tablet['descr_short'];?></p>
						</td>
						<td class="col_spec">
							<p title="<?=html_escape(col_spec_value($tablet['spec_1'],TRUE));?>"><?=col_spec_value($tablet['spec_1']);?></p>
						</td>
						<td class="col_spec">
							<p title="<?=html_escape(col_spec_value($tablet['spec_2'],TRUE));?>"><?=col_spec_value($tablet['spec_2']);?></p>
						</td>
						<td class="col_spec">
							<p title="<?=html_escape(col_spec_value($tablet['spec_3'],TRUE));?>"><?=col_spec_value($tablet['spec_3']);?></p>
						</td>
						<td class="col_price">
							<p><a href="<?=$url;?>">&euro; <?=price_nice($tablet['price']);?></a></p>
							<a href="#" rel="tooltip" data-placement="top" title="Populariteit: <?=$tablet['popularity'];?>%">
								<div class="progress progress-striped">
									<div class="bar" style="width: <?=$tablet['popularity'];?>%;"></div>
								</div>
							</a>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
		</div>
		<?=$pagination;?>
	</div>
</div>

<script id="productTemplate" type="text/x-jquery-tmpl">
	<tr>
		<td class="col_compare">
			<input type="checkbox" name="compare[]" class="compare_item" value="${product_id}" />
		</td>
		<td class="col_img">
			<a href="${url}" title="${title}" rel="popover" data-img="${img_low}">
				<img src="${img_thumb}" alt="${title}" />
			</a>
		</td>
		<td class="col_name">
			<h2><a href="${url}" title="Tablet">${title}</a></h2>
			<p>${descr_short}</p>
		</td>
		<td class="col_spec">
			<p title="${spec_1_title}">{{html spec_1}}</p>
		</td>
		<td class="col_spec">
			<p title="${spec_2_title}">{{html spec_2}}</p>
		</td>
		<td class="col_spec">
			<p title="${spec_3_title}">{{html spec_3}}</p>
		</td>
		<td class="col_price">
			<p><a href="${url}">&euro; ${price}</a></p>
			<a href="#" rel="tooltip" data-placement="top" title="Populariteit: ${popularity}%">
				<div class="progress progress-striped">
					<div class="bar" style="width: ${popularity}%;"></div>
				</div>
			</a>
		</td>
	</tr>
</script>