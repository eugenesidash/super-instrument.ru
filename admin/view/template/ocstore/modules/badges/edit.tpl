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
                <?=$entry_addedit;?>
                <div class="pull-right">
				    <h5 class="panel-title"><?php echo $ocstore_header; ?></h5>
                </div>
            </div>
            <div class="panel-body">
                <? if(isset($error) && $error != ""): ?>
                    <div class="alert alert-danger">
                        <?=$error;?>
                    </div>
                <? endif; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="form-group required <?=(isset($errors['name'])? "has-error": "");?>">
                        <label class="col-sm-3 control-label" for="name"><?=$entry_badges_name;?></label>
                        <div class="col-sm-9">
                            <input type="text" name="name" value="<?=htmlentities($name);?>" class="form-control"  />

                            <? if(isset($errors['name'])): ?>
                                <div class="alert alert-danger" style="margin-top: 5px">
                                    <?=$errors['name'];?>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-3 control-label" for="image"><?=$entry_badges_image;?></label>
                        <div class="col-sm-9">
                            <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?=($image != ""? "/image/{$image}": "/image/no_image.png"); ?>" alt="" title=""  /></a>
                            <input type="hidden" name="image" value="<?=$image;?>" id="input-image" />


                            <? if(isset($errors['image'])): ?>
                                <div class="alert alert-danger" style="margin-top: 5px">
                                    <?=$errors['image'];?>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-3 control-label" for="image"><?=$entry_badges_position;?></label>
                        <div class="col-sm-9">
                            <select name="data[position]" class="form-control">
                                <? foreach (array("topleft", "topright", "bottomleft", "bottomright") as $p): ?>
                                    <? $pp = "entry_position_{$p}"; ?>
                                    <option value="<?=$p;?>" <?=($data['position'] == $p? "selected": "");?>><?=$$pp;?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group <?=(isset($errors['data[badgetext]'])? "has-error": "");?>">
                        <label class="col-sm-3 control-label" for="name"><?=$entry_badges_text;?></label>
                        <div class="col-sm-9">
                            <input type="text" name="data[badgetext]" value="<?=htmlentities($data['badgetext']);?>" class="form-control"  />

                            <? if(isset($errors['data[badgetext]'])): ?>
                            <div class="alert alert-danger" style="margin-top: 5px">
                                <?=$errors['data[badgetext]'];?>
                            </div>
                            <? endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="image"><?=$entry_position;?></label>
                        <div class="col-sm-3">
                            <table>
                                <tr>
                                    <td colspan="3"><?=$entry_position_badge;?></td>
                                </tr>
                                <tr>
                                    <td width="60px">&nbsp;</td>
                                    <td width="60px" class="text-center">
                                        <input type="hidden" name="data[badge][top]" value="<?=(isset($data['badge']['top'])? $data['badge']['top']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block badgetop" type="button" data-toggle="tooltip" title="<?=$entry_position_top;?>"><i class="fa fa-caret-up"></i></button>
                                    </td>
                                    <td width="60px">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <input type="hidden" name="data[badge][left]" value="<?=(isset($data['badge']['left'])? $data['badge']['left']: "");?>"  class="form-control text-center"  />
                                        <button class="btn btn-default btn-block badgeleft" type="button" data-toggle="tooltip" title="<?=$entry_position_left;?>"><i class="fa fa-caret-left"></i></button>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="text-center">
                                        <input type="hidden" name="data[badge][right]" value="<?=(isset($data['badge']['right'])? $data['badge']['right']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block badgeright" type="button" data-toggle="tooltip" title="<?=$entry_position_right;?>"><i class="fa fa-caret-right"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="text-center">
                                        <input type="hidden" name="data[badge][bottom]" value="<?=(isset($data['badge']['bottom'])? $data['badge']['bottom']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block badgebottom" type="button" data-toggle="tooltip" title="<?=$entry_position_bottom;?>"><i class="fa fa-caret-down"></i></button>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table>
                                <tr>
                                    <td colspan="6" class="text-center"><?=$entry_position_text;?></td>
                                </tr>
                                <tr>
                                    <td width="60px">&nbsp;</td>
                                    <td width="60px" class="text-center">
                                        <input type="hidden" name="data[text][top]" value="<?=(isset($data['text']['top'])? $data['text']['top']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block texttop" type="button" data-toggle="tooltip" title="<?=$entry_position_top;?>"><i class="fa fa-caret-up"></i></button>
                                    </td>
                                    <td width="60px">&nbsp;</td>
                                    <td width="60px">&nbsp;</td>
                                    <td class="text-right">
                                        <?=$entry_badges_color;?>:
                                    </td>
                                    <td style="padding-left: 10px">
                                        <input name="data[color]" type="color" value="<?=(isset($data['color'])? $data['color']: "#000000");?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <input type="hidden" name="data[text][left]" value="<?=(isset($data['text']['left'])? $data['text']['left']: "");?>"  class="form-control text-center"  />
                                        <button class="btn btn-default btn-block textleft" type="button" data-toggle="tooltip" title="<?=$entry_position_left;?>"><i class="fa fa-caret-left"></i></button>
                                    </td>
                                    <td class="<?=(isset($errors['angle'])? "has-error": "");?>" width="58px">
                                        <input type="text" name="data[angle]" value="<?=htmlentities($data['angle']);?>" class="form-control text-center" data-toggle="tooltip" title="<?=$entry_badges_angle;?>" />
                                    </td>
                                    <td class="text-center" >
                                        <input type="hidden" name="data[text][right]" value="<?=(isset($data['text']['right'])? $data['text']['right']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block textright" type="button" data-toggle="tooltip" title="<?=$entry_position_right;?>"><i class="fa fa-caret-right"></i></button>
                                    </td>
                                    <td width="60px">&nbsp;</td>
                                    <td class="text-right">
                                        <?=$entry_badges_size;?>:
                                    </td>
                                    <td style="padding-left: 10px">
                                        <input name="data[size]" type="number" min="6" max="72" value="<?=(isset($data['size'])? $data['size']: "10");?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="text-center">
                                        <input type="hidden" name="data[text][bottom]" value="<?=(isset($data['text']['bottom'])? $data['text']['bottom']: "");?>" class="form-control text-center" />
                                        <button class="btn btn-default btn-block textbottom" type="button" data-toggle="tooltip" title="<?=$entry_position_bottom;?>"><i class="fa fa-caret-down"></i></button>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td width="60px">&nbsp;</td>
                                    <td class="text-right">
                                        <?=$entry_badges_font;?>:
                                    </td>
                                    <td style="padding-left: 10px">
                                        <input name="data[font]" type="text" value="<?=(isset($data['font'])? $data['font']: "Arial");?>" list="fonts" />
                                        <datalist id="fonts">
                                            <option value="Arial">
                                            <option value="Courier New">
                                            <option value="Georgia">
                                            <option value="Helvetica">
                                            <option value="Sans">
                                            <option value="Tahoma">
                                            <option value="Times">
                                            <option value="Verdana">
                                        </datalist>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name"><?=$entry_livepreview;?></label>
                        <div class="col-sm-9">
                            <div style="width: <? $image = getimagesize(DIR_IMAGE."catalog/testitem.png"); echo $image[0];?>px; position: relative">
                                <div class="livepreview" style="position: absolute">
                                    <span class="text" style="position:absolute;"></span>
                                    <img src="" class="imgbadge" />
                                </div>
                                <img src="/image/catalog/testitem.png" />
                            </div>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$entry_ballon;?></label>
                        <div class="col-sm-9">
							<textarea name="data[ballontext]" id="ballontext"><?=(isset($data['ballontext'])? $data['ballontext']: "");?></textarea>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?=$entry_ballon_position;?></label>
                        <div class="col-sm-9">
                            <select name="data[ballonposition]" class="form-control">
								<? foreach (array("top", "left", "right", "bottom") as $p): ?>
									<option value="<?=$p;?>" <?=($p == (isset($data['ballonposition'])? $data['ballonposition']: "")? "selected": "");?>><? $field = "entry_ballon_{$p}"; echo $$field;?></option>
								<? endforeach; ?>
							</select>
                        </div>
                    </div>
					
					
                    <div class="form-group required <?=(isset($errors['enabled'])? "has-error": "");?>">
                        <label class="col-sm-3 control-label" for="enabled"><?=$entry_badges_enabled;?></label>
                        <div class="col-sm-9">
                            <select name="enabled" class="form-control">
                                <option value="0" <?=($enabled == 0? "SELECTED": "");?>><?=$entry_disabled;?></option>
                                <option value="1" <?=($enabled == 1? "SELECTED": "");?>><?=$entry_enabled;?></option>
                            </select>
                            <? if(isset($errors['enabled'])): ?>
                                <div class="alert alert-danger" style="margin-top: 5px">
                                    <?=$errors['enabled'];?>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label" for="input-name"><?=$entry_category;?></label>
						<div class="col-sm-9">
							<select class="categories form-control" multiple="multiple" name="data[category][]">
								<?php foreach ($allcategories as $c) : ?>
									<option value="<?=$c['category_id'];?>" <?=(isset($data['category']) && in_array($c['category_id'], $data['category'])? "SELECTED": "");?>><?=$c['name'];?>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-3 control-label" for="input-name"><?=$entry_category;?></label>
						<div class="col-sm-9">
							<select class="tags form-control" multiple="multiple" name="data[tags][]">
								<?php foreach ($alltags as $t) : ?>
									<option value="<?=$t;?>" <?=(isset($data['tags']) && in_array($t, $data['tags'])? "SELECTED": "");?>><?=$t;?>
								<? endforeach; ?>
							</select>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-success"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="view/template/ocstore/js/badges.js"></script>>

<script type="text/javascript">
	$('#ballontext').summernote({height: 200});
</script>  

<script>
    $(document).ready(function() {
        $(".categories").select2({});
        $(".tags").select2({});
    });
</script> 

<?php echo $footer; ?>