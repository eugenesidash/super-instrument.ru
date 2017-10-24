<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>

<?php if ($noindex) { ?>
<!-- OCFilter Start -->
<meta name="robots" content="noindex,nofollow" />
<!-- OCFilter End -->
<?php } ?>
      
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<base href="<?php echo $base; ?>" />
<?php if ($show_meta_robots) { ?>
<meta name="robots" content="<?php echo isset($_GET['page']) || isset($_GET['limit']) || isset($_GET['sort']) ? 'noindex, follow' : 'index, follow'; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<meta property="og:title" content="<?php echo $title; ?>" />
<meta property="og:type" content="website" />
<?php if (isset($og_url)) { ?>
<meta property="og:url" content="<?php echo $og_url; ?>" />
<?php } ?>
<?php if (isset($og_image)) { ?>
<meta property="og:image" content="<?php echo $og_image; ?>" />
<?php } else { ?>
<meta property="og:image" content="<?php echo $logo; ?>" />
<?php } ?>
<meta property="og:site_name" content="<?php echo $name; ?>" />
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link href="catalog/view/theme/unishop/stylesheet/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="catalog/view/theme/unishop/stylesheet/stylesheet.css?v=2.1" rel="stylesheet" type="text/css" media="screen" />
<link href="catalog/view/theme/unishop/stylesheet/font-awesome.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="catalog/view/theme/unishop/stylesheet/elements_<?php echo $store_id; ?>.css" rel="stylesheet" type="text/css" media="screen" />
<?php if ($background_image) { ?>
<link href="catalog/view/theme/unishop/stylesheet/background.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<?php if ($custom_style) { ?>
<link href="catalog/view/theme/unishop/stylesheet/<?php echo $custom_style; ?>" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<?php if ($user_css) { ?>
<style><?php echo $user_css; ?></style>
<?php } ?>
<link href="catalog/view/theme/default/stylesheet/geoip.css" rel="stylesheet">
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script>if(!localStorage.getItem('display')) {localStorage.setItem('display', '<?php echo $default_view; ?>');}</script>
<script src="catalog/view/theme/unishop/js/common.js" type="text/javascript"></script>
<?php if ($user_js) { ?>
<script><?php echo $user_js; ?></script>
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php if (in_array($route, $menu_schema) || (!$route && in_array('common/home', $menu_schema))) { ?>
<?php
	$one_line = 0;
	$two_line = 0;
	foreach($categories as $category) { 
		if(utf8_strlen($category['name']) <= 30) {
			++$one_line;
		} else {
			++$two_line;
		}
	}
?>
<style type="text/css">
	@media (min-width:992px){
	<?php if ($route == 'common/home' || (!$route && in_array('common/home', $menu_schema))) { ?>
		#column-left {margin-top:<?php echo ($one_line*48)+($two_line*68)+1 ?>px} 
	<?php } else { ?>
		#column-left {margin-top:<?php echo ($one_line*48)+($two_line*68)-42 ?>px} 
	<?php } ?>
		#menu {border-radius:4px 4px 0 0} 
		#menu.menu2 .navbar-collapse {display:block !important}
		#menu.menu2 img { text-align:center;vertical-align:middle;padding-right:10px;width:34px; }
	}
</style>
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery.geoip-module.js" type="text/javascript"></script>
</head>
<body class="<?php echo $class; ?>">
<nav id="top">
  <div class="container">
<!-- 	<div class="pull-right">
	<div id="account" class="btn-group">
		<button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
			<i class="fa fa-user" aria-hidden="true"></i> 
			<span class="hidden-xs"><?php if ($logged) { ?><?php echo $customer_name; ?><?php } else { ?><?php echo $text_account; ?><?php } ?></span> 
			<i class="fa fa-caret-down"></i>
		</button>
        <ul class="dropdown-menu dropdown-menu-right">
            <?php if ($logged) { ?>
            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a <?php if($show_register) { ?>onclick="register();"<?php } else { ?>href="<?php echo $register; ?>"<?php } ?> ><?php echo $text_register; ?></a></li>
            <li><a <?php if($show_login) { ?>onclick="login();"<?php } else { ?>href="<?php echo $login; ?>"<?php } ?>><?php echo $text_login; ?></a></li>
            <?php } ?>
        </ul>
    </div>
	</div>
	<?php echo $language; ?>
	<?php echo $currency; ?>
	<?php if($headerlinks) { ?> -->
		<div id="top-links" class="hidden-xs hidden-sm">
			<ul>
			<?php foreach ($headerlinks as $headerlink) { ?>
				<li><a href="<?php echo $headerlink['link']; ?>" title="<?php echo $headerlink['title']; ?>"><?php echo $headerlink['title']; ?></a></li>
			<?php } ?>
			</ul>
		</div>
		<div class="socials">
			<a href="https://vk.com/superinstrumentspb" target="_blank" title=""><i class="fa fa-vk"></i></a>
			<a href="https://www.instagram.com/" target="_blank" title=""><i class="fa fa-instagram"></i></a>
			<a href="https://twitter.com/" target="_blank" title=""><i class="fa fa-twitter"></i></a>
			<a href="https://plus.google.com/" target="_blank" title=""><i class="fa fa-google-plus"></i></a>
			<a href="https://ok.ru/" target="_blank" title=""><i class="fa fa-odnoklassniki"></i></a>
			<a href="https://www.facebook.com/" target="_blank" title=""><i class="fa fa-facebook"></i></a>
		</div>
		<div class="btn-group pull-left visible-xs visible-sm">
			<button class="btn btn-link dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info" aria-hidden="true"></i> <i class="fa fa-caret-down"></i></button>
			<ul class="dropdown-menu dropdown-menu-left">
			<?php foreach ($headerlinks as $headerlink) { ?>
				<li><a href="<?php echo $headerlink['link']; ?>" title="<?php echo $headerlink['title']; ?>"><?php echo $headerlink['title']; ?></a></li>
			<?php } ?>
			</ul>
		</div>
	<?php } ?>
  </div>
</nav>
<header>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div id="logo1">
					<?php if ($logo) { ?>
						<?php if (isset($og_url) && $home == $og_url) { ?>
							<img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" />
						<?php } else { ?>
							<a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" /></a>
						<?php } ?>
					<?php } else { ?>
						<h1><a href="<?php echo $home; ?>"><span><?php echo html_entity_decode($name, ENT_QUOTES, 'UTF-8'); ?></span></a></h1>
					<?php } ?>
				</div>
			</div>
			<div class="col-xs-9 col-sm-4 col-md-3 col-md-push-5">
				<div class="pochta">
					<p>Почта для заявок</p>
					sale@super-instrument.ru
					<div class="region1">
						<?php echo $geoip; ?>
					</div>
				</div>
			</div>
			<div class="col-xs-3 col-sm-2 col-md-1 col-md-push-5"><?php echo $cart; ?></div>
			<div class="col-xs-12 col-sm-6 col-md-<?php if($menu_type == 1) { ?>5<?php } else { ?>4<?php } ?> col-lg-5 hidden-sm col-md-pull-4">
				<div class="slogan">
					<p>Надежный инструмент</p>
					<p>оптом и в розницу!</p>
					<p>Доставка сегодня!</p>
				</div>
				<div class="ourcontacts">
					<div id="phone">
						<div class="phone dropdown-toggle pull-right" data-toggle="dropdown">
							<div>
								<span>+7 (812)-915-98-30</span>
							</div>
							<div><?php echo $delivery_hours; ?></div>
						</div>
						<ul class="dropdown-menu dropdown-menu-right">
							<?php if($callback) { ?><li><a onclick="callback();" class="open_callback"><span class="hidden-xs"><?php echo $lang_1['text_header_callback']; ?></span><?php echo $lang_1['text_header_callback1']; ?></a></li><?php } ?>
							<?php if($text_in_add_contacts_position && $text_in_add_contacts) { ?><li class="text"><hr style="margin-top:0px;" /><?php echo $text_in_add_contacts; ?><hr style="margin-bottom:5px;" /></li><?php } ?>
							<?php foreach ($phones as $phone) { ?>
								<li><a><i class="<?php echo $phone['icon']; ?>" aria-hidden="true"></i><span><?php echo $phone['number']; ?></span></a></li>
							<?php } ?>
							<?php if(!$text_in_add_contacts_position && $text_in_add_contacts) { ?><li class="text"><hr style="margin-top:5px;" /><?php echo $text_in_add_contacts; ?></li><?php } ?>
						</ul>
						<p class="callmebaby" onclick="callback()">Заказать обратный звонок</p>
					</div>
				</div>
				<div id="div_search"><?php echo $search; ?>
				</div>
				<div class="akcii1">
						<ul class="dropdown1">
							<li class="dropdown-top1">
								<img src="image/catalog/img/skidka.png" alt="">
								<span><a class="dropdown-top1" href="/">Акции</a></span>
								<!-- <ul class="dropdown-inside1">
									<li><a href="#">Акция1</a></li>
									<li><a href="#">Акция2</a></li>
									<li><a href="#">Акция3</a></li>
									<li><a href="#">Акция4</a></li>
									<li><a href="#">Акция5</a></li>
								</ul> -->
							</li>
						</ul>
				</div>
			</div>



		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-8 col-lg-9 col-md-push-4 col-lg-push-3">
				<div class="menu_links">
					<?php foreach ($headerlinks2 as $headerlink) { ?>
						<a href="<?php echo $headerlink['link']; ?>" title="<?php echo $headerlink['title']; ?>"><?php if($headerlink['icon']) { echo '<i class="'.$headerlink['icon'].' hidden-md"></i>'; } ?><?php echo $headerlink['title']; ?></a>
					<?php } ?>
				</div>
			</div>
			<?php if ($categories) { ?>
				<div class="col-sm-6 col-md-4 col-lg-3 col-md-pull-8 col-lg-pull-9">
					<nav id="menu" class="menu2 navbar">
						<div class="navbar-header">
							<span id="category" class=""><?php echo $text_menu; ?></span>
							<button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars" aria-hidden="true"></i></button>
						</div>
						<div class="collapse navbar-collapse navbar-ex1-collapse">
						<ul class="nav navbar-nav">
							<?php foreach ($categories as $category) { ?>
								<?php
									$menu_img = substr($category['href'],0,-1);
									$menu_img = substr($menu_img,strrpos($menu_img,'/')+1);
									$menu_img = (file_exists(DIR_IMAGE.'catalog/img/img_for_menu/'.$menu_img.'.png')) ? '<img src="'.HTTPS_SERVER.'image/catalog/img/img_for_menu/'.$menu_img.'.png">' : '';
								?>
								<?php if ($category['children']) { ?>
									<li class="has_chidren">
										<?php if($menu_links_disabled && $category['category_id'] == $category_id) { ?>
											<a><?php echo $menu_img;?>&nbsp;<?php echo $category['name']; ?> <i class="fa fa-chevron-down" aria-hidden="true"></i></a>
										<?php } else { ?>
											<a href="<?php echo $category['href']; ?>"><?php echo $menu_img;?>&nbsp;<?php echo $category['name']; ?> <i class="fa fa-chevron-down" aria-hidden="true"></i></a>
										<?php } ?>
										<span class="dropdown-toggle visible-xs visible-sm"><i class="fa fa-plus" aria-hidden="true"></i><i class="fa fa-minus" aria-hidden="true"></i></span>
										<div class="dropdown-menu">
											<div class="dropdown-inner">
												<?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
													<ul class="list-unstyled <?php if ($category['column']) { echo 'column'; } ?>">
														<?php foreach ($children as $child) { ?>
															<li>
																<?php if($menu_links_disabled && $child['category_id'] == $category_id) { ?>
																	<a style="text-decoration:none;cursor:default"><i class="fa fa-level-up visible-xs visible-sm" aria-hidden="true"></i><?php echo $child['name']; ?></a>
																<?php } else { ?>
																	<a href="<?php echo $child['href']; ?>"><i class="fa fa-level-up visible-xs visible-sm" aria-hidden="true"></i><?php echo $child['name']; ?></a>
																<?php } ?>
																<?php if (isset($child['children']) && count($child['children']) > 0) { ?>
																	<span class="visible-xs visible-sm"><i class="fa fa-plus" aria-hidden="true"></i><i class="fa fa-minus" aria-hidden="true"></i></span>
																	<div class="dropdown-menu">
																		<div class="dropdown-inner">
																			<ul class="list-unstyled">
																				<?php foreach ($child['children'] as $child) { ?>
																					<li>
																						<?php if($menu_links_disabled && $child['category_id'] == $category_id) { ?>
																							<a style="text-decoration:none;cursor:default"><i class="fa fa-level-up visible-xs visible-sm" aria-hidden="true"></i><?php echo $child['name']; ?></a>
																						<?php } else { ?>
																							<a href="<?php echo $child['href']; ?>"><i class="fa fa-level-up visible-xs visible-sm" aria-hidden="true"></i><?php echo $child['name']; ?></a>
																						<?php } ?>
																					</li>
																				<?php } ?>
																			</ul>
																		</div>
																	</div>
																<?php } ?>
															</li>
														<?php } ?>
													</ul>
												<?php } ?>
											</div>
										</div>
									</li>
								<?php } else { ?>
									<?php if($menu_links_disabled && $category['category_id'] == $category_id) { ?>
										<li><a><?php echo $menu_img;?>&nbsp;<?php echo $category['name']; ?></a></li>
									<?php } else { ?>
										<li><a href="<?php echo $category['href']; ?>"><?php echo $menu_img;?>&nbsp;<?php echo $category['name']; ?></a></li>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</ul>
						</div>
					</nav>
				</div>
			<?php } ?>
			<div id="div_search2" class="col-xs-12 col-sm-6 col-md-5 visible-sm"></div>
		</div>
	</div>
</header>
<div id="main_content">