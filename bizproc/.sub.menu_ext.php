<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/bizproc/.left.menu_ext.php");

$tasksCount = (int)CUserCounter::getValue($GLOBALS['USER']->getID(), 'bp_tasks');
$aMenuLinks = [
	[
		GetMessage("MENU_BIZPROC_TASKS_1"),
		SITE_DIR . "company/personal/bizproc/",
		[],
		[
			"counter_id" => "bp_tasks",
			"counter_num" => $tasksCount,
			"menu_item_id" => "menu_bizproc"
		],
		"",
	],
];
if (CModule::IncludeModule("lists") && CLists::isFeatureEnabled())
{
	$aMenuLinks[] = Array(
		GetMessage("MENU_MY_PROCESS_1"),
		SITE_DIR."company/personal/processes/",
		Array(),
		Array("menu_item_id" => "menu_my_processes"),
		""
	);
}
if (CModule::IncludeModule("lists") && CLists::isFeatureEnabled())
{
	$aMenuLinks[] = Array(
		GetMessage("MENU_PROCESS_STREAM2"),
		SITE_DIR."bizproc/processes/",
		Array(),
		Array("menu_item_id" => "menu_processes"),
		""
	);
}
$aMenuLinks[] = Array(
	GetMessage("MENU_BIZPROC_ACTIVE"),
	SITE_DIR."bizproc/bizproc/",
	Array(),
	Array("menu_item_id" => "menu_bizproc_active"),
	""
);
?>