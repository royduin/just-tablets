
<!-- /content -->
				<? if($this->uri->segment(1) != 'admin'): ?>
				<hr>
					<div class="row-fluid">
						<div class="span3">
							<ul class="unstyled">
								<li><a href="<?=site_url();?>">Home</a></li>
								<li><a href="<?=site_url();?>tablets-vergelijken/">Tablets vergelijken</a></li>
								<li><a href="<?=site_url();?>tablet-kiezen/">Tablet kiezen</a></li>
								<li><a href="<?=site_url();?>tablet-nieuws/">Tablet nieuws</a></li>
								<li><a href="<?=site_url();?>contact/">Contact</a></li>
							</ul>
						</div>
						<div class="span3">
							<ul class="unstyled">
								<? foreach(array_slice($brands, 0, count($brands) / 2) as $brand): ?>
									<li><a href="<?=site_url().$brand['name_url'].'-tablets/';?>"><?=$brand['name'];?> tablets</a></li>
								<? endforeach; ?>
							</ul>
						</div>
						<div class="span3">
							<ul class="unstyled">
								<? foreach(array_slice($brands, count($brands) / 2) as $brand): ?>
									<li><a href="<?=site_url().$brand['name_url'].'-tablets/';?>"><?=$brand['name'];?> tablets</a></li>
								<? endforeach; ?>
							</ul>
						</div>
						<div class="span3">
							<ul class="unstyled">
								<li>Android tablets</li>
								<li>Windows tablets</li>
								<li>iPad</li>
							</ul>
						</div>
					</div>
				<? endif; ?>
				<hr>

				<footer>
					<div class="pull-right">
						<a href="http://www.facebook.com/justtablets" target="_blank" title="Volg Just Tablets op Facebook" class="tooltip_clickable">
							<img src="<?=site_url();?>img/icons/social/facebook.png" alt="Just Tablets op Facebook">
						</a> 
						<a href="http://twitter.com/justtabletsnl" target="_blank" title="Volg Just Tablets op Twitter" class="tooltip_clickable">
							<img src="<?=site_url();?>img/icons/social/twitter.png" alt="Just Tablets op Twitter">
						</a>
						<a href="http://feeds.feedburner.com/JustTablets-Nieuws" target="_blank" title="Volg Just Tablets nieuws via RSS" class="tooltip_clickable" rel="alternate" type="application/rss+xml">
							<img src="<?=site_url();?>img/icons/social/rss.png" alt="Just Tablets nieuws RSS feed">
						</a>
						<a href="http://feeds.feedburner.com/JustTablets-Tablets" target="_blank" title="Volg Just Tablets nieuwe tablets via RSS" class="tooltip_clickable" rel="alternate" type="application/rss+xml">
							<img src="<?=site_url();?>img/icons/social/rss.png" alt="Just Tablets nieuwe tablets RSS feed">
						</a>
					</div>
					<p>&copy; Just Tablets 2012 - <?=date('Y');?> | Pagina snelheid: <?php echo $this->benchmark->elapsed_time();?></p>
				</footer>
				
			</div> <!-- /container -->

		<!-- Javascript -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?=site_url();?>js/vendor/jquery-1.8.2.min.js"><\/script>')</script>

		<script src="<?=site_url();?>js/vendor/bootstrap.min.js"></script>

		<? if($this->uri->segment(1) == 'tablet'): ?>
		<script src="<?=site_url();?>js/vendor/jquery.mousewheel-3.0.6.pack.js"></script>
		<script src="<?=site_url();?>js/vendor/fancybox/jquery.fancybox.pack.2.1.3.js"></script>
		<script src="<?=site_url();?>js/vendor/fancybox/helpers/jquery.fancybox-thumbs.1.0.7.js"></script>
		<? endif; ?>

		<? if($this->uri->segment(1) == 'tablets-vergelijken' OR $this->uri->segment(1) == 'tablet-kiezen'): ?>
		<script src="<?=site_url();?>js/vendor/jquery.tmpl.min.js"></script>
		<script src="<?=site_url();?>js/vendor/jquery-ui-1.9.2.custom.js"></script>
		<script src="<?=site_url();?>js/vendor/selectToUISlider.jQuery.js"></script>
		<? endif; ?>

		<script src="<?=site_url();?>js/main.<?=$this->config->item('version');?>.js"></script>

		<? if($this->uri->segment(1) != 'admin'): ?>
		<script>
			var _gaq=[['_setAccount','XX-XXXXXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>

		<!--
		<script type="text/javascript">
			var clicky_site_ids = clicky_site_ids || [];
			clicky_site_ids.push(100561647);
			(function(){
				var s = document.createElement('script');
				s.type = 'text/javascript';
				s.async = true;
				s.src = '//static.getclicky.com/js';
				( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
			})();
		</script>
		<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100561647ns.gif" /></p></noscript>
		-->
		<? endif; ?>
		
	</body>
</html>
