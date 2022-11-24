<?php

namespace Ivanserg\Main\Activities\CommandBuild;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class Properties
{
    const PROPERTY_USERS = 'users';
    const PROPERTY_NAME = 'assignmentName';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_REQUIRED_STAFF_COUNT = 'requiredStaffCount';
    const PROPERTY_BOSSES = 'administrationBoss';

    protected array $arValues;
    protected array $arErrors;
    protected array $arUserPropertyKeys;

    public function __construct()
    {
        $this->arErrors = [];
        $this->arValues = [];
        $this->arUserPropertyKeys = [static::PROPERTY_USERS, static::PROPERTY_BOSSES];
    }

    public function setArValuesByActivity(\CBPCommandBuildActivity $obActivity): void
    {
        $this->arValues = [];
        foreach (array_keys($this->getArList()) as $sPropertyKey) {
            $this->arValues[$sPropertyKey] = $obActivity->{$sPropertyKey};
        }
    }

    public function setArValues(array &$arValues): void
    {
        $this->arValues = $arValues;
    }

    public function getArValues(): array
    {
        return $this->arValues;
    }

    /**
     * this operation is reversible by processForShowingDialog
     * @param $documentType
     * @return void
     */
    public function processForSavingDialog($documentType): void
    {
        foreach ($this->arValues as $sKey => $arValue) {
            if (in_array($sKey, $this->arUserPropertyKeys)) {
                $arValue = \CBPHelper::UsersStringToArray(
                    $arValue,
                    $documentType,
                    $this->arErrors
                );
            }
            $this->arValues[$sKey] = $arValue;
        }
    }

    /**
     * this operation is reversible by processForSavingDialog
     * @param $documentType
     * @param $arWorkflowTemplate
     * @return void
     */
    public function processForShowingDialog($documentType, $arWorkflowTemplate): void
    {
        foreach ($this->arValues as $sKey => $arValue) {
            if (in_array($sKey, $this->arUserPropertyKeys)) {
                $arValue = \CBPHelper::UsersArrayToString(
                    $arValue,
                    $arWorkflowTemplate, $documentType
                );
            }
            $this->arValues[$sKey] = $arValue;
        }

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
            static::PROPERTY_NAME => '',
            static::PROPERTY_REQUIRED_STAFF_COUNT => 0,
            static::PROPERTY_BOSSES => '',
            static::PROPERTY_DESCRIPTION => '',];
    }
}