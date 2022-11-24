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
		<?=CBPDocument::ShowParameterField('user', Properties::PROPERTY_USERS, $arCurrentValues[Properties::PROPERTY_USERS], array('rows' => 1, 'cols' => 70))?>
	</td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span class="adm-required-field">Название задания:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', Properties::PROPERTY_NAME, $arCurrentValues[Properties::PROPERTY_NAME], array('rows' => 1, 'cols' => 70))?>
    </td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span class="adm-required-field">Описание задания:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', Properties::PROPERTY_DESCRIPTION, $arCurrentValues[Properties::PROPERTY_DESCRIPTION], array('rows' => 10, 'cols' => 70))?>
    </td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span>Минимальное число необходимых сотрудников:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('string', Properties::PROPERTY_REQUIRED_STAFF_COUNT, $arCurrentValues[Properties::PROPERTY_REQUIRED_STAFF_COUNT], array('rows' => 1, 'cols' => 70))?>
    </td>
</tr>
<tr>
    <td align="right" width="40%" valign="top"><span>Руководители, которые могут стопорить таску:</span></td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField('user', Properties::PROPERTY_BOSSES, $arCurrentValues[Properties::PROPERTY_BOSSES], array('rows' => 1, 'cols' => 70))?>
    </td>
</tr>