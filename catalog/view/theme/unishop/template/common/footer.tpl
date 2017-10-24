<script>
	$(document).ready(function() {
	<?php if($show_fly_menu) { ?>fly_menu('<?php echo $fly_menu_product; ?>');<?php } ?>
	<?php if($show_fly_cart) { ?>fly_cart();<?php } ?>
	<?php if($show_fly_callback) { ?>fly_callback('<?php echo $fly_callback_text; ?>');<?php } ?>
	change_opt_img('<?php echo $change_opt_img; ?>', '<?php echo $change_opt_img_p; ?>');
	<?php if($show_search) { ?>uni_live_search('<?php echo $show_search_image; ?>', '<?php echo $show_search_description; ?>', '<?php echo $show_search_rating; ?>', '<?php echo $show_search_price; ?>', '<?php echo $search_limit; ?>', '<?php echo $lang['text_live_search_all'];?>', '<?php echo $lang['text_live_search_empty'];?>');<?php } ?>
	});
</script>
<!-- <div class="row">
	<div class="menu_links">
		<a href="" title="Тест1"><i class="fa fa-assistive-listening-systems hidden-md"></i>Тест1</a>
		<a href="" title="Тест2"><i class="fa fa-hourglass-1 hidden-md"></i>Тест2</a>
		<a href="" title="Тест3"><i class="fa fa-music hidden-md"></i>Тест3</a>
	</div>
</div> -->
<div class="clear container"></div>
</div>
<footer>
  <div class="container">
    <div class="row">
      <div class="col-sm-6 col-md-3">
<!--         <h5 class="heading"><i class="<?php echo $column_icon1; ?>"></i><span><?php echo $column_heading1; ?></span></h5> -->
		<div class="ghg">
			<a href="/"><img style="" src="http://super-instrument.ru/image/catalog/img/logo_footer.png" alt=""></a>
		</div>
		<div class="socials">
			<?php if ($socials) { ?>
			<?php foreach ($socials as $social) { ?>
				<a href="<?php echo $social['link']; ?>" target="_blank" title="" ><i class="fa <?php echo $social['icon']; ?>"></i></a>
			<?php } ?>
			<?php } ?>
		</div>
		<p class="cop1">Copyright © <?php echo date('Y'); ?></p>
        <!-- <ul class="list-unstyled">
		<?php if ($informations) { ?>
			<?php foreach ($informations as $information) { ?>
				<li><a href="<?php echo $information['href']; ?>"><i class="fa fa-chevron-right"></i><?php echo $information['title']; ?></a></li>
			<?php } ?>
		<?php } ?>
		<?php if ($footerlinks) { ?>
			<?php foreach($footerlinks as $footerlink) { ?>
				<?php if($footerlink['column'] == 1) { ?>
					<li>
						<?php if($footerlink['link']) { ?><a href="<?php echo $footerlink['link']; ?>"><?php } ?>
							<i class="fa fa-chevron-right"></i><?php echo $footerlink['title']; ?>
						<?php if($footerlink['link']) { ?></a><?php } ?>
					</li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
        </ul> -->
      </div>
      <div class="col-sm-6 col-md-3">
	  <hr class="visible-xs" />
        <h5 class="heading"><i class="<?php echo $column_icon2; ?>"></i><span><?php echo $column_heading2; ?></span></h5>
        <ul class="list-unstyled">
          <?php if ($footerlinks) { ?>
			<?php foreach($footerlinks as $footerlink) { ?>
				<?php if($footerlink['column'] == 2) { ?>
					<li>
						<?php if($footerlink['link']) { ?><a href="<?php echo $footerlink['link']; ?>"><?php } ?>
							<i class="fa fa-chevron-right"></i><?php echo $footerlink['title']; ?>
						<?php if($footerlink['link']) { ?></a><?php } ?>
					</li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
        </ul>
      </div>
	  <div class="clearfix visible-sm"></div>
      <div class="col-sm-6 col-md-3 fkfk">
	  <hr class="visible-xs visible-sm" />
        <h5 class="heading"><i class="<?php echo $column_icon3; ?>"></i><span><?php echo $column_heading3; ?></span></h5>
        <ul class="list-unstyled">
          <?php if ($footerlinks) { ?>
			<?php foreach($footerlinks as $footerlink) { ?>
				<?php if($footerlink['column'] == 3) { ?>
					<li>
						<?php if($footerlink['link']) { ?><a href="<?php echo $footerlink['link']; ?>"><?php } ?>
							<i class="fa fa-chevron-right"></i><?php echo $footerlink['title']; ?>
						<?php if($footerlink['link']) { ?></a><?php } ?>
					</li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
        </ul>
	<h5 class="heading" style="margin-top: 25px;"><span>Принимаем к оплате</span></h5>
      <div class="payments">
		<?php if ($payment_icons) { ?>
		<?php foreach ($payment_icons as $payment_icon) { ?>
			<img src="<?php echo HTTPS_SERVER; ?>image/payment/<?php echo $payment_icon; ?>.png" alt="<?php echo $payment_icon; ?>" />
		<?php } ?>
		<?php } ?>
	</div>
		<?php if($footer_text) { ?><div class="text"><?php echo $footer_text; ?></div><?php } ?>
      </div>
      <div class="col-sm-6 col-md-3">
	  <hr class="visible-xs visible-sm" />
        <h5 class="heading"><i class="<?php echo $column_icon4; ?>"></i><span><?php echo $column_heading4; ?></span></h5>
        <ul class="list-unstyled list-unstyled2">
          <?php if ($footerlinks) { ?>
			<?php foreach($footerlinks as $footerlink) { ?>
				<?php if($footerlink['column'] == 4) { ?>
					<li>
						<?php if($footerlink['link']) { ?><a href="<?php echo $footerlink['link']; ?>"><?php } ?>
							<i class="fa fa-chevron-right"></i><?php echo $footerlink['title']; ?>
						<?php if($footerlink['link']) { ?></a><?php } ?>
					</li>
				<?php } ?>
			<?php } ?>
		<?php } ?>
        </ul>
		<?php if($footer_map) { ?><div class="ya_maps"><?php echo $footer_map; ?></div><?php } ?>
      </div>
    </div>
	<hr />
	<div class="row">
		<div class="col-sm-12 col-md-6">
<!-- 		<div class="socials">
			<?php if ($socials) { ?>
			<?php foreach ($socials as $social) { ?>
				<a href="<?php echo $social['link']; ?>" target="_blank" title="" ><i class="fa <?php echo $social['icon']; ?>"></i></a>
			<?php } ?>
			<?php } ?>
		</div> -->
		</div>
		<div class="col-sm-12  col-md-6">
		<hr class="visible-xs visible-sm" />
<!-- 		<div class="payments">
			<?php if ($payment_icons) { ?>
			<?php foreach ($payment_icons as $payment_icon) { ?>
				<img src="<?php echo HTTPS_SERVER; ?>image/payment/<?php echo $payment_icon; ?>.png" alt="<?php echo $payment_icon; ?>" />
			<?php } ?>
			<?php } ?>
		</div> -->
		</div>
	</div>
  </div>
</footer>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
</body></html>