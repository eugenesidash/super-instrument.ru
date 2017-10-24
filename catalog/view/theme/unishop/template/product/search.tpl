<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb <?php if (in_array('product/search', $menu_schema)) { ?>col-md-offset-4 col-lg-offset-3<?php } ?>">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
			<?php $class = 'col-sm-4 col-md-4 col-lg-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
			<?php $class = 'col-sm-8 col-md-8 col-lg-9'; ?>
		<?php } else { ?>
			<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<?php if (in_array('product/search', $menu_schema) && !$column_left && $column_right) { $class = 'col-sm-8 col-md-8 col-lg-6 col-md-offset-4 col-lg-offset-3'; } ?>
		<?php if (in_array('product/search', $menu_schema) && !$column_left && !$column_right) { $class = 'col-sm-8 col-md-8 col-lg-9 col-md-offset-4 col-lg-offset-3'; } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<h1 class="heading"><span><?php echo $heading_title; ?></span></h1>
			<label class="control-label" for="input-search"><?php echo $entry_search; ?></label>
			<div class="row">
				<div class="col-sm-4"><input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_keyword; ?>" id="input-search" class="form-control" /></div>
				<div class="col-sm-3">
					<select name="category_id" class="form-control">
						<option value="0"><?php echo $text_category; ?></option>
						<?php foreach ($categories as $category_1) { ?>
							<?php if ($category_1['category_id'] == $category_id) { ?>
								<option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
							<?php } else { ?>
								<option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
							<?php } ?>
							<?php foreach ($category_1['children'] as $category_2) { ?>
								<?php if ($category_2['category_id'] == $category_id) { ?>
									<option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
								<?php } else { ?>
									<option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
								<?php } ?>
								<?php foreach ($category_2['children'] as $category_3) { ?>
									<?php if ($category_3['category_id'] == $category_id) { ?>
										<option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-3"><label class="checkbox-inline"><input type="checkbox" name="sub_category" value="1" <?php if ($sub_category) { ?>checked="checked"<?php } ?> /><?php echo $text_sub_category; ?></label></div>
			</div>
			<p><label class="checkbox-inline"><input type="checkbox" name="description" value="1" id="description" <?php if ($description) { ?>checked="checked"<?php } ?> /><?php echo $entry_description; ?></label></p>
			<input type="button" value="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary" />
			<hr />
			<h3 class="heading"><span><?php echo $text_search; ?></span></h3>
			<?php if ($products) { ?>
				<p><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></p>
				<div class="row">
					<div class="col-xs-12"><hr /></div>
					<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 hidden-xs">
						<div class="btn-group">
							<?php echo $show_grid_button ? '<button type="button" id="grid-view" class="btn btn-default" data-toggle="tooltip" title="'.$button_grid.'"><i class="fa fa-th"></i></button>' : ''; ?>
							<?php echo $show_list_button ? '<button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title="'.$button_list.'"><i class="fa fa-th-list"></i></button>' : ''; ?>
							<?php echo $show_compact_button ? '<button type="button" id="compact-view" class="btn btn-default" data-toggle="tooltip" title="'.$lang['button_compact'].'"><i class="fa fa-align-justify"></i></button>' : ''; ?>
						</div>
					</div>
					<div class="col-xs-6 col-sm-5 col-md-4 col-lg-4 col-md-offset-2 col-lg-offset-3 text-right">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-sort"></i><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_sort; ?></span></span>
							<select id="input-sort" class="form-control" onchange="location = this.value;">
								<?php foreach ($sorts as $sorts) { ?>
									<?php if ($sorts['value'] == $sort . '-' . $order) { ?>
										<option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 text-right">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-eye"></i><span class="hidden-xs hidden-sm hidden-md"><?php echo $text_limit; ?></span></span>
							<select id="input-limit" class="form-control" onchange="location = this.value;">
								<?php foreach ($limits as $limits) { ?>
									<?php if ($limits['value'] == $limit) { ?>
										<option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-xs-12"><hr /></div>
				</div>
				<div class="row">
					<?php foreach ($products as $product) { ?>
						<div class="product-layout product-grid col-lg-4 col-md-6 col-sm-6 col-xs-12">
							<div class="product-thumb transition">
								<div class="image">
									<?php foreach($product['stickers'] as $sticker) { ?>
										<div class="product_sticker <?php echo $sticker['name']; ?>"><?php echo $sticker['text']; ?> <?php echo $sticker['value']; ?> <?php echo $sticker['text_after']; ?></div>
									<?php } ?>	
									<a href="<?php echo $product['href']; ?>">
										<img src="<?php echo $product['thumb']; ?>" <?php if ($product['quantity'] < 1 && $show_stock_status) { ?>data-status="<?php echo $product['stock_status']; ?>" data-status-id="<?php echo $product['stock_status_id']; ?>"<?php } ?> <?php if($product['additional_image']) { ?>data-additional="<?php echo $product['additional_image'];?>"<?php } ?> alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
									</a>
								</div>
								<div class="caption">
									<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
									<?php if ($show_description || ($show_description_alt && !$product['attribute_groups'])) { ?>
										<p class="description"><?php echo $product['description']; ?></p>
									<?php } ?>
									<?php if ($show_attr) { ?>
										<div class="attribute <?php if ($show_description_alt && !$product['attribute_groups']) { ?>attribute_alt<?php } ?>">
											<?php foreach ($product['attribute_groups'] as $attribute_group) { ?>
												<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
													<?php if ($show_attr_name) { ?><span class="attr_name"><?php echo $attribute['name']; ?>:</span><?php } ?>
													<span class="attr_value"><?php echo $attribute['text']; ?></span>
												<?php } ?>
											<?php } ?>
										</div>
									<?php } ?>
									<div id="option_<?php echo $product['product_id']; ?>" class="option">
									<?php if ($product['options']) { ?>
										<?php foreach ($product['options'] as $option) { ?>
											<?php if ($option['type'] == 'checkbox' || $option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') { ?>
												<div id="input-option<?php echo $option['product_option_id']; ?>">
													<?php if ($option['required']) { ?>*<?php } ?> <label><?php echo $option['name']; ?>:</label><br />
													<?php if ($option['type'] == 'select') { ?>
														<select name="option[<?php echo $option['product_option_id']; ?>]" class="form-control">
															<option value=""><?php echo $text_select; ?></option>
															<?php foreach ($option['product_option_value'] as $option_value) { ?>
																<option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?><?php if ($option_value['price']) { ?>(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?></option>
															<?php } ?>
														</select>
													<?php } ?>
													<?php if ($option['type'] == 'checkbox' || $option['type'] == 'radio' || $option['type'] == 'image') { ?>
														<?php foreach ($option['product_option_value'] as $option_value) { ?>
															<label class="input">
																<?php if ($option['type'] == 'checkbox') { ?>
																	<input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
																<?php } ?>
																<?php if ($option['type'] == 'radio' || $option['type'] == 'image') { ?>
																	<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
																	<?php if($option_value['image']) { ?>
																		<span class="img" title="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" data-toggle="tooltip" data-thumb="<?php echo $option_value['small']; ?>">
																			<img src="<?php echo $option_value['image']; ?>" data-toggle="tooltip" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" />
																		</span>
																	<?php } ?>
																<?php } ?>
																<span><?php echo $option_value['name']; ?><?php if ($option_value['price']) { ?><span>(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)</span><?php } ?></span>
															</label>
														<?php } ?>
													<?php } ?>
												</div>
											<?php } ?>
										<?php } ?>
									<?php } ?>
									</div>
									<?php if ($product['rating'] >= 0 && $show_rating) { ?>
										<div class="rating">
											<?php for ($i = 1; $i <= 5; $i++) { ?>
												<?php if ($product['rating'] < $i) { ?>
													<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
												<?php } else { ?>
													<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i></span>
												<?php } ?>
											<?php } ?>
											<?php if ($show_rating_count) { ?><sup><a onclick="location='<?php echo $product['href']; ?>#tab-review'"><?php echo $product['num_reviews']; ?></a></sup><?php } ?>
										</div>
									<?php } ?>
									<?php if ($product['price']) { ?>
										<p class="price">
											<?php if (!$product['special']) { ?>
												<?php echo $product['price']; ?>
											<?php } else { ?>
												<span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span> 
											<?php } ?>
											<?php if ($product['tax']) { ?><span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span><?php } ?>
										</p>
									<?php } ?>
								</div>
								<div class="cart">
									<button type="button" class="add_to_cart button btn btn-default <?php echo $product['cart_btn_class']; ?> <?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $product['cart_btn_text']; ?>" <?php echo(!$product['cart_btn_disabled'] ? 'onclick="cart.add('.$product['product_id'].');"' : '') ?>><i class="<?php echo $product['cart_btn_icon']; ?> <?php echo($product['cart_btn_icon_mobile'] ? 'visible-sm visible-xs' : ''); ?>"></i><span class="hidden-sm"><?php echo $product['cart_btn_text']; ?></span></button>
									<?php if ($show_quick_order && $show_quick_order_quantity || $show_quick_order && $product['quantity'] > 0) { ?>
										<button type="button" class="quick_order button btn btn-default" data-toggle="tooltip" title="<?php echo $quick_order_title; ?>" onclick="quick_order('<?php echo $product['product_id']; ?>');"><i class="<?php echo $quick_order_icon; ?>"></i><?php echo ($show_quick_order_text ? '<span class="hidden-sm">'.$quick_order_title.'</span>' : '') ?></button>
									<?php } ?>
									<?php if(!$wishlist_btn_disabled) { ?><button type="button" class="wishlist btn btn-default" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button><?php } ?>
									<?php if(!$compare_btn_disabled) { ?><button type="button" class="compare btn btn-default" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button><?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="pagination_wrap row">
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
					<div class="col-sm-6 text-right"><?php echo $results; ?></div>
				</div>
			<?php } else { ?>
				<p><?php echo $text_empty; ?></p>
			<?php } ?>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<script type="text/javascript"><!--
$('#button-search').bind('click', function() {
	url = 'index.php?route=product/search';

	var search = $('#content input[name=\'search\']').prop('value');

	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').prop('value');

	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

	if (sub_category) {
		url += '&sub_category=true';
	}

	var filter_description = $('#content input[name=\'description\']:checked').prop('value');

	if (filter_description) {
		url += '&description=true';
	}

	location = url;
});

$('#content input[name=\'search\']').bind('keydown', function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').on('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').prop('disabled', true);
	} else {
		$('input[name=\'sub_category\']').prop('disabled', false);
	}
});

$('select[name=\'category_id\']').trigger('change');
--></script>
<?php echo $footer; ?>