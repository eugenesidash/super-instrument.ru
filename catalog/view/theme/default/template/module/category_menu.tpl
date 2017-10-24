<?php if ($categories) { ?>
<div id="category_menu">
  <ul>
	<?php foreach ($categories as $category) { ?>
		<?php if ($category['image']) { ?>
			<li><a href="<?php echo $category['href']; ?>"><img src="<?php echo $category['image']; ?>"><span><?php echo $category['name']; ?></span></a>
		<?php }else{ ?>
			<li><a href="<?php echo $category['href']; ?>"><span><?php echo $category['name']; ?></span></a>
		<?php } ?>
		<?php if ($category['children']) { ?>
			<div>
			<?php for ($i = 0; $i < count($category['children']);) { ?>
				<ul>
				<?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
				<?php for (; $i < $j; $i++) { ?>
					<?php if (isset($category['children'][$i])) { ?>
						<?php if ($category['children'][$i]['image']) { ?>
							<li><a href="<?php echo $category['children'][$i]['href']; ?>"><img src="<?php echo $category['children'][$i]['image']; ?>"><span><?php echo $category['children'][$i]['name']; ?></span></a>
						<?php }else{ ?>
							<li><a href="<?php echo $category['children'][$i]['href']; ?>"><span><?php echo $category['children'][$i]['name']; ?></span></a>
						<?php } ?>

						<?php if ($category['children'][$i]['subchildren']) { ?>
							<div>
							<?php for ($l = 0; $l < count($category['children'][$i]['subchildren']);) { ?>
								<ul>
								<?php $m = $l + ceil(count($category['children'][$i]['subchildren']) / $category['children'][$i]['column']); ?>
								<?php for (; $l < $m; $l++) { ?>
									<?php if (isset($category['children'][$i]['subchildren'][$l])) { ?>
										<?php if ($category['children'][$i]['subchildren'][$l]['image']) { ?>
											<li><a href="<?php echo $category['children'][$i]['subchildren'][$l]['href']; ?>"><img src="<?php echo $category['children'][$i]['subchildren'][$l]['image']; ?>"><span><?php echo $category['children'][$i]['subchildren'][$l]['name']; ?></span></a>
										<?php }else{ ?>
											<li><a href="<?php echo $category['children'][$i]['subchildren'][$l]['href']; ?>"><span><?php echo $category['children'][$i]['subchildren'][$l]['name']; ?></span></a>
										<?php } ?>
									<?php } ?>
								<?php } ?>
								</ul>
							<?php } ?>
						</div>
						<?php } ?>
							</li>
					<?php } ?>
				<?php } ?>
				</ul>
			<?php } ?>
			</div>
		<?php } ?>
	</li>
	<?php } ?>
	</ul>
</div>
<?php } ?>