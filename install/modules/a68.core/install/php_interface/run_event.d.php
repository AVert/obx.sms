<?php

class EventD {
	const PATH_TO_USRINC_DIR = "/bitrix/php_interface/";
	const PATH_TO_EVENT_D = "/bitrix/php_interface/event.d";

	static public function connectAllEvents() {
		
		$pathEventD = $_SERVER["DOCUMENT_ROOT"].self::PATH_TO_EVENT_D; 
		
		if( !is_dir($pathEventD)) {
			return false;
		}
		
		$dirEventD = opendir($pathEventD);
		while ( $elementOfDir = readdir($dirEventD) ) {
			if (
			$elementOfDir != ".."
			&& $elementOfDir != "."
			&& substr($elementOfDir, strlen($elementOfDir)-4, strlen($elementOfDir)) == ".php"
			) {
				$arFilesList[] = $elementOfDir;
			}
		}
		
		foreach($arFilesList as $eventFileName) {
			$eventFilePath = $pathEventD."/".$eventFileName;
			@include $eventFilePath;
		}
	}
}
EventD::connectAllEvents();

?>