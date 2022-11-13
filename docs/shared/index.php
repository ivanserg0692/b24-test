<?php
/**
 * @global  \CMain $APPLICATION
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/shared/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));
$APPLICATION->AddChainItem($APPLICATION->GetTitle(), "/docs/shared/");
?><?php
$APPLICATION->IncludeComponent("bitrix:disk.common", ".default", [
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => SITE_DIR."docs/shared",
		"STORAGE_ID" => "3"
	]
);?><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");