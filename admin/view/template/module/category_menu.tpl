<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table id="module" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_layout; ?></td>
              <td class="left"><?php echo $entry_position; ?></td>
              <td class="left"><?php echo $entry_levels; ?></td>
              <td class="left"><?php echo $entry_count; ?></td>
              <td class="left"><?php echo $entry_images; ?></td>
              <td class="left"><?php echo $entry_status; ?></td>
              <td class="right"><?php echo $entry_sort_order; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $module_row = 0; ?>
          <?php foreach ($modules as $module) { ?>
          <tbody id="module-row<?php echo $module_row; ?>">
            <tr>
              <td class="left"><select name="category_menu_module[<?php echo $module_row; ?>][layout_id]">
                  <?php foreach ($layouts as $layout) { ?>
                  <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                  <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
              <td class="left"><select name="category_menu_module[<?php echo $module_row; ?>][position]">
                  <?php if ($module['position'] == 'content_top') { ?>
                  <option value="content_top" selected="selected"><?php echo $text_content_top; ?></option>
                  <?php } else { ?>
                  <option value="content_top"><?php echo $text_content_top; ?></option>
                  <?php } ?>
                  <?php if ($module['position'] == 'content_bottom') { ?>
                  <option value="content_bottom" selected="selected"><?php echo $text_content_bottom; ?></option>
                  <?php } else { ?>
                  <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
                  <?php } ?>
                  <?php if ($module['position'] == 'column_left') { ?>
                  <option value="column_left" selected="selected"><?php echo $text_column_left; ?></option>
                  <?php } else { ?>
                  <option value="column_left"><?php echo $text_column_left; ?></option>
                  <?php } ?>
                  <?php if ($module['position'] == 'column_right') { ?>
                  <option value="column_right" selected="selected"><?php echo $text_column_right; ?></option>
                  <?php } else { ?>
                  <option value="column_right"><?php echo $text_column_right; ?></option>
                  <?php } ?>
                </select></td>
              <td class="left"><select name="category_menu_module[<?php echo $module_row; ?>][level]">
                  <?php if ($module['level'] == '1') { ?>
                  <option value="1" selected="selected"><?php echo $entry_category; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $entry_category; ?></option>
                  <?php } ?>
                  <?php if ($module['level'] == '2') { ?>
                  <option value="2" selected="selected"><?php echo $entry_child; ?></option>
                  <?php } else { ?>
                  <option value="2"><?php echo $entry_child; ?></option>
                  <?php } ?>
                  <?php if ($module['level'] == '3') { ?>
                  <option value="3" selected="selected"><?php echo $entry_subchild; ?></option>
                  <?php } else { ?>
                  <option value="3"><?php echo $entry_subchild; ?></option>
                  <?php } ?>
                </select>
                <select name="category_menu_module[<?php echo $module_row; ?>][style]">
                  <?php if ($module['style'] == '1') { ?>
                  <option value="1" selected="selected"><?php echo $entry_horisontal; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $entry_horisontal; ?></option>
                  <?php } ?>
                  <?php if ($module['style'] == '2') { ?>
                  <option value="2" selected="selected"><?php echo $entry_vertical; ?></option>
                  <?php } else { ?>
                  <option value="2"><?php echo $entry_vertical; ?></option>
                  <?php } ?>
                </select>
                </td>
              <td class="left">
              	<table>
              		<tr>
              			<td><?php echo $entry_category; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][category_count]">
			                  <?php if ($module['category_count']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
              			</td>
              		</tr>
              		<tr>
              			<td><?php echo $entry_child; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][child_count]">
			                  <?php if ($module['child_count']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
              			</td>
              		</tr>
              		<tr>
              			<td><?php echo $entry_subchild; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][subchild_count]">
			                  <?php if ($module['subchild_count']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
              			</td>
              		</tr>
              	</table>
              </td>
              <td class="left">
              	<table>
              		<tr>
              			<td><?php echo $entry_category; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][category_images][status]">
			                  <?php if ($module['category_images']['status']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
			            </td>
			            <td>
			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][category_images][width]" value="<?php echo $module['category_images']['width']; ?>" size="3" />px X
			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][category_images][height]" value="<?php echo $module['category_images']['height']; ?>" size="3" />px
              			</td>
              		</tr>
              		<tr>
              			<td><?php echo $entry_child; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][child_images][status]">
			                  <?php if ($module['child_images']['status']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
			            </td>
			            <td>
   			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][child_images][width]" value="<?php echo $module['child_images']['width']; ?>" size="3" />px X
			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][child_images][height]" value="<?php echo $module['child_images']['height']; ?>" size="3" />px
              			</td>
              		</tr>
              		<tr>
              			<td><?php echo $entry_subchild; ?></td>
              			<td>
			              	<select name="category_menu_module[<?php echo $module_row; ?>][subchild_images][status]">
			                  <?php if ($module['subchild_images']['status']) { ?>
			                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
			                  <option value="0"><?php echo $text_disabled; ?></option>
			                  <?php } else { ?>
			                  <option value="1"><?php echo $text_enabled; ?></option>
			                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
			                  <?php } ?>
			                </select>
			       		</td>
			       		<td>
			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][subchild_images][width]" value="<?php echo $module['subchild_images']['width']; ?>" size="3" />px X
			                <input type="text" name="category_menu_module[<?php echo $module_row; ?>][subchild_images][height]" value="<?php echo $module['subchild_images']['height']; ?>" size="3" />px
              			</td>
              		</tr>
              	</table>
              </td>
              <td class="left"><select name="category_menu_module[<?php echo $module_row; ?>][status]">
                  <?php if ($module['status']) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
              <td class="right"><input type="text" name="category_menu_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" /></td>
              <td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
            </tr>
          </tbody>
          <?php $module_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="7"></td>
              <td class="left"><a onclick="addModule();" class="button"><?php echo $button_add_module; ?></a></td>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
	<div style="margin-top:25px;border-top:1px dashed #ccc;padding-top:15px;text-align:center;"><? echo $text_help; ?></div>
  </div>
</div>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="category_menu_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';
	html += '    <td class="left"><select name="category_menu_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $text_content_top; ?></option>';
	html += '      <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
	html += '      <option value="column_left"><?php echo $text_column_left; ?></option>';
	html += '      <option value="column_right"><?php echo $text_column_right; ?></option>';
	html += '    </select></td>';
    html += '    <td class="left"><select name="category_menu_module[' + module_row + '][level]">';
    html += '     <option value="1" selected="selected"><?php echo $entry_category; ?></option>';
    html += '     <option value="2"><?php echo $entry_child; ?></option>';
    html += '     <option value="3"><?php echo $entry_subchild; ?></option>';
    html += '    </select>';
    html += '    <select name="category_menu_module[' + module_row + '][style]">';
    html += '     <option value="1" selected="selected"><?php echo $entry_horisontal; ?></option>';
    html += '     <option value="2"><?php echo $entry_vertical; ?></option>';
    html += '   </select>';
    html += '   </td>';
    html += ' <td class="left">';
    html += ' 	<table>';
    html += ' 		<tr>';
    html += ' 			<td><?php echo $entry_category; ?></td>';
    html += ' 			<td>';
	html += '             	<select name="category_menu_module[' + module_row + '][category_count]">';
	html += '                 <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '                 <option value="0"><?php echo $text_disabled; ?></option>';
	html += '               </select>';
    html += ' 			</td>';
    html += ' 		</tr>';
    html += ' 		<tr>';
    html += ' 			<td><?php echo $entry_child; ?></td>';
    html += ' 			<td>';
	html += '             	<select name="category_menu_module[' + module_row + '][child_count]">';
	html += '                 <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '                 <option value="0"><?php echo $text_disabled; ?></option>';
	html += '               </select>';
    html += ' 			</td>';
    html += ' 		</tr>';
    html += ' 		<tr>';
    html += ' 			<td><?php echo $entry_subchild; ?></td>';
    html += ' 			<td>';
	html += '             	<select name="category_menu_module[' + module_row + '][subchild_count]">';
	html += '                 <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '                 <option value="0"><?php echo $text_disabled; ?></option>';
	html += '               </select>';
    html += ' 			</td>';
    html += ' 		</tr>';
    html += ' 	</table>';
    html += ' </td>';
    html += ' <td class="left">';
    html += ' 	<table>';
    html += ' 		<tr>';
    html += ' 			<td><?php echo $entry_category; ?></td>';
	html += '     			<td>';
	html += '		              	<select name="category_menu_module[' + module_row + '][category_images][status]">';
	html += '		                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '		                  <option value="0"><?php echo $text_disabled; ?></option>';
	html += '		                </select>';
	html += '		            </td>';
	html += '		            <td>';
	html += '		                <input type="text" name="category_menu_module[' + module_row + '][category_images][width]" value="0" size="3" />px X';
	html += '		                <input type="text" name="category_menu_module[' + module_row + '][category_images][height]" value="0" size="3" />px';
    html += '          			</td>';
    html += '          		</tr>';
    html += '          		<tr>';
    html += '          			<td><?php echo $entry_child; ?></td>';
    html += '          			<td>';
	html += '		              	<select name="category_menu_module[' + module_row + '][child_images][status]">';
	html += '		                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '		                  <option value="0"><?php echo $text_disabled; ?></option>';
	html += '		                </select>';
	html += '		            </td>';
	html += '		            <td>';
    html += '			            <input type="text" name="category_menu_module[' + module_row + '][child_images][width]" value="0" size="3" />px X';
	html += '		                <input type="text" name="category_menu_module[' + module_row + '][child_images][height]" value="0" size="3" />px';
    html += '          			</td>';
    html += '          		</tr>';
    html += '          		<tr>';
    html += '          			<td><?php echo $entry_subchild; ?></td>';
	html += '             			<td>';
	html += '		              	<select name="category_menu_module[' + module_row + '][subchild_images][status]">';
	html += '		                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
	html += '		                  <option value="0"><?php echo $text_disabled; ?></option>';
	html += '		                </select>';
	html += '      		</td>';
	html += '      		<td>';
	html += '               <input type="text" name="category_menu_module[' + module_row + '][subchild_images][width]" value="0" size="3" />px X';
	html += '               <input type="text" name="category_menu_module[' + module_row + '][subchild_images][height]" value="0" size="3" />px';
    html += ' 			</td>';
    html += ' 		</tr>';
    html += ' 	</table>';
    html += '  </td>';
	html += '    <td class="left"><select name="category_menu_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
    html += '      <option value="0"><?php echo $text_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="category_menu_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';

	$('#module tfoot').before(html);

	module_row++;
}
//--></script>
<?php echo $footer; ?>