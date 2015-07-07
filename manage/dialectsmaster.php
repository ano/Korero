<?php

// id
// language_id
// dialects

?>
<?php if ($dialects->Visible) { ?>
<table cellspacing="0" id="t_dialects" class="ewGrid"><tr><td>
<table id="tbl_dialectsmaster" class="table table-bordered table-striped">
	<tbody>
<?php if ($dialects->id->Visible) { // id ?>
		<tr id="r_id">
			<td><?php echo $dialects->id->FldCaption() ?></td>
			<td<?php echo $dialects->id->CellAttributes() ?>>
<span id="el_dialects_id" class="control-group">
<span<?php echo $dialects->id->ViewAttributes() ?>>
<?php echo $dialects->id->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($dialects->language_id->Visible) { // language_id ?>
		<tr id="r_language_id">
			<td><?php echo $dialects->language_id->FldCaption() ?></td>
			<td<?php echo $dialects->language_id->CellAttributes() ?>>
<span id="el_dialects_language_id" class="control-group">
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($dialects->dialects->Visible) { // dialects ?>
		<tr id="r_dialects">
			<td><?php echo $dialects->dialects->FldCaption() ?></td>
			<td<?php echo $dialects->dialects->CellAttributes() ?>>
<span id="el_dialects_dialects" class="control-group">
<span<?php echo $dialects->dialects->ViewAttributes() ?>>
<?php echo $dialects->dialects->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
</td></tr></table>
<?php } ?>
