<div id="search" class="input-group se">
	<input type="hidden" name="filter_category_id" value="" />
	<div class="cat_id input-group-btn">
		<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown"><span><?php echo $lang['text_search_all']; ?></span><i class="fa fa-chevron-down"></i></button>
		<ul class="dropdown-menu">
		<li data-id=""><a><?php echo $lang['text_search_all']; ?></a></li>
		<?php foreach ($categories as $category) { ?>
			<li data-id="<?php echo $category['category_id']; ?>"><a><?php echo $category['name']; ?></a></li>
		<?php } ?>
		</ul>
	</div>		
	<input type="text" name="search" value="<?php echo $search; ?>" placeholder="Для поиска введите название товара или категории" class="form-control input-lg" />
	<span class="input-group-btn">
		<button type="button" class="search btn btn-default btn-lg"><i class="fa fa-search"></i></button>
	</span>
</div>
<!-- <div id="search_phrase">
	<?php if($search_phrase) { ?><?php echo $lang['text_search_phrase']; ?><a><?php echo $search_phrase; ?></a><?php } ?>
</div> -->
<script>
$(document).ready(function() {
	$('#div_search > *').clone().appendTo('#div_search2');
});
</script>