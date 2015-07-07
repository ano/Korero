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

$words_add = NULL; // Initialize page object first

class cwords_add extends cwords {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{FF0DFD12-B2E5-4AC7-8EC9-50E29151819F}";

	// Table name
	var $TableName = 'words';

	// Page object name
	var $PageObjName = 'words_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Set up master/detail parameters
		$this->SetUpMasterParms();

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["id"] != "") {
				$this->id->setQueryStringValue($_GET["id"]);
				$this->setKey("id", $this->id->CurrentValue); // Set up key
			} else {
				$this->setKey("id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("wordslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "wordsview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->maori->CurrentValue = NULL;
		$this->maori->OldValue = $this->maori->CurrentValue;
		$this->english->CurrentValue = NULL;
		$this->english->OldValue = $this->english->CurrentValue;
		$this->description->CurrentValue = NULL;
		$this->description->OldValue = $this->description->CurrentValue;
		$this->dialects_id->CurrentValue = NULL;
		$this->dialects_id->OldValue = $this->dialects_id->CurrentValue;
		$this->tags->CurrentValue = NULL;
		$this->tags->OldValue = $this->tags->CurrentValue;
		$this->date_added->CurrentValue = NULL;
		$this->date_added->OldValue = $this->date_added->CurrentValue;
		$this->date_updated->CurrentValue = date("Y-m-d H:i:s");
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->maori->FldIsDetailKey) {
			$this->maori->setFormValue($objForm->GetValue("x_maori"));
		}
		if (!$this->english->FldIsDetailKey) {
			$this->english->setFormValue($objForm->GetValue("x_english"));
		}
		if (!$this->description->FldIsDetailKey) {
			$this->description->setFormValue($objForm->GetValue("x_description"));
		}
		if (!$this->dialects_id->FldIsDetailKey) {
			$this->dialects_id->setFormValue($objForm->GetValue("x_dialects_id"));
		}
		if (!$this->tags->FldIsDetailKey) {
			$this->tags->setFormValue($objForm->GetValue("x_tags"));
		}
		if (!$this->date_added->FldIsDetailKey) {
			$this->date_added->setFormValue($objForm->GetValue("x_date_added"));
			$this->date_added->CurrentValue = ew_UnFormatDateTime($this->date_added->CurrentValue, 5);
		}
		if (!$this->date_updated->FldIsDetailKey) {
			$this->date_updated->setFormValue($objForm->GetValue("x_date_updated"));
			$this->date_updated->CurrentValue = ew_UnFormatDateTime($this->date_updated->CurrentValue, 5);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->maori->CurrentValue = $this->maori->FormValue;
		$this->english->CurrentValue = $this->english->FormValue;
		$this->description->CurrentValue = $this->description->FormValue;
		$this->dialects_id->CurrentValue = $this->dialects_id->FormValue;
		$this->tags->CurrentValue = $this->tags->FormValue;
		$this->date_added->CurrentValue = $this->date_added->FormValue;
		$this->date_added->CurrentValue = ew_UnFormatDateTime($this->date_added->CurrentValue, 5);
		$this->date_updated->CurrentValue = $this->date_updated->FormValue;
		$this->date_updated->CurrentValue = ew_UnFormatDateTime($this->date_updated->CurrentValue, 5);
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id")) <> "")
			$this->id->CurrentValue = $this->getKey("id"); // id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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

			// description
			$this->description->ViewValue = $this->description->CurrentValue;
			$this->description->ViewCustomAttributes = "";

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

			// maori
			$this->maori->LinkCustomAttributes = "";
			$this->maori->HrefValue = "";
			$this->maori->TooltipValue = "";

			// english
			$this->english->LinkCustomAttributes = "";
			$this->english->HrefValue = "";
			$this->english->TooltipValue = "";

			// description
			$this->description->LinkCustomAttributes = "";
			$this->description->HrefValue = "";
			$this->description->TooltipValue = "";

			// dialects_id
			$this->dialects_id->LinkCustomAttributes = "";
			$this->dialects_id->HrefValue = "";
			$this->dialects_id->TooltipValue = "";

			// tags
			$this->tags->LinkCustomAttributes = "";
			$this->tags->HrefValue = "";
			$this->tags->TooltipValue = "";

			// date_added
			$this->date_added->LinkCustomAttributes = "";
			$this->date_added->HrefValue = "";
			$this->date_added->TooltipValue = "";

			// date_updated
			$this->date_updated->LinkCustomAttributes = "";
			$this->date_updated->HrefValue = "";
			$this->date_updated->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// maori
			$this->maori->EditCustomAttributes = "";
			$this->maori->EditValue = ew_HtmlEncode($this->maori->CurrentValue);
			$this->maori->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->maori->FldCaption()));

			// english
			$this->english->EditCustomAttributes = "";
			$this->english->EditValue = $this->english->CurrentValue;
			$this->english->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->english->FldCaption()));

			// description
			$this->description->EditCustomAttributes = "";
			$this->description->EditValue = $this->description->CurrentValue;
			$this->description->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->description->FldCaption()));

			// dialects_id
			$this->dialects_id->EditCustomAttributes = "";
			if ($this->dialects_id->getSessionValue() <> "") {
				$this->dialects_id->CurrentValue = $this->dialects_id->getSessionValue();
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
			} else {
			if (trim(strval($this->dialects_id->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->dialects_id->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `dialects`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->dialects_id, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->dialects_id->EditValue = $arwrk;
			}

			// tags
			$this->tags->EditCustomAttributes = "";
			$this->tags->EditValue = $this->tags->CurrentValue;
			$this->tags->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->tags->FldCaption()));

			// date_added
			$this->date_added->EditCustomAttributes = "";
			$this->date_added->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->date_added->CurrentValue, 5));
			$this->date_added->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->date_added->FldCaption()));

			// date_updated
			$this->date_updated->EditCustomAttributes = "";
			$this->date_updated->CurrentValue = ew_FormatDateTime(date("Y-m-d H:i:s"), 5);

			// Edit refer script
			// maori

			$this->maori->HrefValue = "";

			// english
			$this->english->HrefValue = "";

			// description
			$this->description->HrefValue = "";

			// dialects_id
			$this->dialects_id->HrefValue = "";

			// tags
			$this->tags->HrefValue = "";

			// date_added
			$this->date_added->HrefValue = "";

			// date_updated
			$this->date_updated->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->maori->FldIsDetailKey && !is_null($this->maori->FormValue) && $this->maori->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->maori->FldCaption());
		}
		if (!$this->english->FldIsDetailKey && !is_null($this->english->FormValue) && $this->english->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->english->FldCaption());
		}
		if (!$this->dialects_id->FldIsDetailKey && !is_null($this->dialects_id->FormValue) && $this->dialects_id->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->dialects_id->FldCaption());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// maori
		$this->maori->SetDbValueDef($rsnew, $this->maori->CurrentValue, "", FALSE);

		// english
		$this->english->SetDbValueDef($rsnew, $this->english->CurrentValue, "", FALSE);

		// description
		$this->description->SetDbValueDef($rsnew, $this->description->CurrentValue, NULL, FALSE);

		// dialects_id
		$this->dialects_id->SetDbValueDef($rsnew, $this->dialects_id->CurrentValue, NULL, FALSE);

		// tags
		$this->tags->SetDbValueDef($rsnew, $this->tags->CurrentValue, NULL, FALSE);

		// date_added
		$this->date_added->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_added->CurrentValue, 5), NULL, FALSE);

		// date_updated
		$this->date_updated->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_updated->CurrentValue, 5), NULL, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->id->setDbValue($conn->Insert_ID());
			$rsnew['id'] = $this->id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up master/detail based on QueryString
	function SetUpMasterParms() {
		$bValidMaster = FALSE;

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_MASTER])) {
			$sMasterTblVar = $_GET[EW_TABLE_SHOW_MASTER];
			if ($sMasterTblVar == "") {
				$bValidMaster = TRUE;
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
			}
			if ($sMasterTblVar == "dialects") {
				$bValidMaster = TRUE;
				if (@$_GET["id"] <> "") {
					$GLOBALS["dialects"]->id->setQueryStringValue($_GET["id"]);
					$this->dialects_id->setQueryStringValue($GLOBALS["dialects"]->id->QueryStringValue);
					$this->dialects_id->setSessionValue($this->dialects_id->QueryStringValue);
					if (!is_numeric($GLOBALS["dialects"]->id->QueryStringValue)) $bValidMaster = FALSE;
				} else {
					$bValidMaster = FALSE;
				}
			}
		}
		if ($bValidMaster) {

			// Save current master table
			$this->setCurrentMasterTable($sMasterTblVar);

			// Reset start record counter (new master key)
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);

			// Clear previous master key from Session
			if ($sMasterTblVar <> "dialects") {
				if ($this->dialects_id->QueryStringValue == "") $this->dialects_id->setSessionValue("");
			}
		}
		$this->DbMasterFilter = $this->GetMasterFilter(); //  Get master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Get detail filter
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "wordslist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($words_add)) $words_add = new cwords_add();

// Page init
$words_add->Page_Init();

// Page main
$words_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$words_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var words_add = new ew_Page("words_add");
words_add.PageID = "add"; // Page ID
var EW_PAGE_ID = words_add.PageID; // For backward compatibility

// Form object
var fwordsadd = new ew_Form("fwordsadd");

// Validate form
fwordsadd.Validate = function() {
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
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fwordsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fwordsadd.ValidateRequired = true;
<?php } else { ?>
fwordsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fwordsadd.Lists["x_dialects_id"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_dialects","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $words_add->ShowPageHeader(); ?>
<?php
$words_add->ShowMessage();
?>
<form name="fwordsadd" id="fwordsadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="words">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_wordsadd" class="table table-bordered table-striped">
<?php if ($words->maori->Visible) { // maori ?>
	<tr id="r_maori">
		<td><span id="elh_words_maori"><?php echo $words->maori->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $words->maori->CellAttributes() ?>>
<span id="el_words_maori" class="control-group">
<input type="text" data-field="x_maori" name="x_maori" id="x_maori" size="30" maxlength="45" placeholder="<?php echo $words->maori->PlaceHolder ?>" value="<?php echo $words->maori->EditValue ?>"<?php echo $words->maori->EditAttributes() ?>>
</span>
<?php echo $words->maori->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($words->english->Visible) { // english ?>
	<tr id="r_english">
		<td><span id="elh_words_english"><?php echo $words->english->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $words->english->CellAttributes() ?>>
<span id="el_words_english" class="control-group">
<textarea data-field="x_english" name="x_english" id="x_english" cols="35" rows="4" placeholder="<?php echo $words->english->PlaceHolder ?>"<?php echo $words->english->EditAttributes() ?>><?php echo $words->english->EditValue ?></textarea>
</span>
<?php echo $words->english->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($words->description->Visible) { // description ?>
	<tr id="r_description">
		<td><span id="elh_words_description"><?php echo $words->description->FldCaption() ?></span></td>
		<td<?php echo $words->description->CellAttributes() ?>>
<span id="el_words_description" class="control-group">
<textarea data-field="x_description" class="editor" name="x_description" id="x_description" cols="30" rows="8" placeholder="<?php echo $words->description->PlaceHolder ?>"<?php echo $words->description->EditAttributes() ?>><?php echo $words->description->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fwordsadd", "x_description", 30, 8, <?php echo ($words->description->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $words->description->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($words->dialects_id->Visible) { // dialects_id ?>
	<tr id="r_dialects_id">
		<td><span id="elh_words_dialects_id"><?php echo $words->dialects_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $words->dialects_id->CellAttributes() ?>>
<?php if ($words->dialects_id->getSessionValue() <> "") { ?>
<span<?php echo $words->dialects_id->ViewAttributes() ?>>
<?php echo $words->dialects_id->ViewValue ?></span>
<input type="hidden" id="x_dialects_id" name="x_dialects_id" value="<?php echo ew_HtmlEncode($words->dialects_id->CurrentValue) ?>">
<?php } else { ?>
<select data-field="x_dialects_id" id="x_dialects_id" name="x_dialects_id"<?php echo $words->dialects_id->EditAttributes() ?>>
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
?>
</select>
<?php
$sSqlWrk = "SELECT `id`, `dialects` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `dialects`";
$sWhereWrk = "";

// Call Lookup selecting
$words->Lookup_Selecting($words->dialects_id, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
?>
<input type="hidden" name="s_x_dialects_id" id="s_x_dialects_id" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`id` = {filter_value}"); ?>&t0=3">
<?php } ?>
<?php echo $words->dialects_id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($words->tags->Visible) { // tags ?>
	<tr id="r_tags">
		<td><span id="elh_words_tags"><?php echo $words->tags->FldCaption() ?></span></td>
		<td<?php echo $words->tags->CellAttributes() ?>>
<span id="el_words_tags" class="control-group">
<textarea data-field="x_tags" name="x_tags" id="x_tags" cols="30" rows="4" placeholder="<?php echo $words->tags->PlaceHolder ?>"<?php echo $words->tags->EditAttributes() ?>><?php echo $words->tags->EditValue ?></textarea>
</span>
<?php echo $words->tags->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($words->date_added->Visible) { // date_added ?>
	<tr id="r_date_added">
		<td><span id="elh_words_date_added"><?php echo $words->date_added->FldCaption() ?></span></td>
		<td<?php echo $words->date_added->CellAttributes() ?>>
<span id="el_words_date_added" class="control-group">
<input type="text" data-field="x_date_added" name="x_date_added" id="x_date_added" placeholder="<?php echo $words->date_added->PlaceHolder ?>" value="<?php echo $words->date_added->EditValue ?>"<?php echo $words->date_added->EditAttributes() ?>>
</span>
<?php echo $words->date_added->CustomMsg ?></td>
	</tr>
<?php } ?>
<span id="el_words_date_updated" class="control-group">
<input type="hidden" data-field="x_date_updated" name="x_date_updated" id="x_date_updated" value="<?php echo ew_HtmlEncode($words->date_updated->CurrentValue) ?>">
</span>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fwordsadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$words_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$words_add->Page_Terminate();
?>
