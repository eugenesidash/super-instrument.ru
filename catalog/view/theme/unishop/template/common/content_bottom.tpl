<?php foreach ($modules as $key => $module) { ?>
	<?php if ($route == '' || $route == 'common/home') { ?>
		<?php if ($key < 1) { ?>
			<?php echo $module; ?>
		<?php } ?>
	<?php } else {?>
		<?php echo $module; ?>
	<?php } ?>
<?php } ?>