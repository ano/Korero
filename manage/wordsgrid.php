<?php

// Create page object
if (!isset($words_grid)) $words_grid = new cwords_grid();

// Page init
$words_grid->Page_Init();

// Page main
$words_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$words_grid->Page_Render();
?>
<?php if ($words->Export == "") { ?>
<script type="text/javascript">

// Page object
var words_grid = new ew_Page("words_grid");
words_grid.PageID = "grid"; // Page ID
var EW_PAGE_ID = words_grid.PageID; // For backward compatibility

// Form object
var fwordsgrid = new ew_Form("fwordsgrid");
fwordsgrid.FormKeyCountName = '<?php echo $words_grid->FormKeyCountName ?>';

// Validate form
fwordsgrid.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_maori");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($words->maori->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_english");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($words->english->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_dialects_id");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($words->dialects_id->FldCaption()) ?>");

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
fwordsgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "maori", false)) return false;
	if (ew_ValueChanged(fobj, infix, "english", false)) return false;
	if (ew_ValueChanged(fobj, infix, "dialects_id", false)) return false;
	if (ew_ValueChanged(fobj, infix, "tags", false)) return false;
	return true;
}

// Form_CustomValidate event
fwordsgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fwordsgrid.ValidateRequired = true;
<?php } else { ?>
fwordsgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fwordsgrid.Lists["x_dialects_id"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_dialects","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<?php } ?>
<?php if ($words->getCurrentMasterTable() == "" && $words_grid->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $words_grid->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
if ($words->CurrentAction == "gridadd") {
	if ($words->CurrentMode == "copy") {
		$bSelectLimit = EW_SELECT_LIMIT;
		if ($bSelectLimit) {
			$words_grid->TotalRecs = $words->SelectRecordCount();
			$words_grid->Recordset = $words_grid->LoadRecordset($words_grid->StartRec-1, $words_grid->DisplayRecs);
		} else {
			if ($words_grid->Recordset = $words_grid->LoadRecordset())
				$words_grid->TotalRecs = $words_grid->Recordset->RecordCount();
		}
		$words_grid->StartRec = 1;
		$words_grid->DisplayRecs = $words_grid->TotalRecs;
	} else {
		$words->CurrentFilter = "0=1";
		$words_grid->StartRec = 1;
		$words_grid->DisplayRecs = $words->GridAddRowCount;
	}
	$words_grid->TotalRecs = $words_grid->DisplayRecs;
	$words_grid->StopRec = $words_grid->DisplayRecs;
} else {
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$words_grid->TotalRecs = $words->SelectRecordCount();
	} else {
		if ($words_grid->Recordset = $words_grid->LoadRecordset())
			$words_grid->TotalRecs = $words_grid->Recordset->RecordCount();
	}
	$words_grid->StartRec = 1;
	$words_grid->DisplayRecs = $words_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$words_grid->Recordset = $words_grid->LoadRecordset($words_grid->StartRec-1, $words_grid->DisplayRecs);
}
$words_grid->RenderOtherOptions();
?>
<?php $words_grid->ShowPageHeader(); ?>
<?php
$words_grid->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div id="fwordsgrid" class="ewForm form-horizontal">
<div id="gmp_words" class="ewGridMiddlePanel">
<table id="tbl_wordsgrid" class="ewTable ewTableSeparate">
<?php echo $words->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$words_grid->RenderListOptions();

// Render list options (header, left)
$words_grid->ListOptions->Render("header", "left");
?>
<?php if ($words->id->Visible) { // id ?>
	<?php if ($words->SortUrl($words->id) == "") { ?>
		<td><div id="elh_words_id" class="words_id"><div class="ewTableHeaderCaption"><?php echo $words->id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_words_id" class="words_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $words->id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($words->id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($words->id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($words->maori->Visible) { // maori ?>
	<?php if ($words->SortUrl($words->maori) == "") { ?>
		<td><div id="elh_words_maori" class="words_maori"><div class="ewTableHeaderCaption"><?php echo $words->maori->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_words_maori" class="words_maori">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $words->maori->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($words->maori->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($words->maori->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($words->english->Visible) { // english ?>
	<?php if ($words->SortUrl($words->english) == "") { ?>
		<td><div id="elh_words_english" class="words_english"><div class="ewTableHeaderCaption"><?php echo $words->english->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_words_english" class="words_english">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $words->english->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($words->english->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($words->english->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($words->dialects_id->Visible) { // dialects_id ?>
	<?php if ($words->SortUrl($words->dialects_id) == "") { ?>
		<td><div id="elh_words_dialects_id" class="words_dialects_id"><div class="ewTableHeaderCaption"><?php echo $words->dialects_id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_words_dialects_id" class="words_dialects_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $words->dialects_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($words->dialects_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($words->dialects_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($words->tags->Visible) { // tags ?>
	<?php if ($words->SortUrl($words->tags) == "") { ?>
		<td><div id="elh_words_tags" class="words_tags"><div class="ewTableHeaderCaption"><?php echo $words->tags->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_words_tags" class="words_tags">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $words->tags->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($words->tags->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($words->tags->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$words_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$words_grid->StartRec = 1;
$words_grid->StopRec = $words_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($words_grid->FormKeyCountName) && ($words->CurrentAction == "gridadd" || $words->CurrentAction == "gridedit" || $words->CurrentAction == "F")) {
		$words_grid->KeyCount = $objForm->GetValue($words_grid->FormKeyCountName);
		$words_grid->StopRec = $words_grid->StartRec + $words_grid->KeyCount - 1;
	}
}
$words_grid->RecCnt = $words_grid->StartRec - 1;
if ($words_grid->Recordset && !$words_grid->Recordset->EOF) {
	$words_grid->Recordset->MoveFirst();
	if (!$bSelectLimit && $words_grid->StartRec > 1)
		$words_grid->Recordset->Move($words_grid->StartRec - 1);
} elseif (!$words->AllowAddDeleteRow && $words_grid->StopRec == 0) {
	$words_grid->StopRec = $words->GridAddRowCount;
}

// Initialize aggregate
$words->RowType = EW_ROWTYPE_AGGREGATEINIT;
$words->ResetAttrs();
$words_grid->RenderRow();
if ($words->CurrentAction == "gridadd")
	$words_grid->RowIndex = 0;
if ($words->CurrentAction == "gridedit")
	$words_grid->RowIndex = 0;
while ($words_grid->RecCnt < $words_grid->StopRec) {
	$words_grid->RecCnt++;
	if (intval($words_grid->RecCnt) >= intval($words_grid->StartRec)) {
		$words_grid->RowCnt++;
		if ($words->CurrentAction == "gridadd" || $words->CurrentAction == "gridedit" || $words->CurrentAction == "F") {
			$words_grid->RowIndex++;
			$objForm->Index = $words_grid->RowIndex;
			if ($objForm->HasValue($words_grid->FormActionName))
				$words_grid->RowAction = strval($objForm->GetValue($words_grid->FormActionName));
			elseif ($words->CurrentAction == "gridadd")
				$words_grid->RowAction = "insert";
			else
				$words_grid->RowAction = "";
		}

		// Set up key count
		$words_grid->KeyCount = $words_grid->RowIndex;

		// Init row class and style
		$words->ResetAttrs();
		$words->CssClass = "";
		if ($words->CurrentAction == "gridadd") {
			if ($words->CurrentMode == "copy") {
				$words_grid->LoadRowValues($words_grid->Recordset); // Load row values
				$words_grid->SetRecordKey($words_grid->RowOldKey, $words_grid->Recordset); // Set old record key
			} else {
				$words_grid->LoadDefaultValues(); // Load default values
				$words_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$words_grid->LoadRowValues($words_grid->Recordset); // Load row values
		}
		$words->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($words->CurrentAction == "gridadd") // Grid add
			$words->RowType = EW_ROWTYPE_ADD; // Render add
		if ($words->CurrentAction == "gridadd" && $words->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$words_grid->RestoreCurrentRowFormValues($words_grid->RowIndex); // Restore form values
		if ($words->CurrentAction == "gridedit") { // Grid edit
			if ($words->EventCancelled) {
				$words_grid->RestoreCurrentRowFormValues($words_grid->RowIndex); // Restore form values
			}
			if ($words_grid->RowAction == "insert")
				$words->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$words->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($words->CurrentAction == "gridedit" && ($words->RowType == EW_ROWTYPE_EDIT || $words->RowType == EW_ROWTYPE_ADD) && $words->EventCancelled) // Update failed
			$words_grid->RestoreCurrentRowFormValues($words_grid->RowIndex); // Restore form values
		if ($words->RowType == EW_ROWTYPE_EDIT) // Edit row
			$words_grid->EditRowCnt++;
		if ($words->CurrentAction == "F") // Confirm row
			$words_grid->RestoreCurrentRowFormValues($words_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$words->RowAttrs = array_merge($words->RowAttrs, array('data-rowindex'=>$words_grid->RowCnt, 'id'=>'r' . $words_grid->RowCnt . '_words', 'data-rowtype'=>$words->RowType));

		// Render row
		$words_grid->RenderRow();

		// Render list options
		$words_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($words_grid->RowAction <> "delete" && $words_grid->RowAction <> "insertdelete" && !($words_grid->RowAction == "insert" && $words->CurrentAction == "F" && $words_grid->EmptyRow())) {
?>
	<tr<?php echo $words->RowAttributes() ?>>
<?php

// Render list options (body, left)
$words_grid->ListOptions->Render("body", "left", $words_grid->RowCnt);
?>
	<?php if ($words->id->Visible) { // id ?>
		<td<?php echo $words->id->CellAttributes() ?>>
<?php if ($words->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_id" name="o<?php echo $words_grid->RowIndex ?>_id" id="o<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->OldValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_id" class="control-group words_id">
<span<?php echo $words->id->ViewAttributes() ?>>
<?php echo $words->id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x<?php echo $words_grid->RowIndex ?>_id" id="x<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->CurrentValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $words->id->ViewAttributes() ?>>
<?php echo $words->id->ListViewValue() ?></span>
<input type="hidden" data-field="x_id" name="x<?php echo $words_grid->RowIndex ?>_id" id="x<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->FormValue) ?>">
<input type="hidden" data-field="x_id" name="o<?php echo $words_grid->RowIndex ?>_id" id="o<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->OldValue) ?>">
<?php } ?>
<a id="<?php echo $words_grid->PageObjName . "_row_" . $words_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($words->maori->Visible) { // maori ?>
		<td<?php echo $words->maori->CellAttributes() ?>>
<?php if ($words->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_maori" class="control-group words_maori">
<input type="text" data-field="x_maori" name="x<?php echo $words_grid->RowIndex ?>_maori" id="x<?php echo $words_grid->RowIndex ?>_maori" size="30" maxlength="45" placeholder="<?php echo $words->maori->PlaceHolder ?>" value="<?php echo $words->maori->EditValue ?>"<?php echo $words->maori->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_maori" name="o<?php echo $words_grid->RowIndex ?>_maori" id="o<?php echo $words_grid->RowIndex ?>_maori" value="<?php echo ew_HtmlEncode($words->maori->OldValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_maori" class="control-group words_maori">
<input type="text" data-field="x_maori" name="x<?php echo $words_grid->RowIndex ?>_maori" id="x<?php echo $words_grid->RowIndex ?>_maori" size="30" maxlength="45" placeholder="<?php echo $words->maori->PlaceHolder ?>" value="<?php echo $words->maori->EditValue ?>"<?php echo $words->maori->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $words->maori->ViewAttributes() ?>>
<?php echo $words->maori->ListViewValue() ?></span>
<input type="hidden" data-field="x_maori" name="x<?php echo $words_grid->RowIndex ?>_maori" id="x<?php echo $words_grid->RowIndex ?>_maori" value="<?php echo ew_HtmlEncode($words->maori->FormValue) ?>">
<input type="hidden" data-field="x_maori" name="o<?php echo $words_grid->RowIndex ?>_maori" id="o<?php echo $words_grid->RowIndex ?>_maori" value="<?php echo ew_HtmlEncode($words->maori->OldValue) ?>">
<?php } ?>
<a id="<?php echo $words_grid->PageObjName . "_row_" . $words_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($words->english->Visible) { // english ?>
		<td<?php echo $words->english->CellAttributes() ?>>
<?php if ($words->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_english" class="control-group words_english">
<textarea data-field="x_english" name="x<?php echo $words_grid->RowIndex ?>_english" id="x<?php echo $words_grid->RowIndex ?>_english" cols="35" rows="4" placeholder="<?php echo $words->english->PlaceHolder ?>"<?php echo $words->english->EditAttributes() ?>><?php echo $words->english->EditValue ?></textarea>
</span>
<input type="hidden" data-field="x_english" name="o<?php echo $words_grid->RowIndex ?>_english" id="o<?php echo $words_grid->RowIndex ?>_english" value="<?php echo ew_HtmlEncode($words->english->OldValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_english" class="control-group words_english">
<textarea data-field="x_english" name="x<?php echo $words_grid->RowIndex ?>_english" id="x<?php echo $words_grid->RowIndex ?>_english" cols="35" rows="4" placeholder="<?php echo $words->english->PlaceHolder ?>"<?php echo $words->english->EditAttributes() ?>><?php echo $words->english->EditValue ?></textarea>
</span>
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $words->english->ViewAttributes() ?>>
<?php echo $words->english->ListViewValue() ?></span>
<input type="hidden" data-field="x_english" name="x<?php echo $words_grid->RowIndex ?>_english" id="x<?php echo $words_grid->RowIndex ?>_english" value="<?php echo ew_HtmlEncode($words->english->FormValue) ?>">
<input type="hidden" data-field="x_english" name="o<?php echo $words_grid->RowIndex ?>_english" id="o<?php echo $words_grid->RowIndex ?>_english" value="<?php echo ew_HtmlEncode($words->english->OldValue) ?>">
<?php } ?>
<a id="<?php echo $words_grid->PageObjName . "_row_" . $words_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($words->dialects_id->Visible) { // dialects_id ?>
		<td<?php echo $words->dialects_id->CellAttributes() ?>>
<?php if ($words->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<?php if ($words->dialects_id->getSessionValue() <> "") { ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_dialects_id" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id"<?php echo $words->dialects_id->EditAttributes() ?>>
<?php
if (is_array($words->dialects_id->EditValue)) {
	$arwrk = $words->dialects_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($words->dialects_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $words->dialects_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dialects`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $words->Lookup_Selecting($words->dialects_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" id="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<input type="hidden" data-field="x_dialects_id" name="o<?php echo $words_grid->RowIndex ?>_dialects_id" id="o<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->OldValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php if ($words->dialects_id->getSessionValue() <> "") { ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_dialects_id" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id"<?php echo $words->dialects_id->EditAttributes() ?>>
<?php
if (is_array($words->dialects_id->EditValue)) {
	$arwrk = $words->dialects_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($words->dialects_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $words->dialects_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dialects`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $words->Lookup_Selecting($words->dialects_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" id="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ListViewValue() ?></span>
<input type="hidden" data-field="x_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->FormValue) ?>">
<input type="hidden" data-field="x_dialects_id" name="o<?php echo $words_grid->RowIndex ?>_dialects_id" id="o<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->OldValue) ?>">
<?php } ?>
<a id="<?php echo $words_grid->PageObjName . "_row_" . $words_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($words->tags->Visible) { // tags ?>
		<td<?php echo $words->tags->CellAttributes() ?>>
<?php if ($words->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_tags" class="control-group words_tags">
<textarea data-field="x_tags" name="x<?php echo $words_grid->RowIndex ?>_tags" id="x<?php echo $words_grid->RowIndex ?>_tags" cols="30" rows="4" placeholder="<?php echo $words->tags->PlaceHolder ?>"<?php echo $words->tags->EditAttributes() ?>><?php echo $words->tags->EditValue ?></textarea>
</span>
<input type="hidden" data-field="x_tags" name="o<?php echo $words_grid->RowIndex ?>_tags" id="o<?php echo $words_grid->RowIndex ?>_tags" value="<?php echo ew_HtmlEncode($words->tags->OldValue) ?>">
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $words_grid->RowCnt ?>_words_tags" class="control-group words_tags">
<textarea data-field="x_tags" name="x<?php echo $words_grid->RowIndex ?>_tags" id="x<?php echo $words_grid->RowIndex ?>_tags" cols="30" rows="4" placeholder="<?php echo $words->tags->PlaceHolder ?>"<?php echo $words->tags->EditAttributes() ?>><?php echo $words->tags->EditValue ?></textarea>
</span>
<?php } ?>
<?php if ($words->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $words->tags->ViewAttributes() ?>>
<?php echo $words->tags->ListViewValue() ?></span>
<input type="hidden" data-field="x_tags" name="x<?php echo $words_grid->RowIndex ?>_tags" id="x<?php echo $words_grid->RowIndex ?>_tags" value="<?php echo ew_HtmlEncode($words->tags->FormValue) ?>">
<input type="hidden" data-field="x_tags" name="o<?php echo $words_grid->RowIndex ?>_tags" id="o<?php echo $words_grid->RowIndex ?>_tags" value="<?php echo ew_HtmlEncode($words->tags->OldValue) ?>">
<?php } ?>
<a id="<?php echo $words_grid->PageObjName . "_row_" . $words_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$words_grid->ListOptions->Render("body", "right", $words_grid->RowCnt);
?>
	</tr>
<?php if ($words->RowType == EW_ROWTYPE_ADD || $words->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fwordsgrid.UpdateOpts(<?php echo $words_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($words->CurrentAction <> "gridadd" || $words->CurrentMode == "copy")
		if (!$words_grid->Recordset->EOF) $words_grid->Recordset->MoveNext();
}
?>
<?php
	if ($words->CurrentMode == "add" || $words->CurrentMode == "copy" || $words->CurrentMode == "edit") {
		$words_grid->RowIndex = '$rowindex$';
		$words_grid->LoadDefaultValues();

		// Set row properties
		$words->ResetAttrs();
		$words->RowAttrs = array_merge($words->RowAttrs, array('data-rowindex'=>$words_grid->RowIndex, 'id'=>'r0_words', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($words->RowAttrs["class"], "ewTemplate");
		$words->RowType = EW_ROWTYPE_ADD;

		// Render row
		$words_grid->RenderRow();

		// Render list options
		$words_grid->RenderListOptions();
		$words_grid->StartRowCnt = 0;
?>
	<tr<?php echo $words->RowAttributes() ?>>
<?php

// Render list options (body, left)
$words_grid->ListOptions->Render("body", "left", $words_grid->RowIndex);
?>
	<?php if ($words->id->Visible) { // id ?>
		<td>
<?php if ($words->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_words_id" class="control-group words_id">
<span<?php echo $words->id->ViewAttributes() ?>>
<?php echo $words->id->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x<?php echo $words_grid->RowIndex ?>_id" id="x<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_id" name="o<?php echo $words_grid->RowIndex ?>_id" id="o<?php echo $words_grid->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($words->id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($words->maori->Visible) { // maori ?>
		<td>
<?php if ($words->CurrentAction <> "F") { ?>
<span id="el$rowindex$_words_maori" class="control-group words_maori">
<input type="text" data-field="x_maori" name="x<?php echo $words_grid->RowIndex ?>_maori" id="x<?php echo $words_grid->RowIndex ?>_maori" size="30" maxlength="45" placeholder="<?php echo $words->maori->PlaceHolder ?>" value="<?php echo $words->maori->EditValue ?>"<?php echo $words->maori->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_words_maori" class="control-group words_maori">
<span<?php echo $words->maori->ViewAttributes() ?>>
<?php echo $words->maori->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_maori" name="x<?php echo $words_grid->RowIndex ?>_maori" id="x<?php echo $words_grid->RowIndex ?>_maori" value="<?php echo ew_HtmlEncode($words->maori->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_maori" name="o<?php echo $words_grid->RowIndex ?>_maori" id="o<?php echo $words_grid->RowIndex ?>_maori" value="<?php echo ew_HtmlEncode($words->maori->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($words->english->Visible) { // english ?>
		<td>
<?php if ($words->CurrentAction <> "F") { ?>
<span id="el$rowindex$_words_english" class="control-group words_english">
<textarea data-field="x_english" name="x<?php echo $words_grid->RowIndex ?>_english" id="x<?php echo $words_grid->RowIndex ?>_english" cols="35" rows="4" placeholder="<?php echo $words->english->PlaceHolder ?>"<?php echo $words->english->EditAttributes() ?>><?php echo $words->english->EditValue ?></textarea>
</span>
<?php } else { ?>
<span id="el$rowindex$_words_english" class="control-group words_english">
<span<?php echo $words->english->ViewAttributes() ?>>
<?php echo $words->english->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_english" name="x<?php echo $words_grid->RowIndex ?>_english" id="x<?php echo $words_grid->RowIndex ?>_english" value="<?php echo ew_HtmlEncode($words->english->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_english" name="o<?php echo $words_grid->RowIndex ?>_english" id="o<?php echo $words_grid->RowIndex ?>_english" value="<?php echo ew_HtmlEncode($words->english->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($words->dialects_id->Visible) { // dialects_id ?>
		<td>
<?php if ($words->CurrentAction <> "F") { ?>
<?php if ($words->dialects_id->getSessionValue() <> "") { ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_dialects_id" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id"<?php echo $words->dialects_id->EditAttributes() ?>>
<?php
if (is_array($words->dialects_id->EditValue)) {
	$arwrk = $words->dialects_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($words->dialects_id->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $words->dialects_id->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dialects`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $words->Lookup_Selecting($words->dialects_id, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" id="s_x<?php echo $words_grid->RowIndex ?>_dialects_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<?php } else { ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ViewValue ?></span>
<input type="hidden" data-field="x_dialects_id" name="x<?php echo $words_grid->RowIndex ?>_dialects_id" id="x<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_dialects_id" name="o<?php echo $words_grid->RowIndex ?>_dialects_id" id="o<?php echo $words_grid->RowIndex ?>_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($words->tags->Visible) { // tags ?>
		<td>
<?php if ($words->CurrentAction <> "F") { ?>
<span id="el$rowindex$_words_tags" class="control-group words_tags">
<textarea data-field="x_tags" name="x<?php echo $words_grid->RowIndex ?>_tags" id="x<?php echo $words_grid->RowIndex ?>_tags" cols="30" rows="4" placeholder="<?php echo $words->tags->PlaceHolder ?>"<?php echo $words->tags->EditAttributes() ?>><?php echo $words->tags->EditValue ?></textarea>
</span>
<?php } else { ?>
<span id="el$rowindex$_words_tags" class="control-group words_tags">
<span<?php echo $words->tags->ViewAttributes() ?>>
<?php echo $words->tags->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_tags" name="x<?php echo $words_grid->RowIndex ?>_tags" id="x<?php echo $words_grid->RowIndex ?>_tags" value="<?php echo ew_HtmlEncode($words->tags->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_tags" name="o<?php echo $words_grid->RowIndex ?>_tags" id="o<?php echo $words_grid->RowIndex ?>_tags" value="<?php echo ew_HtmlEncode($words->tags->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$words_grid->ListOptions->Render("body", "right", $words_grid->RowCnt);
?>
<script type="text/javascript">
fwordsgrid.UpdateOpts(<?php echo $words_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($words->CurrentMode == "add" || $words->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $words_grid->FormKeyCountName ?>" id="<?php echo $words_grid->FormKeyCountName ?>" value="<?php echo $words_grid->KeyCount ?>">
<?php echo $words_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($words->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $words_grid->FormKeyCountName ?>" id="<?php echo $words_grid->FormKeyCountName ?>" value="<?php echo $words_grid->KeyCount ?>">
<?php echo $words_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($words->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fwordsgrid">
</div>
<?php

// Close recordset
if ($words_grid->Recordset)
	$words_grid->Recordset->Close();
?>
<?php if ($words_grid->ShowOtherOptions) { ?>
<div class="ewGridLowerPanel ewListOtherOptions">
<?php
	foreach ($words_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<?php } ?>
</div>
</td></tr></table>
<?php if ($words->Export == "") { ?>
<script type="text/javascript">
fwordsgrid.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$words_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$words_grid->Page_Terminate();
?>
