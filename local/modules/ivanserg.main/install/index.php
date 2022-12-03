<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ivanserg_main extends \CModule
{
    var $MODULE_ID = "ivanserg.main";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";
    var $PARTNER_NAME;
    var $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = $arModuleVersion["NAME"];
        $this->MODULE_DESCRIPTION = $arModuleVersion["DESCRIPTION"];
        $this->PARTNER_NAME = 'IvanSerg0692';
        $this->PARTNER_URI = 'https://github.com/ivanserg0692';
    }

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
    }

}