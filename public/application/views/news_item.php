<h1 class="well"><?=$title;?></h1>
	<img class="media-object img-polaroid img-center" src="<?=($image ?: site_url().'apple-touch-icon-114x114-precomposed.png');?>" alt="<?=html_escape($title);?>">
	<p class="well"><?=$description;?></p>
<p class="well">
	<a href="<?=$link;?>" class="btn btn-primary pull-right" target="_blank">Lees verder</a>
	<small>Geplaatst op <?=date('H:i d-m-Y', strtotime($date));?> door <?=$supplier;?></small>
</p>