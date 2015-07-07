<?php

// Create page object
if (!isset($dialects_grid)) $dialects_grid = new cdialects_grid();

// Page init
$dialects_grid->Page_Init();

// Page main
$dialects_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$dialects_grid->Page_Render();
?>
<?php if ($dialects->Export == "") { ?>
<script type="text/javascript">

// Page object
var dialects_grid = new ew_Page("dialects_grid");
dialects_grid.PageID = "grid"; // Page ID
var EW_PAGE_ID = dialects_grid.PageID; // For backward compatibility

// Form object
var fdialectsgrid = new ew_Form("fdialectsgrid");
fdialectsgrid.FormKeyCountName = '<?php echo $dialects_grid->FormKeyCountName ?>';

// Validate form
fdialectsgrid.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			elm = this.GetElements("x" + infix + "_language_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($dialects->language_id->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_dialects");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($dialects->dialects->FldCaption()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
fdialectsgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "language_id", false)) return false;
	if (ew_ValueChanged(fobj, infix, "dialects", false)) return false;
	return true;
}

// Form_CustomValidate event
fdialectsgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fdialectsgrid.ValidateRequired = true;
<?php } else { ?>
fdialectsgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fdialectsgrid.Lists["x_language_id"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x__language","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<?php } ?>
<?php if ($dialects->getCurrentMasterTable() == "" && $dialects_grid->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $dialects_grid->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
if ($dialects->CurrentAction == "gridadd") {
	if ($dialects->CurrentMode == "copy") {
		$bSelectLimit = EW_SELECT_LIMIT;
		if ($bSelectLimit) {
			$dialects_grid->TotalRecs = $dialects->SelectRecordCount();
			$dialects_grid->Recordset = $dialects_grid->LoadRecordset($dialects_grid->StartRec-1, $dialects_grid->DisplayRecs);
		} else {
			if ($dialects_grid->Recordset = $dialects_grid->LoadRecordset())
				$dialects_grid->TotalRecs = $dialects_grid->Recordset->RecordCount();
		}
		$dialects_grid->StartRec = 1;
		$dialects_grid->DisplayRecs = $dialects_grid->TotalRecs;
	} else {
		$dialects->CurrentFilter = "0=1";
		$dialects_grid->StartRec = 1;
		$dialects_grid->DisplayRecs = $dialects->GridAddRowCount;
	}
	$dialects_grid->TotalRecs = $dialects_grid->DisplayRecs;
	$dialects_grid->StopRec = $dialects_grid->DisplayRecs;
} else {
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$dialects_grid->TotalRecs = $dialects->SelectRecordCount();
	} else {
		if ($dialects_grid->Recordset = $dialects_grid->LoadRecordset())
			$dialects_grid->TotalRecs = $dialects_grid->Recordset->RecordCount();
	}
	$dialects_grid->StartRec = 1;
	$dialects_grid->DisplayRecs = $dialects_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$dialects_grid->Recordset = $dialects_grid->LoadRecordset($dialects_grid->StartRec-1, $dialects_grid->DisplayRecs);
}
$dialects_grid->RenderOtherOptions();
?>
<?php $dialects_grid->ShowPageHeader(); ?>
<?php
$dialects_grid->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div id="fdialectsgrid" class="ewForm form-horizontal">
<div id="gmp_dialects" class="ewGridMiddlePanel">
<table id="tbl_dialectsgrid" class="ewTable ewTableSeparate">
<?php echo $dialects->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$dialects_grid->RenderListOptions();

// Render list options (header, left)
$dialects_grid->ListOptions->Render("header", "left");
?>
<?php if ($dialects->id->Visible) { // id ?>
	<?php if ($dialects->SortUrl($dialects->id) == "") { ?>
		<td><div id="elh_dialects_id" class="dialects_id"><div class="ewTableHeaderCaption"><?php echo $dialects->id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_dialects_id" class="dialects_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $dialects->id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($dialects->id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($dialects->id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($dialects->language_id->Visible) { // language_id ?>
	<?php if ($dialects->SortUrl($dialects->language_id) == "") { ?>
		<td><div id="elh_dialects_language_id" class="dialects_language_id"><div class="ewTableHeaderCaption"><?php echo $dialects->language_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_dialects_language_id" class="dialects_language_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $dialects->language_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($dialects->language_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($dialects->language_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($dialects->dialects->Visible) { // dialects ?>
	<?php if ($dialects->SortUrl($dialects->dialects) == "") { ?>
		<td><div id="elh_dialects_dialects" class="dialects_dialects"><div class="ewTableHeaderCaption"><?php echo $dialects->dialects->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_dialects_dialects" class="dialects_dialects">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $dialects->dialects->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($dialects->dialects->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($dialects->dialects->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$dialects_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$dialects_grid->StartRec = 1;
$dialects_grid->StopRec = $dialects_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($dialects_grid->FormKeyCountName) && ($dialects->CurrentAction == "gridadd" || $dialects->CurrentAction == "gridedit" || $dialects->CurrentAction == "F")) {
		$dialects_grid->KeyCount = $objForm->GetValue($dialects_grid->FormKeyCountName);
		$dialects_grid->StopRec = $dialects_grid->StartRec + $dialects_grid->KeyCount - 1;
	}
}
$dialects_grid->RecCnt = $dialects_grid->StartRec - 1;
if ($dialects_grid->Recordset && !$dialects_grid->Recordset->EOF) {
	$dialects_grid->Recordset->MoveFirst();
	if (!$bSelectLimit && $dialects_grid->StartRec > 1)
		$dialects_grid->Recordset->Move($dialects_grid->StartRec - 1);
} elseif (!$dialects->AllowAddDeleteRow && $dialects_grid->StopRec == 0) {
	$dialects_grid->StopRec = $dialects->GridAddRowCount;
}

// Initialize aggregate
$dialects->RowType = EW_ROWTYPE_AGGREGATEINIT;
$dialects->ResetAttrs();
$dialects_grid->RenderRow();
if ($dialects->CurrentAction == "gridadd")
	$dialects_grid->RowIndex = 0;
if ($dialects->CurrentAction == "gridedit")
	$dialects_grid->RowIndex = 0;
while ($dialects_grid->RecCnt < $dialects_grid->StopRec) {
	$dialects_grid->RecCnt++;
	if (intval($dialects_grid->RecCnt) >= intval($dialects_grid->StartRec)) {
		$dialects_grid->RowCnt++;
		if ($dialects->CurrentAction == "gridadd" || $dialects->CurrentAction == "gridedit" || $dialects->CurrentAction == "F") {
			$dialects_grid->RowIndex++;
			$objForm->Index = $dialects_grid->RowIndex;
			if ($objForm->HasValue($dialects_grid->FormActionName))
				$dialects_grid->RowAction = strval($objForm->GetValue($dialects_grid->FormActionName));
			elseif ($dialects->CurrentAction == "gridadd")
				$dialects_grid->RowAction = "insert";
			else
				$dialects_grid->RowAction = "";
		}

		// Set up key count
		$dialects_grid->KeyCount = $dialects_grid->RowIndex;

		// Init row class and style
		$dialects->ResetAttrs();
		$dialects->CssClass = "";
		if ($dialects->CurrentAction == "gridadd") {
			if ($dialects->CurrentMode == "copy") {
				$dialects_grid->LoadRowValues($dialects_grid->Recordset); // Load row values
				$dialects_grid->SetRecordKey($dialects_grid->RowOldKey, $dialects_grid->Recordset); // Set old record key
			} else {
				$dialects_grid->LoadDefaultValues(); // Load default values
				$dialects_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$dialects_grid->LoadRowValues($dialects_grid->Recordset); // Load row values
		}
		$dialects->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($dialects->CurrentAction == "gridadd") // Grid add
			$dialects->RowType = EW_ROWTYPE_ADD; // Render add
		if ($dialects->CurrentAction == "gridadd" && $dialects->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$dialects_grid->RestoreCurrentRowFormValues($dialects_grid->RowIndex); // Restore form values
		if ($dialects->CurrentAction == "gridedit") { // Grid edit
			if ($dialects->EventCancelled) {
				$dialects_grid->RestoreCurrentRowFormValues($dialects_grid->RowIndex); // Restore form values
			}
			if ($dialects_grid->RowAction == "insert")
				$dialects->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$dialects->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($dialects->CurrentAction == "gridedit" && ($dialects->RowType == EW_ROWTYPE_EDIT || $dialects->RowType == EW_ROWTYPE_ADD) && $dialects->EventCancelled) // Update failed
			$dialects_grid->RestoreCurrentRowFormValues($dialects_grid->RowIndex); // Restore form values
		if ($dialects->RowType == EW_ROWTYPE_EDIT) // Edit row
			$dialects_grid->EditRowCnt++;
		if ($dialects->CurrentAction == "F") // Confirm row
			$dialects_grid->RestoreCurrentRowFormValues($dialects_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$dialects->RowAttrs = array_merge($dialects->RowAttrs, array('data-rowindex'=>$dialects_grid->RowCnt, 'id'=>'r' . $dialects_grid->RowCnt . '_dialects', 'data-rowtype'=>$dialects->RowType));

		// Render row
		$dialects_grid->RenderRow();

		// Render list options
		$dialects_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($dialects_grid->RowAction <> "delete" && $dialects_grid->RowAction <> "insertdelete" && !($dialects_grid->RowAction == "insert" && $dialects->CurrentAction == "F" && $dialects_grid->EmptyRow())) {
?>
	<tr<?php echo $dialects->RowAttributes() ?>>
<?php

// Render list options (body, left)
$dialects_grid->ListOptions->Render("body", "left", $dialects_grid->RowCnt);
?>
	<?php if ($dialects->id->Visible) { // id ?>
		<td<?php echo $dialects->id->CellAttributes() ?>>
<?php if ($dialects->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_id" name="o<?php echo $dialects_grid->RowIndex ?>_id" id="o<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->OldValue) ?>">
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $dialects_grid->RowCnt ?>_dialects_id" class="control-group dialects_id">
<span<?php echo $dialects->id->ViewAttributes() ?>>
<?php echo $dialects->id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x<?php echo $dialects_grid->RowIndex ?>_id" id="x<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->CurrentValue) ?>">
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $dialects->id->ViewAttributes() ?>>
<?php echo $dialects->id->ListViewValue() ?></span>
<input type="hidden" data-field="x_id" name="x<?php echo $dialects_grid->RowIndex ?>_id" id="x<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->FormValue) ?>">
<input type="hidden" data-field="x_id" name="o<?php echo $dialects_grid->RowIndex ?>_id" id="o<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->OldValue) ?>">
<?php } ?>
<a id="<?php echo $dialects_grid->PageObjName . "_row_" . $dialects_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($dialects->language_id->Visible) { // language_id ?>
		<td<?php echo $dialects->language_id->CellAttributes() ?>>
<?php if ($dialects->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<?php if ($dialects->language_id->getSessionValue() <> "") { ?>
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_language_id" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id"<?php echo $dialects->language_id->EditAttributes() ?>>
<?php
if (is_array($dialects->language_id->EditValue)) {
	$arwrk = $dialects->language_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($dialects->language_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $dialects->language_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `language` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `language`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $dialects->Lookup_Selecting($dialects->language_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" id="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<input type="hidden" data-field="x_language_id" name="o<?php echo $dialects_grid->RowIndex ?>_language_id" id="o<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->OldValue) ?>">
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php if ($dialects->language_id->getSessionValue() <> "") { ?>
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_language_id" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id"<?php echo $dialects->language_id->EditAttributes() ?>>
<?php
if (is_array($dialects->language_id->EditValue)) {
	$arwrk = $dialects->language_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($dialects->language_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $dialects->language_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `language` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `language`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $dialects->Lookup_Selecting($dialects->language_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" id="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ListViewValue() ?></span>
<input type="hidden" data-field="x_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->FormValue) ?>">
<input type="hidden" data-field="x_language_id" name="o<?php echo $dialects_grid->RowIndex ?>_language_id" id="o<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->OldValue) ?>">
<?php } ?>
<a id="<?php echo $dialects_grid->PageObjName . "_row_" . $dialects_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($dialects->dialects->Visible) { // dialects ?>
		<td<?php echo $dialects->dialects->CellAttributes() ?>>
<?php if ($dialects->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $dialects_grid->RowCnt ?>_dialects_dialects" class="control-group dialects_dialects">
<input type="text" data-field="x_dialects" name="x<?php echo $dialects_grid->RowIndex ?>_dialects" id="x<?php echo $dialects_grid->RowIndex ?>_dialects" size="30" maxlength="45" placeholder="<?php echo $dialects->dialects->PlaceHolder ?>" value="<?php echo $dialects->dialects->EditValue ?>"<?php echo $dialects->dialects->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_dialects" name="o<?php echo $dialects_grid->RowIndex ?>_dialects" id="o<?php echo $dialects_grid->RowIndex ?>_dialects" value="<?php echo ew_HtmlEncode($dialects->dialects->OldValue) ?>">
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $dialects_grid->RowCnt ?>_dialects_dialects" class="control-group dialects_dialects">
<input type="text" data-field="x_dialects" name="x<?php echo $dialects_grid->RowIndex ?>_dialects" id="x<?php echo $dialects_grid->RowIndex ?>_dialects" size="30" maxlength="45" placeholder="<?php echo $dialects->dialects->PlaceHolder ?>" value="<?php echo $dialects->dialects->EditValue ?>"<?php echo $dialects->dialects->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($dialects->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $dialects->dialects->ViewAttributes() ?>>
<?php echo $dialects->dialects->ListViewValue() ?></span>
<input type="hidden" data-field="x_dialects" name="x<?php echo $dialects_grid->RowIndex ?>_dialects" id="x<?php echo $dialects_grid->RowIndex ?>_dialects" value="<?php echo ew_HtmlEncode($dialects->dialects->FormValue) ?>">
<input type="hidden" data-field="x_dialects" name="o<?php echo $dialects_grid->RowIndex ?>_dialects" id="o<?php echo $dialects_grid->RowIndex ?>_dialects" value="<?php echo ew_HtmlEncode($dialects->dialects->OldValue) ?>">
<?php } ?>
<a id="<?php echo $dialects_grid->PageObjName . "_row_" . $dialects_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$dialects_grid->ListOptions->Render("body", "right", $dialects_grid->RowCnt);
?>
	</tr>
<?php if ($dialects->RowType == EW_ROWTYPE_ADD || $dialects->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fdialectsgrid.UpdateOpts(<?php echo $dialects_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($dialects->CurrentAction <> "gridadd" || $dialects->CurrentMode == "copy")
		if (!$dialects_grid->Recordset->EOF) $dialects_grid->Recordset->MoveNext();
}
?>
<?php
	if ($dialects->CurrentMode == "add" || $dialects->CurrentMode == "copy" || $dialects->CurrentMode == "edit") {
		$dialects_grid->RowIndex = '$rowindex$';
		$dialects_grid->LoadDefaultValues();

		// Set row properties
		$dialects->ResetAttrs();
		$dialects->RowAttrs = array_merge($dialects->RowAttrs, array('data-rowindex'=>$dialects_grid->RowIndex, 'id'=>'r0_dialects', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($dialects->RowAttrs["class"], "ewTemplate");
		$dialects->RowType = EW_ROWTYPE_ADD;

		// Render row
		$dialects_grid->RenderRow();

		// Render list options
		$dialects_grid->RenderListOptions();
		$dialects_grid->StartRowCnt = 0;
?>
	<tr<?php echo $dialects->RowAttributes() ?>>
<?php

// Render list options (body, left)
$dialects_grid->ListOptions->Render("body", "left", $dialects_grid->RowIndex);
?>
	<?php if ($dialects->id->Visible) { // id ?>
		<td>
<?php if ($dialects->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_dialects_id" class="control-group dialects_id">
<span<?php echo $dialects->id->ViewAttributes() ?>>
<?php echo $dialects->id->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x<?php echo $dialects_grid->RowIndex ?>_id" id="x<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_id" name="o<?php echo $dialects_grid->RowIndex ?>_id" id="o<?php echo $dialects_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($dialects->id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($dialects->language_id->Visible) { // language_id ?>
		<td>
<?php if ($dialects->CurrentAction <> "F") { ?>
<?php if ($dialects->language_id->getSessionValue() <> "") { ?>
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_language_id" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id"<?php echo $dialects->language_id->EditAttributes() ?>>
<?php
if (is_array($dialects->language_id->EditValue)) {
	$arwrk = $dialects->language_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($dialects->language_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $dialects->language_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `language` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `language`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $dialects->Lookup_Selecting($dialects->language_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" id="s_x<?php echo $dialects_grid->RowIndex ?>_language_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<?php } else { ?>
<span<?php echo $dialects->language_id->ViewAttributes() ?>>
<?php echo $dialects->language_id->ViewValue ?></span>
<input type="hidden" data-field="x_language_id" name="x<?php echo $dialects_grid->RowIndex ?>_language_id" id="x<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_language_id" name="o<?php echo $dialects_grid->RowIndex ?>_language_id" id="o<?php echo $dialects_grid->RowIndex ?>_language_id" value="<?php echo ew_HtmlEncode($dialects->language_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($dialects->dialects->Visible) { // dialects ?>
		<td>
<?php if ($dialects->CurrentAction <> "F") { ?>
<span id="el$rowindex$_dialects_dialects" class="control-group dialects_dialects">
<input type="text" data-field="x_dialects" name="x<?php echo $dialects_grid->RowIndex ?>_dialects" id="x<?php echo $dialects_grid->RowIndex ?>_dialects" size="30" maxlength="45" placeholder="<?php echo $dialects->dialects->PlaceHolder ?>" value="<?php echo $dialects->dialects->EditValue ?>"<?php echo $dialects->dialects->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_dialects_dialects" class="control-group dialects_dialects">
<span<?php echo $dialects->dialects->ViewAttributes() ?>>
<?php echo $dialects->dialects->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_dialects" name="x<?php echo $dialects_grid->RowIndex ?>_dialects" id="x<?php echo $dialects_grid->RowIndex ?>_dialects" value="<?php echo ew_HtmlEncode($dialects->dialects->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_dialects" name="o<?php echo $dialects_grid->RowIndex ?>_dialects" id="o<?php echo $dialects_grid->RowIndex ?>_dialects" value="<?php echo ew_HtmlEncode($dialects->dialects->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$dialects_grid->ListOptions->Render("body", "right", $dialects_grid->RowCnt);
?>
<script type="text/javascript">
fdialectsgrid.UpdateOpts(<?php echo $dialects_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($dialects->CurrentMode == "add" || $dialects->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $dialects_grid->FormKeyCountName ?>" id="<?php echo $dialects_grid->FormKeyCountName ?>" value="<?php echo $dialects_grid->KeyCount ?>">
<?php echo $dialects_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($dialects->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $dialects_grid->FormKeyCountName ?>" id="<?php echo $dialects_grid->FormKeyCountName ?>" value="<?php echo $dialects_grid->KeyCount ?>">
<?php echo $dialects_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($dialects->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fdialectsgrid">
</div>
<?php

// Close recordset
if ($dialects_grid->Recordset)
	$dialects_grid->Recordset->Close();
?>
<?php if ($dialects_grid->ShowOtherOptions) { ?>
<div class="ewGridLowerPanel ewListOtherOptions">
<?php
	foreach ($dialects_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<?php } ?>
</div>
</td></tr></table>
<?php if ($dialects->Export == "") { ?>
<script type="text/javascript">
fdialectsgrid.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$dialects_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$dialects_grid->Page_Terminate();
?>
