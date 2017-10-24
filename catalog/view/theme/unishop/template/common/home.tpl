<?php echo $header; ?>
<div class="container">
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
			<?php $class = 'col-sm-4 col-md-4 col-lg-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
			<?php $class = 'col-sm-12 col-md-8 col-lg-9'; ?>
		<?php } else { ?>
			<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<?php if (in_array('common/home', $menu_schema) && !$column_left && !$column_right) { $class = 'col-sm-8 col-md-8 col-lg-9 col-md-offset-4 col-lg-offset-3'; } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?></div>
		<?php echo $column_right; ?>
	</div>
	<?php if($home_banners[1]['text'] || $home_banners[2]['text'] || $home_banners[3]['text']) { ?>
	<?php if($home_banners[1]['text']) { $banner = 'col-sm-12 col-md-12'; } ?>
	<?php if($home_banners[1]['text'] && $home_banners[2]['text']) { $banner = 'col-sm-6 col-md-6'; }?>
	<?php if($home_banners[1]['text'] && $home_banners[2]['text'] && $home_banners[3]['text']) { $banner = 'col-sm-4 col-md-4'; } ?>
	<div class="row">
		<div class="home_banners">
			<?php foreach($home_banners as $key => $home_banner) { ?>
				<?php if($home_banner['text']) { ?>
					<div class="<?php echo $banner; ?>">
						<div <?php if($home_banner['link']) { ?>class="has_link"<?php } ?> <?php if($home_banner['link'] && !isset($home_banner['link_popup'])) { ?>onclick="location='<?php echo $home_banner['link']; ?>'"<?php } ?><?php if($home_banner['link'] && isset($home_banner['link_popup'])) { ?>onclick="banner_link('<?php echo $home_banner['link']; ?>');"<?php } ?>>
							<?php if($home_banner['icon']) { ?>
								<div><i class="<?php echo $home_banner['icon']; ?>"></i></div>
							<?php } ?>
							<div>
								<strong><?php echo html_entity_decode($home_banner['text'], ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo html_entity_decode($home_banner['text1'], ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<?php echo $column_left2; ?>
		<div <?php if ($column_left2) { ?>class="col-sm-8 col-md-8 col-lg-9"<?php } else { ?>class="col-xs-12"<?php } ?>>
			<?php echo $content_bottom; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<?php echo $content_bottom2; ?>
		</div>
	</div>
</div>
<?php echo $footer; ?>