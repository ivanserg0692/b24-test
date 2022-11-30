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
    const PROPERTY_RESULT_AR_STAFF_ID = 'arStaffId';
    const PROPERTY_RESULT_BOSS_ID = 'bossId';

    protected array $arValues;
    protected array $arErrors;
    protected array $arUserPropertyKeys;
    protected bool $isProcessedForTask = false;

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
            try {
                $this->arValues[$sPropertyKey] = $obActivity->{$sPropertyKey};
            } catch (\Throwable $exception) {
                continue;
            }
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

    /**
     * this operation is irreversible
     * @param $documentId
     * @return void
     */
    public function proccessForTask($documentId)
    {
        if ($this->isProcessedForTask) {
            return;
        }
        $this->isProcessedForTask = true;
        foreach ($this->arValues as $sKey => $arValue) {
            if (in_array($sKey, $this->arUserPropertyKeys)) {
                $arValue = \CBPHelper::ExtractUsers($arValue, $documentId, false);
            }
            $this->arValues[$sKey] = $arValue;
        }
    }

    public function getArUserIdsByPropertyKey(string $name, $documentId): array
    {
        $obProperties = ($this->isProcessedForTask) ? $this : clone $this;
        $obProperties->proccessForTask($documentId);
        return $obProperties->{$name} ?: [];
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (!isset($this->arValues[$name])) {
            return null;
        }
        return $this->arValues[$name];
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
            static::PROPERTY_DESCRIPTION => '',
            static::PROPERTY_RESULT_AR_STAFF_ID => [],
            static::PROPERTY_RESULT_BOSS_ID => ''
        ];
    }
}