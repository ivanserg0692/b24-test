<?php

namespace Ivanserg\Main\Activities\CommandBuild;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class Properties
{
    const PROPERTY_USERS = 'users';
    const PROPERTY_TASK_ID = 'taskId';
    const PROPERTY_NAME = 'assignmentName';
    const PROPERTY_DESCRIPTION = 'description';

    protected array $arValues;
    protected array $arErrors;

    public function __construct()
    {
        $this->arErrors = [];
    }

    public function setArValues(array &$arValues)
    {
        $this->arValues = $arValues;
    }

    public function valid(): void
    {
        $this->arErrors = [];
        foreach ($this->getRequiredKeys() as $sKey) {
            if (!$this->arValues[$sKey]) {
                $this->arErrors[] = [
                    'code' => 'required_input',
                    'message' => 'Заполните обязательное поле ' . $sKey
                ];
            }
        }
    }

    public function getArrErrors(): array
    {
        return $this->arErrors;
    }


    protected function getRequiredKeys(): array
    {
        return [
            static::PROPERTY_USERS,
            static::PROPERTY_NAME,
            static::PROPERTY_DESCRIPTION];
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