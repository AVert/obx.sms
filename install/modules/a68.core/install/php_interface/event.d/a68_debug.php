<?php
if( !function_exists('wd') ) {
	function wd($mixed, $collapse = null, $bPrint = true) {
		return bxutils::debug($mixed, $collapse, $bPrint);
	}

}
if( !function_exists('d') ) {
	function d($mixed, $collapse = null, $bPrint = true) {
		return bxutils::debug($mixed, $collapse, $bPrint);
	}
}
if( !function_exists('dd') ) {
	function dd($mixed, $collapse = null, $bPrint = true) {
		return bxutils::debug($mixed, $collapse, $bPrint);
	}
}
?>