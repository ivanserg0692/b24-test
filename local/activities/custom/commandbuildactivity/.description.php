<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arActivityDescription = array(
    "NAME" => "Выбор исполнителей",
    "DESCRIPTION" => "Собрать список исполнителей проекта",
    "TYPE" => "activity",
    "CLASS" => "CommandBuildActivity",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => array(
        "ID" => "other",
    ),
    "FILTER" => array(
        'EXCLUDE' => CBPHelper::DISTR_B24
    ),
    'RETURN' => [
        'arStaffId' => [
            'NAME' => 'Исполнители Staff',
            'TYPE' => 'user',
            'Multiple' => true
        ],
        'bossId' => [
            'NAME' => 'Руководитель',
            'TYPE' => 'user'
        ]
    ]
);
