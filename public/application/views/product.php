<div itemscope itemtype="http://data-vocabulary.org/Product">
<div class="well">
	<h1 itemprop="name"><?=$title;?></h1>
	<p itemprop="description"><?=$page_description;?></p>
</div>
<div class="row">
	<div class="span3 bs-docs-sidebar">
		<div class="well thumb">
			<a href="<?=$img_high;?>" title="<?=$title;?>" class="fancybox" rel="images">
				<img itemprop="image" src="<?=($img_low) ? $img_low : $img_thumb;?>" alt="<?=$title;?>" />
			</a>
		</div>
		<ul class="nav nav-list bs-docs-sidenav" data-spy="affix" data-offset-top="400">
			<li><a href="#overzicht"><i class="icon-chevron-right"></i> Overzicht</a></li>
			<li><a href="#afbeeldingen"><i class="icon-chevron-right"></i> Afbeeldingen</a></li>
			<li><a href="#omschrijving"><i class="icon-chevron-right"></i> Omschrijving</a></li>
			<li><a href="#specificaties"><i class="icon-chevron-right"></i> Specificaties</a></li>
			<li><a href="#media"><i class="icon-chevron-right"></i> Media</a></li>
			<li><a href="#prijzen"><i class="icon-chevron-right"></i> Prijzen</a></li>
			<li><a href="#reviews"><i class="icon-chevron-right"></i> Reviews</a></li>
		</ul>
	</div>
	<div class="span9">
		
		<section id="overzicht">
			<h2>Overzicht</h2>
			<table class="table table-striped table-bordered table-hover">
				<? if(in_array($this->input->ip_address(),$this->config->item('admins'))): ?>
				<tr>
					<th class="span3">Icecat ID</th>
					<td class="span9"><?=$product_iid;?></td>
				</tr>
				<? endif; ?>
				<tr>
					<th class="span3">Merk</th>
					<td class="span9"><a href="<?=site_url();?><?=$name_url;?>-tablets/" title="Bekijk alle tablets van <?=$name;?>" itemprop="brand"><?=$name;?></a></td>
				</tr>
				<? if(isset($ean[0])){ ?>
				<tr>
					<th class="span3">EAN</th>
					<td class="span9">
						<?
						$ean = explode(',',$ean);
						if(count($ean) == 1){
							echo '<span itemprop="identifier" content="upc:<?=$ean[0];?>">'.$ean[0].'</span>';
							echo (in_array($this->input->ip_address(),$this->config->item('admins')) ? ' <a href="http://icecat.nl/index.cgi?language=en&new_search=1&lookup_text='.$ean[0].'"> <i class="icon-search"></i> Icecat</a>' : '');
						} else {
							echo '<ul class="ean">';
							foreach($ean as $e){
								echo '<li itemprop="identifier" content="upc:<?=$e;?>">';
								echo $e;
								echo (in_array($this->input->ip_address(),$this->config->item('admins')) ? ' <a href="http://icecat.nl/index.cgi?language=en&new_search=1&lookup_text='.$e.'"> <i class="icon-search"></i> Icecat</a>' : '');
								echo '</li>';
							}
							echo '</ul>';
						}
						?>
					</td>
				</tr>
				<? } ?>
				<tr>
					<th class="span3">SKU</th>
					<td class="span9" itemprop="identifier" content="sku:<?=$sku;?>">
						<?=$sku;?>
						<?=(in_array($this->input->ip_address(),$this->config->item('admins')) ? ' <a href="http://icecat.nl/index.cgi?language=en&new_search=1&lookup_text='.$sku.'"> <i class="icon-search"></i> Icecat</a>' : '');?>
					</td>
				</tr>
				<tr>
					<th class="span3">Uitgebracht op</th>
					<td class="span9"><?=date('d-m-Y',strtotime($release));?></td>
				</tr>
				<? if($url){ ?>
				<tr>
					<th class="span3">Informatie fabrikant</th>
					<td class="span9"><a href="<?=$url;?>" target="_blank" title="Naar de website van de fabrikant"><?=$url;?></a></td>
				</tr>
				<? } ?>
				<? if($warranty){ ?>
				<tr>
					<th class="span3">Fabrieks garantie</th>
					<td class="span9"><?=$warranty;?></td>
				</tr>
				<? } ?>
			</table>
		</section>
		
		<section id="afbeeldingen">
			<h2>Afbeeldingen</h2>
			<? if(!isset($images_thumb[0])){ ?>
				<div class="alert alert-info">
					Er zijn op het moment geen meerdere afbeeldingen beschikbaar voor deze tablet.
				</div>
			<? } else { ?>
				<?
				$images_thumb 	= explode(',',$images_thumb);
				$images_high 	= explode(',',$images_high);
				?>
				<ul class="thumbnails">
					<? foreach($images_thumb as $key=>$image){ ?>
						<li>
							<a href="<?=$images_high[$key];?>" title="<?=$title;?>" class="thumbnail fancybox" rel="images">
								<img src="<?=$image;?>" alt="">
							</a>
						</li>
					<? } ?>
				</ul>
			<? } ?>
		</section>
		
		<section id="omschrijving">
			<h2>Omschrijving</h2>
			<? if($descr_long){ ?>
			<p class="well"><?=$descr_long;?></p>
			<? } else { ?>
				<div class="alert alert-info">
					Er is nog geen omschrijving beschikbaar voor deze tablet.
				</div>
			<? } ?>
		</section>
		
		<section id="specificaties">
			<h2>Specificaties</h2>
			<? if($specs){ ?>
				<? $cat = FALSE; ?>
				<? foreach($specs as $spec){ ?>
					<? if($cat != $spec['cat']){ ?>
						<? if($cat){ ?>
							</table>
						<? } ?>
						<h3><?=$spec['cat'];?></h3>
						<table class="table table-striped table-bordered table-hover">
					<? } ?>
					<tr>
						<th class="span6"><?=$spec['name'];?></th>
						<td class="span6"><?=value($spec['value']);?></td>
					</tr>
					<? $cat = $spec['cat']; ?>
				<? } ?>
				</table>
			<? } else { ?>
				<div class="alert alert-info">
					Er zijn nog geen specificaties beschikbaar voor deze tablet.
				</div>
			<? } ?>
			
			<? if($features){ ?>
				<h3>Specificatie logo's</h3>
				<ul class="media-list features well well-small">
					<? foreach($features as $feature){ ?>
						<li class="media">
							<img class="pull-left" src="<?=$feature['image'];?>" alt="" />
							<div class="media-body">
								<p><?=$feature['descr'];?></p>
							</div>
						</li>
					<? } ?>
				</ul>
			<? } ?>
			
		</section>
		
		<section id="media">
			<h2>Media</h2>
			<? if($pdf_spec OR $pdf_manual){ ?>
			<h3>PDF</h3>
			<table class="table table-striped table-bordered table-hover">
				<? if($pdf_spec){ ?>
				<tr>
					<th class="span6">Specificaties fabrikant (PDF)</th>
					<td class="span6"><a href="<?=$pdf_spec;?>" target="_blank"><?=$pdf_spec;?></a></td>
				</tr>
				<? } ?>
				<? if($pdf_manual){ ?>
				<tr>
					<th class="span6">Handleiding (PDF)</th>
					<td class="span6"><a href="<?=$pdf_manual;?>" target="_blank"><?=$pdf_manual;?></a></td>
				</tr>
				<? } ?>
			</table>
			<? } ?>
			<h3>Videos</h3>
			<p class="well">
				<iframe width="100%" height="315" src="http://www.youtube.com/embed?listType=search&list=<?=urlencode($title);?>&color=white&loop=1&modestbranding&showinfo=1&theme=light" frameborder="0" allowfullscreen></iframe>
			</p>
		</section>
		
		<section id="prijzen">
			<h2>Prijzen</h2>
			<? if(empty($prices)){ ?>
				<div class="alert alert-info">
					Er zijn voor deze tablet (nog) geen prijzen (meer) beschikbaar.
				</div>
			<? } else { ?>
				<table class="table table-striped table-bordered table-hover">
					<tr>
						<th class="span3">Shop</th>
						<th class="span2">Plaats</th>
						<th class="span3">Levertijd</th>
						<th class="span2">Prijs</th>
						<th class="span2">Prijs incl.</th>
						<th class="span1"></th>
					</tr>
					<? foreach($prices as $price){ ?>
						<tr>
							<td><?=$price['name'];?></td>
							<td>
								<? if(strstr($price['city'],',')){ ?>
									<? $count = count(explode(',',$price['city'])); ?>
									<a href="#" rel="tooltip" data-placement="top" title="<?=str_replace(',',', ',$price['city']);?>"><?=$count;?> winkels</a>
								<? } else { ?>
									<?=$price['city'];?>
								<? } ?>
							</td>
							<td>
								<a href="#" rel="tooltip" data-placement="top" title="<?=str_replace('"','',$price['stock_text']);?>">
								<? if($price['stock_store'] AND $price['stock_store'] != 'Nee' AND $price['stock_store'] != '0'){ ?>
									<span class="badge badge-success">Op voorraad</span>
								<? } elseif($price['stock_supplier'] AND $price['stock_supplier'] != 'Nee' AND $price['stock_supplier'] != '0'){ ?>
									<span class="badge badge-warning">Op voorraad bij toeleverancier</span>
								<? } else { ?>
									<span class="badge badge-important">Niet op voorraad</span>
								<? } ?>
								</a>
							</td>
							<td>&euro; <?=price_nice($price['price']);?></td>
							<td>&euro; <?=price_nice($price['price'] + $price['shipping']);?></td>
							<td><a href="<?=$price['url'];?>" target="_blank" class="btn btn-primary btn-mini"><i class="icon-chevron-right icon-white"></i></a></td>
						</tr>
					<? } ?>
				</table>
			<? } ?>
		</section>
		
		<section id="reviews">
			<h2>Reviews</h2>
			
			<? if(!count($reviews)): ?>

				<div class="alert alert-info">
					Er zijn voor deze tablet (nog) geen reviews beschikbaar.
				</div>

			<? else: ?>

				<ul class="media-list">
					<? foreach($reviews as $review): ?>
						<li class="media">
							<div class="media-body">
								<h3 class="media-heading"><a href="<?=$review['url'];?>" target="_blank"<?=($review['logo'] ? ' rel="popover" data-img="'.$review['logo'].'"' : '');?> title="<?=ucfirst($review['supplier']);?>"><?=ucfirst($review['supplier']);?></a></h3>
								<blockquote>
									<p><?=$review['value'];?></p>
									<small>Geplaatst op: <?=date('d-m-Y',strtotime($review['updated']));?></small>
								</blockquote>
								<? if($review['value_good']): ?>
									<p><span class="badge badge-success"><i class="icon-thumbs-up"></i> <?=$review['value_good'];?></span></p>
								<? endif; ?>
								<? if($review['value_bad']): ?>
									<p><span class="badge badge-important"><i class="icon-thumbs-down"></i> <?=$review['value_bad'];?></span></p>
								<? endif; ?>
							</div>
							<? if($review['score']): ?>
								<p class="pull-right">
									<span class="label label-success">Score: <?=$review['score']; ?> / 100</span>
								</p>
							<? endif; ?>
						</li>
					<? endforeach; ?>
				</ul>

			<? endif; ?>

		</section>
		
	</div>
</div>
</div>