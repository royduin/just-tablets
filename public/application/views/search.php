<h1 class="well">Zoeken<?=($s ? ' naar '.$s : '');?></h1>

<? if(!$s AND !isset($result)): ?>

	<div class="alert">
		Je hebt geen zoekopdracht opgegeven!
	</div>

<? elseif($s AND !count($result)): ?>

	<div class="alert alert-error">
		Er is niets gevonden
	</div>

<? else: ?>
	
	<h2><?=$count;?> tablets gevonden</h2>
	<table class="table table-striped table-hover table-bordered">
		<? foreach($result as $tablet): ?>
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
				<!-- <td class="col_spec">
					<p title="7">7"</p>
				</td>
				<td class="col_spec">
					<p title="32GB">32GB</p>
				</td>
				<td class="col_spec">
					<p title="340g">340g</p>
				</td> -->
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
	</table>
	<a href="<?=site_url().'tablets-vergelijken/';?>" class="btn btn-primary btn-small" id="compare_button" >Vergelijk geselecteerde tablets</a>

<? endif; ?>

<h2>Tag cloud</h2>
<p class="well"><?=$taggly;?></p>