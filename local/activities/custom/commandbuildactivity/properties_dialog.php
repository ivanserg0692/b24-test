<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Ivanserg\Main\Activities\CommandBuild\Properties;

/**
 * @var array $arCurrentValues
 */
?>
<tr>
	<td align="right" width="40%" valign="top"><span class="adm-required-field">Выборка пользователей:</span></td>
	<td width="60%">
		<?=CBPDocument::ShowParameterField('user', Properties::PROPERTY_USERS, $arCurrentValues['users'], array('rows' => 1, 'cols' => 70))?>
	</td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span class="adm-required-field">Название задания:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', Properties::PROPERTY_NAME, $arCurrentValues['name'], array('rows' => 1, 'cols' => 70))?>
    </td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span class="adm-required-field">Описание задания:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', Properties::PROPERTY_DESCRIPTION, $arCurrentValues['description'], array('rows' => 10, 'cols' => 70))?>
    </td>
</tr>