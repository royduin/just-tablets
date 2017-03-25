<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="nl" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="nl" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="nl" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="nl" class="no-js"> <!--<![endif]-->
	<head prefix="og: http://ogp.me/ns#">
		<meta charset="utf-8">
		<title><?=(isset($page_title)) ? html_escape($page_title) : 'Just Tablets - Bekijk en vergelijk eenvoudig alle tablets!' ?></title>
		<meta name="description" content="<?=(isset($page_description)) ? html_escape($page_description) : 'Just Tablets - Wij draaien de tablets om! Bekijk en vergelijk eenvoudig alle tablets! Geen idee welke tablet bij u past? Doorloop onze eenvoudige tablet keuze wizard!' ?>">
		<meta name="viewport" content="width=device-width">
		
<? if($this->uri->segment(1) == 'tablet'): ?>
		<meta property="og:title" content="<?=html_escape($title);?>" />
		<meta property="og:description" content="<?=html_escape($descr_short);?>" />
		<meta property="og:locale" content="nl_NL" />
		<meta property="og:type" content="product" />
		<meta property="og:url" content="<?=current_url();?>" />
		<meta property="og:image" content="<?=$img_high;?>" />
		<meta property="og:site_name" content="JustTablets.nl" />
<? endif; ?>

		<link rel="sitemap" type="application/xml" title="Sitemap" href="<?=site_url();?>sitemap.xml" />
			
<? if($this->uri->segment(1) == 'contact'): ?>
		<meta name="robots" content="noindex">
<? endif;?>

<? if(isset($page_canonical)): ?>
		<link rel="canonical" href="<?=$page_canonical;?>" />
<? endif; ?>

<? if(isset($page_prev)): ?>
		<link rel="prev" href="<?=$page_prev;?>" />
<? endif; ?>

<? if(isset($page_next)): ?>
		<link rel="next" href="<?=$page_next;?>" />
<? endif; ?>

		<link rel="stylesheet" href="<?=site_url();?>css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=site_url();?>css/bootstrap-responsive.min.css">

		<? if($this->uri->segment(1) == 'tablet'): ?>
<link rel="stylesheet" href="<?=site_url();?>js/vendor/fancybox/jquery.fancybox.2.1.3.css">
		<link rel="stylesheet" href="<?=site_url();?>js/vendor/fancybox/helpers/jquery.fancybox-thumbs.1.0.7.css">
		<? endif; ?>

		<? if($this->uri->segment(1) == 'tablets-vergelijken' OR $this->uri->segment(1) == 'tablet-kiezen'): ?>
<link rel="stylesheet" href="<?=site_url();?>css/start/jquery-ui-1.9.2.custom.css">
		<link rel="stylesheet" href="<?=site_url();?>css/ui.slider.extras.css">
		<? endif; ?>

		<link rel="stylesheet" href="<?=site_url();?>css/main.<?=$this->config->item('version');?>.css">

		<script src="<?=site_url();?>js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
	</head>
	
	<? echo ($this->uri->segment(1) == 'tablet') ? '<body data-spy="scroll" data-target=".bs-docs-sidebar">' : '<body>' ?>
	
		<!--[if lt IE 7]>
			<div class="alert alert-error">
				<p class="chromeframe">Je gebruikt een verouderde browser! <a href="http://browsehappy.com/">Upgrade je browser nu</a> of <a href="http://www.google.com/chromeframe/?redirect=true">installeer Google Chrome Frame</a>.</p>
			</div>
		<![endif]-->

		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="<?=site_url();?>">Just Tablets</a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li<?=(!$this->uri->segment(1)) ? ' class="active"' : '' ?>><a href="<?=site_url();?>">Home</a></li>
							<li<?=($this->uri->segment(1) == 'tablets-vergelijken') ? ' class="active"' : '' ?>><a href="<?=site_url();?>tablets-vergelijken/">Vergelijken</a></li>
							<li<?=($this->uri->segment(1) == 'tablet-kiezen') ? ' class="active"' : '' ?>><a href="<?=site_url();?>tablet-kiezen/">Kiezen</a></li>
							<li<?=($this->uri->segment(1) == 'tablet-nieuws') ? ' class="active"' : '' ?>><a href="<?=site_url();?>tablet-nieuws/">Nieuws</a></li>
							<li<?=($this->uri->segment(1) == 'contact') ? ' class="active"' : '' ?>><a href="<?=site_url();?>contact/">Contact</a></li>
							<? if(in_array($this->input->ip_address(),$this->config->item('admins'))): ?>
								<li<?=($this->uri->segment(1) == 'admin') ? ' class="active"' : '' ?>><a href="<?=site_url();?>admin/">Admin</a></li>
								<li><a href="<?=site_url();?>cron/">Cronjobs</a></li>
								<li><a href="<?=site_url();?>admin/clear_cache/">Clear cache</a></li>
							<? endif; ?>
						</ul>
						<?=form_open('zoeken/',array('class' => 'navbar-search pull-right'));?>
							<input type="text" name="s" class="search-query" placeholder="Zoeken...">
						</form>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<div class="container">

<!-- content -->