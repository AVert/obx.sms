<?php
$bConnectEpilog = false;
if(!defined("BX_ROOT")) {
	$bConnectEpilog = true;
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	global $USER;
	if( !$USER->IsAdmin() ) return false;
}

if(!function_exists("A68_CopyDirFilesEx")) {
	function A68_CopyDirFilesEx($path_from, $path_to, $ReWrite = True, $Recursive = False, $bDeleteAfterCopy = False, $strExclude = "") {
		$path_from = str_replace(array("\\", "//"), "/", $path_from);
		$path_to = str_replace(array("\\", "//"), "/", $path_to);
		if(is_file($path_from) && !is_file($path_to)) {
			if( CheckDirPath($path_to) ) {
				$file_name = substr($path_from, strrpos($path_from, "/")+1);
				$path_to .= $file_name;
				return CopyDirFiles($path_from, $path_to, $ReWrite, $Recursive, $bDeleteAfterCopy, $strExclude);
			}
		}
		if( is_dir($path_from) && substr($path_to, strlen($path_to)-1) == "/" ) {
			$folderName = substr($path_from, strrpos($path_from, "/")+1);
			$path_to .= $folderName;
		}
		return CopyDirFiles($path_from, $path_to, $ReWrite, $Recursive, $bDeleteAfterCopy, $strExclude);
	}
}
if( is_file($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/a68.core/install/get_back_installed_files.php") ) {
	require_once $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/a68.core/install/get_back_installed_files.php";
}
A68_CopyDirFilesEx($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/a68.core", $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/a68.sms/install/modules/", true, true, FALSE, "modules");
if($bConnectEpilog) require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>