<?php if ($news) { ?>
<div class="row product_carousel">
<h3 class="heading"><span><?php echo $customtitle; ?></span></h3>
	  <div class="news_wrapper">
      <div class="news_module">
        <?php foreach ($news as $news_story) { ?>
          <div class="news">
			<?php if ($news_story['image']) { ?>
              <div class="image">
                <a onclick="location='<?php echo $news_story['href']; ?>'" title="<?php echo $news_story['title']; ?>"><img src="<?php echo $news_story['image']; ?>" class="img-responsive" alt="<?php echo $news_story['title']; ?>" /></a>
              </div>
            <?php } ?>
			<?php if ($show_headline) { ?>
				<div class="name"><a href="<?php echo $news_story['href']; ?>" title=""><?php echo $news_story['title']; ?></a></div>
			<?php } ?>
			<div class="description"><?php echo $news_story['description']; ?></div>
            <div class="posted"><?php echo $news_story['posted']; ?><a onclick="location='<?php echo $news_story['href']; ?>'"> <?php echo $text_more; ?></a></div>
          </div>
        <?php } ?>
        <?php if ($showbutton) { ?>
          <div style="text-align:right;">
            <a href="<?php echo $newslist; ?>" class="button"><span><?php echo $buttonlist; ?></span></a>
          </div>
        <?php } ?>
      </div>
    </div>
</div>
<?php } ?>
<script type="text/javascript">
	$('.news_module').owlCarousel({
		responsiveBaseWidth: '.news_module',
		itemsCustom: [[0, 1], [580, 2], [720, 3], [1000, 4]],
		navigation: true,
		autoPlay: false,
		navigationText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
		pagination: false,
	});
</script>