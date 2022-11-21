<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Ivanserg\Main\Activities\CommandBuild\Properties;

/**
 * @var array $arResult
 */
?>
<tr>
    <td valign="top" width="40%" align="right" class="bizproc-field-name">
        Тема:
    </td>
    <td valign="top" width="60%" class="bizproc-field-value">
        <?= $arResult[Properties::PROPERTY_NAME] ?>
    </td>
</tr>
<tr>
    <td valign="top" width="40%" align="right" class="bizproc-field-name">
        Описание:
    </td>
    <td valign="top" width="60%" class="bizproc-field-value">
        <?= $arResult[Properties::PROPERTY_DESCRIPTION] ?>
    </td>
</tr>
