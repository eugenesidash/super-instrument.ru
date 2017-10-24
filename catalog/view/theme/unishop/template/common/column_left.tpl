<?php if ($modules) { ?>
<aside id="column-left" class="col-sm-4 col-md-4 col-lg-3 hidden-xs <?php if ($route == '' || $route == 'common/home') { ?>hidden-sm<?php } ?>">
<?php foreach ($modules as $key => $module) { ?>
	<?php if ($route == '' || $route == 'common/home') { ?>
		<?php if ($key <= 1) { ?>
			<?php echo $module; ?>
		<?php } ?>
	<?php } else {?>
		<?php echo $module; ?>
	<?php } ?>
<?php } ?>
</aside>
<?php } ?>
