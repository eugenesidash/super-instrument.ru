<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
	
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
				<button type="button" title="" class="btn btn-xs btn-success recache" data-original-title="<?=$entry_recache;?>" data-toggle="tooltip"><i class="fa fa-refresh"></i></button>
				<button type="button" title="" class="btn btn-xs btn-danger empty" data-original-title="<?=$entry_empty;?>" data-toggle="tooltip"><i class="fa fa-eraser"></i></button>


				<div class="pull-right">
					<h5 class="panel-title"><?php echo $ocstore_header; ?></h5>
				</div>
            </div>
            <div class="panel-body">
				<? if (isset($error) && $error != "") : ?>
					<div class="alert alert-danger"><?=$error;?></div>
				<? endif;?>
				<? if (isset($success) && $success != "") : ?>
					<div class="alert alert-success"><?=$success;?></div>
				<? endif;?>
                <form method="post">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?=$entry_badges_name;?></th>
                                <th><?=$entry_badges_image;?></th>
                                <th><?=$entry_badges_enabled;?></th>
                                <th width="100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($badges as $b): ?>
                                <tr <?=($b['enabled']==0? 'class="danger"': "");?>>
                                    <td><?=$b['name'];?></td>
                                    <td><img src="/image/<?=$b['image'];?>"/></td>
                                    <td><?=($b['enabled'] == 1? $entry_enabled: $entry_disabled);?></td>
                                    <td class="text-right">
                                        <a href="<?=$link_edit;?>&id=<?=$b['id'];?>" data-toggle="tooltip" title="" class="btn btn-xs btn-primary" data-original-title="<?=$button_edit;?>"><i class="fa fa-pencil"></i></a>
                                        <button type="button" data-href="<?=$link_remove;?>&id=<?=$b['id'];?>" title="" class="btn btn-xs btn-danger remove" data-original-title="<?=$button_remove;?>" data-toggle="tooltip"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </form>
                <a href="<?=$link_add;?>" class="btn btn-sm btn-success"><?=$button_add;?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="remove" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$entry_remove;?></h4>
            </div>
            <div class="modal-body">
                <?=$entry_remove_text;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$button_close;?></button>
                <a href="" class="btn btn-danger" id="removelink"><?=$button_remove;?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="recache" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$entry_recache;?></h4>
            </div>
            <div class="modal-body">
                <?=$entry_recache_text;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$button_close;?></button>
                <a href="<?=$link_recache;?>" class="btn btn-success" ><?=$button_recache;?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="empty" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=$entry_empty;?></h4>
            </div>
            <div class="modal-body">
                <?=$entry_empty_text;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$button_close;?></button>
                <a href="<?=$link_empty;?>" class="btn btn-danger" ><?=$button_empty;?></a>
            </div>
        </div>
    </div>
</div>


<script>
    $(function() {
        $(".remove").click(function() {
            $("#removelink").attr("href", $(this).data("href"));
            $("#remove").modal("show");
        });
        $(".recache").click(function() {
            $("#recache").modal("show");
        });
        $(".empty").click(function() {
            $("#empty").modal("show");
        });
    });
</script>

<?php echo $footer; ?>