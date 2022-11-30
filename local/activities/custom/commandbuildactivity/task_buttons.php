<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult;
 */

use \IvanSerg\Main\Activities\CommandBuild\Properties;

global $USER;
$userId = $USER->GetID();
$isBoss = in_array($userId, $arResult[Properties::PROPERTY_BOSSES]);
$isStaff = in_array($userId, $arResult[Properties::PROPERTY_USERS]);
?>
<?php if ($isBoss): ?>
    <input type="submit" name="stop" value="Остановить сборку команды" class=" bp-button bp-button-accept" style="border: none">
<?php elseif ($isStaff): ?>
    <input type="submit" name="will" value="Буду участвовать" class=" bp-button bp-button-accept" style="border: none">
<?php endif; ?>