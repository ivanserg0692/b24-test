<?php
/**
 * @global  \CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/docs/manage/index.php");
$APPLICATION->SetTitle(GetMessage("DOCS_TITLE"));

$APPLICATION->IncludeComponent("bitrix:disk.common", ".default", Array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => SITE_DIR."docs/manage",
		"STORAGE_ID" => "4"
	)
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
