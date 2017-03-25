<div class="well">
	<h1>Tablet kiezen</h1>
	<p><?=$page_description;?></p>
</div>

<?=form_open('',array('class' => 'form-inline'));?>

<input type="checkbox" name="buyable" id="buyable" value="buyable" checked="checked" class="hide">

<div class="accordion" id="wizard">

	<div class="accordion-group">
		<div class="accordion-heading">
			<h2><a class="accordion-toggle" data-toggle="collapse" data-parent="#wizard" href="#price">Prijs</a></h2>
		</div>
		<div id="price" class="accordion-body collapse in">
			<div class="accordion-inner">
				<p>In het geval dat je een budget hebt geef dan hieronder aan tussen welke bedragen je op zoek bent naar een tablet.</p>
				<hr>
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
		</div>
	</div>

	<div class="accordion-group">
		<div class="accordion-heading">
			<h2><a class="accordion-toggle" data-toggle="collapse" data-parent="#wizard" href="#brand">Merk</a></h2>
		</div>
		<div id="brand" class="accordion-body collapse">
			<div class="accordion-inner">
				<p>Voorkeur voor een merk?</p>
				<hr>
				<ul class="unstyled inline">
				<? foreach($brands as $key=>$brand): ?>
					<li>
						<label for="b<?=$brand['brand_id'];?>" class="checkbox">
							<input type="checkbox" name="brands" id="b<?=$brand['brand_id'];?>" value="<?=$brand['brand_id'];?>">
							<?=$brand['name'];?>
							<span class="filter_count">(<?=$brand['brand_count'];?>)</span>
						</label>
					</li>
				<? endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

<? foreach($filters as $key=>$filter): ?>
	<? if($filter['default']): ?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<h2><a class="accordion-toggle" data-toggle="collapse" data-parent="#wizard" href="#<?=$filter['feature_id'];?>"><?=$filter['name'];?></a></h2>
			</div>
			<div id="<?=$filter['feature_id'];?>" class="accordion-body collapse">
				<div class="accordion-inner">
					<p>Mocht je een voorkeur hebben geef dit dan hieronder aan.</p>
					<hr>
					<? if($filter['filter'] == 1): ?>
						<ul class="unstyled inline">
						<? foreach($filter['values'] as $key=>$item): ?>
							<li>
								<label for="f<?=$filter['feature_id'];?>_<?=$item['value_id'];?>" class="checkbox">
									<input type="checkbox" name="f<?=$filter['feature_id'];?>" id="f<?=$filter['feature_id'];?>_<?=$item['value_id'];?>" value="<?=$item['value_id'];?>">
									<?=value($item['value_name'],TRUE);?> <small><?=(strlen($item['sign_name']) <= 2 ? strtoupper($item['sign_name']) : strtolower($item['sign_name']));?></small>
									<span class="filter_count"></span>
								</label>
							</li>
						<? endforeach; ?>
						</ul>
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
		</div>
	<? endif; ?>

<? endforeach; ?>

<a href="<?=site_url();?>tablets-vergelijken/" class="btn btn-large btn-primary btn-block" id="gowizard">Bekijk resultaat</a>
</form>