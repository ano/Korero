<?php

// id
// language

?>
<?php if ($_language->Visible) { ?>
<table cellspacing="0" id="t__language" class="ewGrid"><tr><td>
<table id="tbl__languagemaster" class="table table-bordered table-striped">
	<tbody>
<?php if ($_language->id->Visible) { // id ?>
		<tr id="r_id">
			<td><?php echo $_language->id->FldCaption() ?></td>
			<td<?php echo $_language->id->CellAttributes() ?>>
<span id="el__language_id" class="control-group">
<span<?php echo $_language->id->ViewAttributes() ?>>
<?php echo $_language->id->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
<?php if ($_language->_language->Visible) { // language ?>
		<tr id="r__language">
			<td><?php echo $_language->_language->FldCaption() ?></td>
			<td<?php echo $_language->_language->CellAttributes() ?>>
<span id="el__language__language" class="control-group">
<span<?php echo $_language->_language->ViewAttributes() ?>>
<?php echo $_language->_language->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
</td></tr></table>
<?php } ?>
