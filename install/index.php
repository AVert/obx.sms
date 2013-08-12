<?
/*******************************************
 ** @product OBX:Market Bitrix Module     **
 ** @authors                              **
 **         Maksim S. Makarov aka pr0n1x  **
 **         Morozov P. Artem aka tashiro  **
 ** @license Affero GPLv3                 **
 ** @mailto rootfavell@gmail.com          **
 ** @mailto tashiro@yandex.ru             **
 ** @copyright 2013 DevTop                **
 *******************************************/

class obx_sms extends CModule
{
	var $MODULE_ID = "obx.sms";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	protected $installDir = null;
	protected $moduleDir = null;
	protected $bxModulesDir = null;
	protected $arErrors = array();
	protected $arWarnings = array();
	protected $arMessages = array();
	protected $bSuccessInstallDB = false;
	protected $bSuccessInstallFiles = false;
	protected $bSuccessInstallDeps = false;
	protected $bSuccessInstallEvents = false;
	protected $bSuccessInstallTasks = true;
	protected $bSuccessInstallData = false;
	protected $bSuccessUnInstallDB = false;
	protected $bSuccessUnInstallFiles = false;
	protected $bSuccessUnInstallDeps = false;
	protected $bSuccessUnInstallEvents = false;
	protected $bSuccessUnInstallTasks = true;
	protected $bSuccessUnInstallData = false;

	const DB = 1;
	const FILES = 2;
	const DEPS = 4;
	const EVENTS = 8;
	const TASKS = 16;
	const TARGETS = 31;

	public function obx_sms() {
		$this->installDir = str_replace(array("\\", "//"), "/", __FILE__);
		//10 == strlen("/index.php")
		//8 == strlen("/install")
		$this->installDir = substr($this->installDir , 0, strlen($this->installDir ) - 10);
		$this->moduleDir = substr($this->installDir , 0, strlen($this->installDir ) - 8);
		$this->bxModulesDir = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules";

		$arModuleVersion = array();
		$arModuleVersion = include($this->installDir."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("OBX_SMS_MODULE_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("OBX_SMS_MODULE_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("OBX_SMS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("OBX_SMS_PARTNER_URI");
	}

	public function getErrors() {
		return $this->arErrors;
	}

	public function getWarnings() {
		return $this->arWarnings;
	}

	public function getMessages() {
		return $this->arMessages;
	}

	/**
	 * @param int $maskTarget
	 * @return bool
	 */
	public function isInstallationSuccess($maskTarget) {
		$bSuccess = true;
		if($maskTarget & self::DB) {
			$bSuccess = $this->bSuccessInstallDB && $bSuccess;
		}
		if($maskTarget & self::FILES) {
			$bSuccess = $this->bSuccessInstallFiles && $bSuccess;
		}
		if($maskTarget & self::DEPS) {
			$bSuccess = $this->bSuccessInstallDeps && $bSuccess;
		}
		if($maskTarget & self::EVENTS) {
			$bSuccess = $this->bSuccessInstallEvents && $bSuccess;
		}
		if($maskTarget & self::TASKS) {
			$bSuccess = $this->bSuccessInstallTasks && $bSuccess;
		}
		return $bSuccess;
	}

	/**
	 * @param int $maskTarget
	 * @return bool
	 */
	public function isUnInstallationSuccess($maskTarget) {
		$bSuccess = true;
		if($maskTarget & self::DB) {
			$bSuccess = $this->bSuccessUnInstallDB && $bSuccess;
		}
		if($maskTarget & self::FILES) {
			$bSuccess = $this->bSuccessUnInstallFiles && $bSuccess;
		}
		if($maskTarget & self::DEPS) {
			$bSuccess = $this->bSuccessUnInstallDeps && $bSuccess;
		}
		if($maskTarget & self::EVENTS) {
			$bSuccess = $this->bSuccessUnInstallEvents && $bSuccess;
		}
		if($maskTarget & self::TASKS) {
			$bSuccess = $this->bSuccessUnInstallTasks && $bSuccess;
		}
		return $bSuccess;
	}

	public function DoInstall() {
		$bSuccess = true;
		$bSuccess = $this->InstallDB() && $bSuccess;
		$bSuccess = $this->InstallFiles() && $bSuccess;
		$bSuccess = $this->InstallDeps() && $bSuccess;
		$bSuccess = $this->InstallEvents() && $bSuccess;
		$bSuccess = $this->InstallTasks() && $bSuccess;
		if($bSuccess) {
			if( !IsModuleInstalled($this->MODULE_ID) ) {
				RegisterModule($this->MODULE_ID);
			}
			$this->InstallData();
		}
		return $bSuccess;
	}
	public function DoUninstall() {
		$bSuccess = true;
		$bSuccess = $this->UnInstallTasks() && $bSuccess;
		$bSuccess = $this->UnInstallEvents() && $bSuccess;
		//$bSuccess = $this->UnInstallDeps() && $bSuccess;
		$bSuccess = $this->UnInstallFiles() && $bSuccess;
		$bSuccess = $this->UnInstallDB() && $bSuccess;
		if($bSuccess) {
			if( IsModuleInstalled($this->MODULE_ID) ) {
				UnRegisterModule($this->MODULE_ID);
			}
		}
		return $bSuccess;
	}
	public function InstallFiles() {
		$this->bSuccessInstallFiles = true;
		if (is_file($this->installDir . "/install_files.php")) {
			require($this->installDir . "/install_files.php");
		}
		else {
			$this->bSuccessInstallFiles = false;
		}
		return $this->bSuccessInstallFiles;
	}
	public function UnInstallFiles() {
		$this->bSuccessUnInstallFiles = true;
		if (is_file($this->installDir . "/uninstall_files.php")) {
			require($this->installDir . "/uninstall_files.php");
		}
		else {
			$this->bSuccessUnInstallFiles = false;
		}
		return $this->bSuccessUnInstallFiles;
	}

	public function InstallDB() {
		global $DB, $DBType;
		$this->bSuccessInstallDB = true;
		if( is_file($this->installDir.'/db/'.$DBType.'/install.sql') ) {
			$this->prepareDBConnection();
			$arErrors = $DB->RunSQLBatch($this->installDir.'/db/'.$DBType.'/install.sql');
			if( is_array($arErrors) && count($arErrors)>0 ) {
				$this->arErrors = $arErrors;
				$this->bSuccessInstallDB = false;
			}
		}
		else {
			$this->bSuccessInstallDB = false;
		}
		return $this->bSuccessInstallDB;
	}
	public function UnInstallDB() {
		global $DB, $DBType;
		$this->bSuccessUnInstallDB = true;
		if( is_file($this->installDir.'/db/'.$DBType.'/uninstall.sql') ) {
			$this->prepareDBConnection();
			$arErrors = $DB->RunSQLBatch($this->installDir.'/db/'.$DBType.'/uninstall.sql');
			if( is_array($arErrors) && count($arErrors)>0 ) {
				$this->arErrors = $arErrors;
				$this->bSuccessUnInstallDB = false;
			}
		}
		else {
			$this->bSuccessUnInstallDB = false;
		}
		return $this->bSuccessUnInstallDB;
	}

	protected function prepareDBConnection() {
		global $APPLICATION, $DB, $DBType;
		if (defined('MYSQL_TABLE_TYPE') && strlen(MYSQL_TABLE_TYPE) > 0) {
			$DB->Query("SET table_type = '" . MYSQL_TABLE_TYPE . "'", true);
		}
		if (defined('BX_UTF') && BX_UTF === true) {
			$DB->Query('SET NAMES "utf8"');
			//$DB->Query('SET sql_mode=""');
			$DB->Query('SET character_set_results=utf8');
			$DB->Query('SET collation_connection = "utf8_unicode_ci"');
		}
	}

	public function InstallEvents() { $this->bSuccessInstallEvents = true; return $this->bSuccessInstallEvents; }
	public function UnInstallEvents() { $this->bSuccessUnInstallEvents = true; return $this->bSuccessUnInstallEvents; }
	public function InstallTasks() { $this->bSuccessInstallTasks = true; return $this->bSuccessInstallTasks; }
	public function UnInstallTasks() { $this->bSuccessUnInstallTasks = true; return $this->bSuccessUnInstallTasks; }
	public function InstallData() { $this->bSuccessInstallData = true; return $this->bSuccessInstallData; }
	public function UnInstallData() { $this->bSuccessUnInstallData = true; return $this->bSuccessUnInstallData; }


	protected function getDepsList() {
		$arDepsList = array();
		if( is_dir($this->installDir."/modules") && is_file($this->installDir.'/dependencies.php') ) {
			$arDepsList = require $this->installDir.'/dependencies.php';
		}
		return $arDepsList;
	}
	public function InstallDeps() { $this->bSuccessInstallDeps = true; return $this->bSuccessInstallDeps; }
	public function UnInstallDeps() { $this->bSuccessUnInstallDeps = true; return $this->bSuccessUnInstallDeps; }


	static public function getModuleCurDir(){
		static $strPath2Lang = null;
		if($strPath2Lang === null){
			$strPath2Lang = str_replace("\\", "/", __FILE__);
			// 18 = strlen of "/install/index.php"
			$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
		}
		return $strPath2Lang;
	}

	static public function includeLangFile(){
		global $MESS;
		@include(GetLangFileName(self::getModuleCurDir()."/lang/", "/install/index.php"));
	}
}

obx_sms::includeLangFile();
