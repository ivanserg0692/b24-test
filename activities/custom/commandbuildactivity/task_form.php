<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Ivanserg\Main\Activities\CommandBuild\Properties;

/**
 * @var array $arResult
 */

global $USER;
$userId = $USER->GetID();
$isBoss = in_array($userId, $arResult[Properties::PROPERTY_BOSSES]);
$isStaff = in_array($userId, $arResult[Properties::PROPERTY_USERS]);
$isComplex = $isBoss && $isStaff;
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
        <?= htmlspecialchars_decode($arResult[Properties::PROPERTY_DESCRIPTION]) ?>
    </td>
</tr>
<?php if($isBoss &&
    count($arResult[Properties::PROPERTY_RESULT_AR_STAFF_ID]) < $arResult[Properties::PROPERTY_REQUIRED_STAFF_COUNT]): ?>
    <tr><td valign="top" width="60%" class="bizproc-field-value" colspan="2">
            <?= $arResult[Properties::PROPERTY_CANT_STOP_TEXT] ?>
        </td>
    </tr>
    <?php endif; ?>
<?php if($isComplex): ?>
<tr><td colspan="2"><hr></td></tr>
    <tr>
        <td valign="top" width="40%" align="right" class="bizproc-field-name">
            Буду участвовать:
        </td>
        <td valign="top" width="60%" class="bizproc-field-value">
            <input type="checkbox" name="will-participate" value="Y">
        </td>
    </tr>
<?php endif; ?>
