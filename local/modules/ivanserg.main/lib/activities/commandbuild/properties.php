<?php

namespace Ivanserg\Main\Activities\CommandBuild;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class Properties
{
    const PROPERTY_USERS = 'users';
    const PROPERTY_TASK_ID = 'taskId';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    public function getRequiredKeys():array
    {
        return RequiredProperties::getArFieldKeys();
    }

    public function getArList(): array
    {
        return [
            static::PROPERTY_USERS => "",
            static::PROPERTY_TASK_ID => '',
            static::PROPERTY_NAME => '',
            static::PROPERTY_DESCRIPTION => '',];
    }
}