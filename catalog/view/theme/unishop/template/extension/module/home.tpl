<?php echo $header; ?>
<div class="container">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-4'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-8 col-md-8 col-lg-9 col-sm-push-4'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
		<div class="<?php echo $class; ?>"><?php echo $content_top; ?></div>
	</div>
	<div class="row">
			<?php if(isset($home_banner1) && isset($home_banner2) && isset($home_banner3)) { ?>
			<div class="home_banners">
				<div class="col-md-4"><div><?php echo $home_banner1; ?></div></div>
				<div class="col-md-4"><div><?php echo $home_banner2; ?></div></div>
				<div class="col-md-4"><div><?php echo $home_banner3; ?></div></div>
			</div>
			<?php } ?>
	</div>
	<div class="row">
		<?php echo $column_left2; ?>
		<div <?php if ($column_left2) { ?>class="col-sm-8 col-md-9"<?php } else { ?>class="col-xs-12"<?php } ?>>
			<?php echo $content_bottom; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<?php echo $content_bottom2; ?>
		</div>
	</div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>