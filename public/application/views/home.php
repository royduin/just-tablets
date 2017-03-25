<?
$background = [];
$counter_h = -20;
$counter_v = -25;
$query = $this->db->query("SELECT img_thumb FROM tb_product ORDER BY popularity DESC LIMIT 100");
foreach($query->result_array() as $item)
{
	array_push($background,'url('.$item['img_thumb'].') no-repeat '.$counter_h.'px '.$counter_v.'px');
	$counter_h += 75;

	if($counter_h > 1200){
		$counter_h = -20;
		$counter_v += 75;
	}
}
?>


<!-- Don't look at this ugly code! -->
<style type="text/css">.hero-unit:before { background:<?=implode(',',$background);?>; }</style>
<!-- And..... now you may look further! :) -->


<div class="hero-unit">
	<h1>Just Tablets</h1>
	<p>Wij draaien de tablets om! Bekijk en vergelijk eenvoudig alle tablets! Geen idee welke tablet bij u past? Doorloop onze eenvoudige tablet keuze wizard!</p>
	<a href="<?=site_url();?>tablets-vergelijken/" class="btn btn-primary btn-large">Vergelijken &raquo;</a>
	<a href="<?=site_url();?>tablet-kiezen/" class="btn btn-primary btn-large">Keuze wizard &raquo;</a>
</div>

<div class="row">

	<div class="span4">
		<h2>Laatste nieuws</h2>
		<? $url = site_url().'tablet-nieuws/'.$news['news_id'].'/'.$news['title_url'].'/'; ?>
		<h3 class="well well-small"><a href="<?=$url;?>" title="Ga naar de <?=html_escape($news['title']);?> nieuws pagina"><?=$news['title'];?></a></h3>
		<? if($news['image']){ ?>
			<a href="<?=$url;?>" title="Ga naar de <?=html_escape($news['title']);?> nieuws pagina"><img class="media-object img-polaroid img-center" src="<?=$news['image'];?>" alt="<?=html_escape($news['title']);?>"></a>
		<? } ?>
		<blockquote>
			<p><?=$news['description'];?></p>
			<small>Geplaatst op: <?=date('d-m-Y',strtotime($news['date']));?></small>
		</blockquote>
		<p><a class="btn btn-primary" href="<?=$url;?>">Ga naar bericht &raquo;</a> <a class="btn btn-primary" href="<?=site_url();?>tablet-nieuws/">Bekijk alle nieuws items &raquo;</a></p>
	</div>

	<div class="span4">
		<? $url = site_url().'tablet/'.$best_tablet['product_id'].'/'.$best_tablet['title_url'].'/';?>
		<h2>Populairste tablet</h2>
		<h3 class="well well-small"><a href="<?=$url;?>" title="Ga naar de <?=$best_tablet['title'];?> product pagina"><?=$best_tablet['title'];?></a></h3>
		<a href="<?=$url;?>" title="Ga naar de <?=$best_tablet['title'];?> product pagina"><img src="<?=($best_tablet['img_mid'] && @getimagesize($best_tablet['img_mid']) ? $best_tablet['img_mid'] : $best_tablet['img_high']);?>" alt="<?=$best_tablet['title'];?>"></a>
		<div class="price_nice label label-success">&euro;<?=price_nice($best_tablet['price']);?></div>
		<blockquote>
			<p><?=$best_tablet['descr_short'];?></p>
		</blockquote>
		<p><a class="btn btn-primary" href="<?=$url;?>" title="Ga naar de <?=$best_tablet['title'];?> product pagina">Bekijk tablet &raquo;</a> <a class="btn btn-primary" href="<?=site_url().$best_tablet['brand_url'].'-tablets/';?>" title="Ga naar de prijsvergelijker en bekijk alle <?=$best_tablet['brand'];?> tablets">Bekijk alle tablets van <?=$best_tablet['brand'];?> &raquo;</a></p>
   	</div>

	<div class="span4">
		<? $url = site_url().'tablet/'.$review['product_id'].'/'.$review['title_url'].'/#reviews';?>
		<h2>Laatste review</h2>
		<h3 class="well well-small"><a href="<?=$url;?>" title="Ga naar de <?=$review['title'];?> product pagina"><?=$review['title'];?></a></h3>
		<a href="<?=$url;?>" title="Ga naar de <?=$review['title'];?> product pagina"><img src="<?=($review['img_mid'] && @getimagesize($review['img_mid']) ? $review['img_mid'] : $review['img_high']);?>" alt="<?=$review['title'];?>"></a>
		<div class="price_nice label label-success">&euro;<?=price_nice($review['price']);?></div>
		<blockquote>
			<p><?=$review['value'];?></p>
			<small>Geplaatst op: <?=date('d-m-Y',strtotime($review['updated']));?></small>
		</blockquote>
		<p><a class="btn btn-primary" href="<?=$url;?>">Bekijk review &raquo;</a></p>
	</div>

</div>

<p class="well lead">Just Tablets heeft <strong><?=$total;?> verschillende tablets</strong> op de website, van <strong><?=$brands_count;?> merken</strong> waarvan <strong><?=$total_buyable;?> op het moment te koop</strong>. Daarnaast zijn in onze prijsvergelijker <strong><?=$shops;?> (web)winkels</strong> aanwezig met bij elkaar <strong><?=$prices;?> prijzen</strong> en <strong><?=$total_reviews;?> reviews</strong>.</p>