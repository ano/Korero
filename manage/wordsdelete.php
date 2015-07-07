<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "wordsinfo.php" ?>
<?php include_once "dialectsinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$words_delete = NULL; // Initialize page object first

class cwords_delete extends cwords {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{FF0DFD12-B2E5-4AC7-8EC9-50E29151819F}";

	// Table name
	var $TableName = 'words';

	// Page object name
	var $PageObjName = 'words_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (words)
		if (!isset($GLOBALS["words"])) {
			$GLOBALS["words"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["words"];
		}

		// Table object (dialects)
		if (!isset($GLOBALS['dialects'])) $GLOBALS['dialects'] = new cdialects();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'words', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("wordslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in words class, wordsinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->id->setDbValue($rs->fields('id'));
		$this->maori->setDbValue($rs->fields('maori'));
		$this->english->setDbValue($rs->fields('english'));
		$this->description->setDbValue($rs->fields('description'));
		$this->dialects_id->setDbValue($rs->fields('dialects_id'));
		$this->tags->setDbValue($rs->fields('tags'));
		$this->date_added->setDbValue($rs->fields('date_added'));
		$this->date_updated->setDbValue($rs->fields('date_updated'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->maori->DbValue = $row['maori'];
		$this->english->DbValue = $row['english'];
		$this->description->DbValue = $row['description'];
		$this->dialects_id->DbValue = $row['dialects_id'];
		$this->tags->DbValue = $row['tags'];
		$this->date_added->DbValue = $row['date_added'];
		$this->date_updated->DbValue = $row['date_updated'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// maori
		// english
		// description
		// dialects_id
		// tags
		// date_added
		// date_updated

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// maori
			$this->maori->ViewValue = $this->maori->CurrentValue;
			$this->maori->ViewCustomAttributes = "";

			// english
			$this->english->ViewValue = $this->english->CurrentValue;
			$this->english->ViewCustomAttributes = "";

			// dialects_id
			if (strval($this->dialects_id->CurrentValue) <> "") {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->dialects_id->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dialects`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->dialects_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->dialects_id->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->dialects_id->ViewValue = $this->dialects_id->CurrentValue;
				}
			} else {
				$this->dialects_id->ViewValue = NULL;
			}
			$this->dialects_id->ViewCustomAttributes = "";

			// tags
			$this->tags->ViewValue = $this->tags->CurrentValue;
			$this->tags->ViewCustomAttributes = "";

			// date_added
			$this->date_added->ViewValue = $this->date_added->CurrentValue;
			$this->date_added->ViewValue = ew_FormatDateTime($this->date_added->ViewValue, 5);
			$this->date_added->ViewCustomAttributes = "";

			// date_updated
			$this->date_updated->ViewValue = $this->date_updated->CurrentValue;
			$this->date_updated->ViewValue = ew_FormatDateTime($this->date_updated->ViewValue, 5);
			$this->date_updated->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// maori
			$this->maori->LinkCustomAttributes = "";
			$this->maori->HrefValue = "";
			$this->maori->TooltipValue = "";

			// english
			$this->english->LinkCustomAttributes = "";
			$this->english->HrefValue = "";
			$this->english->TooltipValue = "";

			// dialects_id
			$this->dialects_id->LinkCustomAttributes = "";
			$this->dialects_id->HrefValue = "";
			$this->dialects_id->TooltipValue = "";

			// tags
			$this->tags->LinkCustomAttributes = "";
			$this->tags->HrefValue = "";
			$this->tags->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['id'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "wordslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($words_delete)) $words_delete = new cwords_delete();

// Page init
$words_delete->Page_Init();

// Page main
$words_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$words_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var words_delete = new ew_Page("words_delete");
words_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = words_delete.PageID; // For backward compatibility

// Form object
var fwordsdelete = new ew_Form("fwordsdelete");

// Form_CustomValidate event
fwordsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fwordsdelete.ValidateRequired = true;
<?php } else { ?>
fwordsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fwordsdelete.Lists["x_dialects_id"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_dialects","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($words_delete->Recordset = $words_delete->LoadRecordset())
	$words_deleteTotalRecs = $words_delete->Recordset->RecordCount(); // Get record count
if ($words_deleteTotalRecs <= 0) { // No record found, exit
	if ($words_delete->Recordset)
		$words_delete->Recordset->Close();
	$words_delete->Page_Terminate("wordslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $words_delete->ShowPageHeader(); ?>
<?php
$words_delete->ShowMessage();
?>
<form name="fwordsdelete" id="fwordsdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="words">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($words_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_wordsdelete" class="ewTable ewTableSeparate">
<?php echo $words->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($words->id->Visible) { // id ?>
		<td><span id="elh_words_id" class="words_id"><?php echo $words->id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($words->maori->Visible) { // maori ?>
		<td><span id="elh_words_maori" class="words_maori"><?php echo $words->maori->FldCaption() ?></span></td>
<?php } ?>
<?php if ($words->english->Visible) { // english ?>
		<td><span id="elh_words_english" class="words_english"><?php echo $words->english->FldCaption() ?></span></td>
<?php } ?>
<?php if ($words->dialects_id->Visible) { // dialects_id ?>
		<td><span id="elh_words_dialects_id" class="words_dialects_id"><?php echo $words->dialects_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($words->tags->Visible) { // tags ?>
		<td><span id="elh_words_tags" class="words_tags"><?php echo $words->tags->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$words_delete->RecCnt = 0;
$i = 0;
while (!$words_delete->Recordset->EOF) {
	$words_delete->RecCnt++;
	$words_delete->RowCnt++;

	// Set row properties
	$words->ResetAttrs();
	$words->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$words_delete->LoadRowValues($words_delete->Recordset);

	// Render row
	$words_delete->RenderRow();
?>
	<tr<?php echo $words->RowAttributes() ?>>
<?php if ($words->id->Visible) { // id ?>
		<td<?php echo $words->id->CellAttributes() ?>>
<span id="el<?php echo $words_delete->RowCnt ?>_words_id" class="control-group words_id">
<span<?php echo $words->id->ViewAttributes() ?>>
<?php echo $words->id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($words->maori->Visible) { // maori ?>
		<td<?php echo $words->maori->CellAttributes() ?>>
<span id="el<?php echo $words_delete->RowCnt ?>_words_maori" class="control-group words_maori">
<span<?php echo $words->maori->ViewAttributes() ?>>
<?php echo $words->maori->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($words->english->Visible) { // english ?>
		<td<?php echo $words->english->CellAttributes() ?>>
<span id="el<?php echo $words_delete->RowCnt ?>_words_english" class="control-group words_english">
<span<?php echo $words->english->ViewAttributes() ?>>
<?php echo $words->english->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($words->dialects_id->Visible) { // dialects_id ?>
		<td<?php echo $words->dialects_id->CellAttributes() ?>>
<span id="el<?php echo $words_delete->RowCnt ?>_words_dialects_id" class="control-group words_dialects_id">
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($words->tags->Visible) { // tags ?>
		<td<?php echo $words->tags->CellAttributes() ?>>
<span id="el<?php echo $words_delete->RowCnt ?>_words_tags" class="control-group words_tags">
<span<?php echo $words->tags->ViewAttributes() ?>>
<?php echo $words->tags->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$words_delete->Recordset->MoveNext();
}
$words_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fwordsdelete.Init();
</script>
<?php
$words_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$words_delete->Page_Terminate();
?>
