<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPCommandBuildActivity extends CBPActivity
{
    const CODE_USERS = 'users';

    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array("Title" => "", static::CODE_USERS => "");
    }

    public function Execute()
    {
        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId();
        $users = array_unique(CBPHelper::ExtractUsers($this->users, $documentId, false));

        return CBPActivityExecutionStatus::Closed;
    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $arErrors = array();

        if ($user == null || !$user->isAdmin()) {
            $arErrors[] = array(
                "code" => "perm",
                "message" => GetMessage("BPCA_NO_PERMS"),
            );
        }

        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user));
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();

        if (!is_array($arCurrentValues)) {
            $arCurrentValues = array(static::CODE_USERS => "");

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if (is_array($arCurrentActivity["Properties"])) {
                $arCurrentValues[static::CODE_USERS] = CBPHelper::UsersArrayToString($arCurrentActivity["Properties"][static::CODE_USERS],
                    $arWorkflowTemplate, $documentType);
            }
        }

        return $runtime->ExecuteResourceFile(
            __FILE__,
            "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName,
            )
        );
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = array();

        $arProperties = array(static::CODE_USERS =>
            CBPHelper::UsersStringToArray(
                $arCurrentValues[static::CODE_USERS],
                $documentType,
                $errors
            ));

        $arErrors = self::ValidateProperties($arProperties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        if (count($arErrors) > 0)
            return false;

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }
}