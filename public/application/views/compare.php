<div class="well">
	<h1>Tablets vergelijken</h1>
	<p><?=$page_description;?></p>
</div>
<?
$new_specs 		= [];
foreach($specs as $spec)
{
	if(isset($new_specs[$spec['cat']]))
	{
		if(!isset($new_specs[$spec['cat']][$spec['name']])){
			$new_specs[$spec['cat']][$spec['name']] = array();
		}
		$new_specs[ $spec['cat'] ] = array_merge( $new_specs[$spec['cat']] , [ $spec['name'] => ( $new_specs[$spec['cat']][$spec['name']] + [ $spec['product_id'] => $spec['value'] ]) ]);
	}
	else
	{
		$new_specs[ $spec['cat'] ] = [ $spec['name'] => [ $spec['product_id'] => $spec['value'] ] ];
	}
}
?>
<div style="overflow: auto;">
<table class="table table-striped table-bordered table-hover" id="compare-table" style="width: <?=((count($products) * 200) + 150);?>px;">
	<tr>
		<td class="index-colum"></td>
		<? foreach($products as $product): ?>
			<td>
				<a href="<?=site_url().'tablet/'.$product['product_id'].'/'.$product['title_url'].'/';?>">
					<img src="<?=$product['img_thumb'];?>" alt="<?=$product['title']; ?>" class="img-polaroid" />
				</a>
			</td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum"></td>
		<? foreach($products as $product): ?>
			<?
			$remove_url		= explode('-',$this->uri->segment(2));
			if(count($remove_url) == 1){
				$remove_url = site_url().'tablets-vergelijken/';
			} else {
				$remove_url	= array_diff($remove_url,array($product['product_id']));
				$remove_url = site_url().'tablets-vergelijken/'.implode('-',$remove_url).'/';
			}
			?>
			<td>
				<strong>
					<a href="<?=$remove_url;?>" title="Klik om dit product te verwijderen uit de vergelijking" rel="tooltip_clickable"><i class="icon-remove-sign"></i></a> 
					<a href="<?=site_url().'tablet/'.$product['product_id'].'/'.$product['title_url'].'/';?>">
						<?=$product['title']; ?>
					</a>
				</strong>
			</td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Merk</td>
		<? foreach($products as $product): ?>
			<td><a href="<?=site_url().$product['brand_name_url'].'-tablets/';?>"><?=$product['brand_name'];?></a></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Product</td>
		<? foreach($products as $product): ?>
			<td><?=$product['name'];?></td>
		<? endforeach;?>
	</tr>
	
	
	<tr>
		<td class="index-colum">Uitgebracht op</td>
		<? foreach($products as $product): ?>
			<td><?=date('d-m-Y',strtotime($product['release']));?></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Fabrieksgarantie</td>
		<? foreach($products as $product): ?>
			<td><?=$product['warranty'];?></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Populariteit</td>
		<? foreach($products as $product): ?>
			<td>
				<a href="#" rel="tooltip" data-placement="top" title="Populariteit: <?=$product['popularity'];?>%">
					<div class="progress progress-striped">
						<div class="bar" style="width: <?=$product['popularity'];?>%;"></div>
					</div>
				</a>
			</td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Prijs</td>
		<? foreach($products as $product): ?>
			<td>
				<a href="<?=site_url().'tablet/'.$product['product_id'].'/'.$product['title_url'].'/#prijzen';?>">
					&euro; <?=price_nice($product['price']);?>
				</a>
			</td>
		<? endforeach;?>
	</tr>





<? foreach($new_specs as $spec_cat=>$spec_name): ?>
	
	<tr class="info">
		<td class="index-colum"><strong><?=$spec_cat;?></strong></td>
		<? for ($i = 1; $i <= count($products); $i++) { ?>
    		<td></td>
		<? } ?>
	</tr>

	<? foreach($spec_name as $name=>$values): ?>
		<tr<?=((count(array_unique($values)) != 1) ? ' class="success"' : ((count($values) != count($products)) ? ' class="warning"' : '') );?>>
			<td class="index-colum"><?=$name;?></td>
			<? foreach($values as $value): ?>
				<!-- <td><?=value($value);?></td> -->
			<? endforeach; ?>

			<? foreach($products as $product): ?>
				<td><?=(isset($values[$product['product_id']]) ? value($values[$product['product_id']]) : '');?></td>
			<? endforeach; ?>

		</tr>
	<? endforeach; ?>

<? endforeach; ?>




	<tr>
		<td class="index-colum"><strong>Meer informatie</strong></td>
		<? foreach($products as $product): ?>
			<td></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">EAN</td>
		<? foreach($products as $product): ?>
			<td>
				<? foreach(explode(',',$product['ean']) as $ean): ?>
					<?=$ean.'<br />';?>
				<? endforeach;?>
			</td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">SKU</td>
		<? foreach($products as $product): ?>
			<td><?=$product['sku'];?></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Just Tablets ID</td>
		<? foreach($products as $product): ?>
			<td><?=$product['product_id'];?></td>
		<? endforeach;?>
	</tr>
	<tr>
		<td class="index-colum">Informatie fabrikant</td>
		<? foreach($products as $product): ?>
			<td><?=(($product['url']) ? '<a href="'.$product['url'].'" target="_blank">Productinformatie van de fabrikant</a>' : ''); ?></td>
		<? endforeach;?>
	</tr>
</table>
</div>

<strong>Legenda:</strong>
<p class="well well-small"><span class="label label-info">Specificatie categorie</span> <span class="label label-success">Specificatie is anders bij een ander product</span> <span class="label label-warning">Specificatie is mogelijk anders bij een ander product</span> <small>(niet bij alle producten is de betreffende specificatie in dat geval aanwezig)</small></p>