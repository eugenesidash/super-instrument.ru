<?php echo $header; ?>
<link href="view/stylesheet/unishop.css" property="stylesheet" rel="stylesheet" type="text/css" media="screen" />
<?php echo $column_left; ?>
<div id="content">
<div class="page-header">
<div class="container-fluid">
<div class="pull-right">
	<!-- <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Отменить" class="btn btn-default"><i class="fa fa-reply"></i></a> -->
</div>
<h1><?php echo $heading_title; ?></h1>
<br />
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
</div>
</div>
<div class="container-fluid container-fluid_new">
<?php if ($success) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
<?php } ?>
<?php if ($error_warning) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>

<div id="modification_load" style="display:none !important"></div>
<div style="display:table;width:100%;">
	<ul class="nav nav-stacked nav-tabs nav-pills">
		<li class="active"><a href="#tab-updates" data-toggle="tab">Unishop - управление обновлениями</a></li>
		<li><a href="#tab-template" data-toggle="tab">Unishop - шаблон</a></li>
		<li><a href="#tab-blog" data-toggle="tab">Unishop - блог</a></li>
		<li><a href="#tab-request" data-toggle="tab">Unishop - "Вопросы покупателей"</a></li>
	</ul>
	<div class="tab-content tab-content-new">
		<div id="tab-updates" class="tab-pane active">
			<table id="module" class="list">
				<tr>
					<td colspan="2" class="header">
						<b>UniShop - модуль управления обновлениеми</b>
					</td>
				</tr>
				<tr><td>При обновлении шаблона или дополнительных модулей делается бекап изменяемых файлов. Найти его можно в папке /system/storage/download/</td></tr>
				<tr><td><a class="update btn btn-default" onclick="update('update');"></a></td></tr>
			</table>
			<div class="update_history"></div>
		</div>
		<div id="tab-template" class="tab-pane">
			<table id="module" class="list">
				<tr><td colspan="2" class="header"><b>Обновление шаблона</b></td></tr>
				<tr><td><a class="template btn btn-default" onclick="update('template');"></a></td></tr>
			</table>
			<div class="template_history"></div>
		</div>
		<div id="tab-blog" class="tab-pane">
			<table id="module" class="list">
				<tr><td colspan="2" class="header"><b>Обновление блога</b></td></tr>
				<tr><td><a class="blog btn btn-default" onclick="update('blog');"></a></td></tr>
			</table>
			<div class="blog_history"></div>
		</div>
		<div id="tab-request" class="tab-pane">
			<table id="module" class="list">
				<tr><td colspan="2" class="header"><b>Обновление модуля "Вопросы покупателей"?</b></td></tr>
				<tr><td><a class="request btn btn-default" onclick="update('request');"></a></td></tr>
			</table>
			<div class="request_history"></div>
		</div>
	</div>
</div>
<div id="copyright"><?php echo $text_copyright; ?></div>
</div>
<script src="view/javascript/popup.js" type="text/javascript"></script>
</div>
</div>

<script type="text/javascript">
	$('.nav-tabs a:first').tab('show');
	$('#tab-0 .nav-tabs a:first').tab('show');

	$('document').ready(function() {		
		<?php $datas = array(); ?>
		<?php $datas[] = array('type' => 'update'); ?>
		<?php $datas[] = array('type' => 'template'); ?>
		<?php $datas[] = array('type' => 'blog'); ?>
		<?php $datas[] = array('type' => 'request'); ?>
		<?php foreach($datas as $data) { ?>
			check('<?php echo $data['type']; ?>');
		<?php } ?>
		
		$('.nav-pills li').on('click', function() {
			var destination = $('.nav-pills').offset().top-60;
			$('html, body').animate({scrollTop: destination}, 400);
		});
	});

	function update(type) {
		$.ajax({
			url: 'index.php?route=module/unishop_update/version&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: 'type='+type+'&update=1',
			success: function(json) {
				if(json['error']) {
					$('#content').append('<div class="div_update"><ul><li>'+json['text']+'</li></ul></div>');
					$('#content .div_update').append('<div class="text-right"><br /><button class="btn btn-primary" onclick="$(\'.div_update\').popup(\'hide\');">Закрыть</button>');
					$('.div_update').popup('show');
				}
				if(json['success']) {
					$('#content').append('<div class="div_update"><ul>'+json['text']+'</ul></div>');
					$('#content .div_update').append('<div class="text-right"><br /><button class="btn btn-primary" onclick="window.location.reload();">Я все прочитал(а), продолжить</button>');
					$('.div_update').popup('show');
					refresh();
				}
				if(json['error_permission']) {
					$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '+json['text']+'</div>');
				}
			}
		});
	}
	
	function check(type) {
		$.ajax({
			url: 'index.php?route=module/unishop_update/version&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: 'type='+type,
			success: function(json) {
				if(json['success']) {
					if(json['new_version']) {
						$('div.'+type+'_history').html(json['history']);
						$('.btn.'+type).append(json['text']).addClass('btn-primary');
						$('.container-fluid_new').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> '+json['text']+'</div>');
					} else {
						$('div.'+type+'_history').html(json['history']);
						$('.btn.'+type).append(json['text']);
					}
				} 
				if(json['error']) {
					if(type == 'update') {
						$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '+json['text']+'</div>');
						$('.btn.update').text(json['text']).addClass('btn-danger');
					}
					if(type == 'template') {
						$('.container-fluid_new').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '+json['text']+'</div>');
						$('.btn.template').text(json['text']).addClass('btn-danger');
					}
					if(type == 'blog')  {
						$('.update.blog').after('&nbsp;&nbsp;&nbsp; Приобрести его вы можете по данному адресу: <a href="https://opencartforum.com/files/file/3268-%D0%B1%D0%BB%D0%BE%D0%B3-%D0%B4%D0%BB%D1%8F-%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0-unishop/" target="_blank" >OpencartForum.com</>');
						$('.btn.blog').text(json['text']).addClass('btn-danger');
					}
					if(type == 'request')  {
						$('.update.request').after('&nbsp;&nbsp;&nbsp; Приобрести его вы можете по данному адресу: <a href="https://opencartforum.com/files/file/3319-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D1%8B-%D0%BF%D0%BE%D0%BA%D1%83%D0%BF%D0%B0%D1%82%D0%B5%D0%BB%D0%B5%D0%B9-%D0%B4%D0%BB%D1%8F-%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0-unishop/" target="_blank" >OpencartForum.com</>');
						$('.btn.request').text(json['text']).addClass('btn-danger');
					}
				}
			}
		});
	}
	
	function refresh() {
		$.ajax({
			url: 'index.php?route=extension/modification/refresh&token=<?php echo $token; ?>',
		});
		$.ajax({
			url: 'index.php?route=catalog/review&token=<?php echo $token; ?>',
		});
		$.ajax({
			url: 'index.php?route=uni_blog/category&token=<?php echo $token; ?>',
		});
	}
</script>
<?php echo $footer; ?>