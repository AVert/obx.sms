<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("__hide_footer", "Y");

if (!CModule::IncludeModule("obx.sms"))
	die();

OBX_Tools::PrintBXError("");
$object = OBX_SmsSender::factory("LETSADS");
wd($object, 'providerObject');
$result = $object->send("79135295396","test message");
//wd($object->requestBalance(),"Balance");
wd($result,"result");

//wd(OBX_SmsSender::popLastError(), 'OBX_SmsSender::popLastError()');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>