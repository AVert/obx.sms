<?php
@include_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/obx.sms/providers/TurboSmsUA.php');
OBX\Sms\Provider\TurboSmsUA::registerProvider();