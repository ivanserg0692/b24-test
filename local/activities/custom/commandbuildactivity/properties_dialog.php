<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<tr>
	<td align="right" width="40%" valign="top"><span class="adm-required-field">Выборка пользователей:</span></td>
	<td width="60%">
		<?=CBPDocument::ShowParameterField('user', 'users', $arCurrentValues['users'], array('rows' => 1, 'cols' => 70))?>
	</td>
</tr>