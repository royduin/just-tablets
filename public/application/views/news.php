<div class="well">
	<h1>Tablet nieuws</h1>
	<p><?=$page_description;?></p>
</div>
<ul class="media-list well news">
	<? foreach($items as $item){ ?>
		<? $url = site_url().'tablet-nieuws/'.$item['news_id'].'/'.$item['title_url'].'/'; ?>
		<li class="media">
			<img class="media-object pull-right img-polaroid" src="<?=($item['image'] ?: site_url().'apple-touch-icon-114x114-precomposed.png');?>" alt="<?=html_escape($item['title']);?>">
			<div class="media-body">
				<h2 class="media-heading"><a href="<?=$url;?>" title="Ga naar bericht"><?=$item['title'];?></a></h2>
				<p><?=$item['description'];?></p>
				<small>Geplaatst op <?=date('H:i d-m-Y', strtotime($item['date']));?> door <?=$item['supplier'];?></small>
			</div>
		</li>
	<? } ?>
</ul>
<?=$pagination;?>