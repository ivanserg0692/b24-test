<?php

namespace IvanSerg\Main\Activities\CommandBuild;

use IvanSerg\Main\Activities\Properties\AbstractRequiredProperties;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class RequiredProperties extends AbstractRequiredProperties
{
    static public function getArFieldKeys(): array
    {
        return [
            Properties::PROPERTY_USERS => "",
            Properties::PROPERTY_TASK_ID => '',
            Properties::PROPERTY_NAME => '',
            Properties::PROPERTY_DESCRIPTION => '',
        ];
    }
}