<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use \IvanSerg\Main\Activities\CommandBuild\Properties;

\CModule::IncludeModule('ivanserg.main');

class CBPCommandBuildActivity extends CBPActivity
    implements IBPEventActivity, IBPActivityExternalEventListener
{

    const CODE_ACTIVITY = 'CommandBuildActivity';

    protected Properties $obProperties;

    protected function getArUsersIds(): array
    {
        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId();
        return CBPHelper::ExtractUsers($this->users, $documentId, false);
    }

    public function __sleep(): array
    {
        return array_diff(array_keys(get_object_vars($this)), ['obProperties']);
    }

    public function __wakeup(): void
    {
        $this->obProperties = new Properties;
        $this->obProperties->setArValuesByActivity($this);
    }

    public function __construct($name)
    {
        parent::__construct($name);
        $this->obProperties = new Properties;
        $this->arProperties = array("Title" => ""
            ) + $this->obProperties->getArList();

    }

    public function Execute()
    {
        $this->obProperties->setArValuesByActivity($this);
        $this->Subscribe($this);
        $this->writeToTrackingService('Подписка выполнена ' . $this->name);
        return CBPActivityExecutionStatus::Executing;
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
        $obProperties = new Properties();
        $obProperties->setArValues($arTestProperties);
        $obProperties->valid();

        return array_merge($arErrors, parent::ValidateProperties($arTestProperties, $user), $obProperties->getArrErrors());
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters,
                                               $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();

        if (!is_array($arCurrentValues)) {
            $arCurrentValues = array(Properties::PROPERTY_USERS => "");
            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);

            if (is_array($arCurrentActivity["Properties"])) {
                $arCurrentValues[Properties::PROPERTY_USERS] = CBPHelper::UsersArrayToString(
                    $arCurrentActivity["Properties"][Properties::PROPERTY_USERS],
                    $arWorkflowTemplate, $documentType
                );
                $arCurrentValues[Properties::PROPERTY_NAME] = $arCurrentActivity["Properties"][Properties::PROPERTY_NAME];
                $arCurrentValues[Properties::PROPERTY_DESCRIPTION] = $arCurrentActivity["Properties"][Properties::PROPERTY_DESCRIPTION];
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

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate,
                                                     &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues,
                                                     &$arErrors)
    {
        $arErrors = array();

        $arProperties = array(
            Properties::PROPERTY_USERS =>
                CBPHelper::UsersStringToArray(
                    $arCurrentValues[Properties::PROPERTY_USERS],
                    $documentType,
                    $errors
                ),
            Properties::PROPERTY_NAME => $arCurrentValues[Properties::PROPERTY_NAME],
            Properties::PROPERTY_DESCRIPTION => $arCurrentValues[Properties::PROPERTY_DESCRIPTION]
        );


        $arErrors = self::ValidateProperties($arProperties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        if (count($arErrors) > 0)
            return false;

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }

    public function Cancel()
    {
        $this->writeToTrackingService('Задача отменена ' . $this->name);
        if ($this->taskId > 0) {
            $this->Unsubscribe($this);
        }

        return CBPActivityExecutionStatus::Closed;
    }

    public function HandleFault(Exception $exception)
    {
        $status = $this->cancel();
        if ($status == CBPActivityExecutionStatus::Canceling) {
            return CBPActivityExecutionStatus::Faulting;
        }
        return parent::HandleFault($exception);
    }

    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        $runtime = CBPRuntime::GetRuntime();
        $documentService = $runtime->GetService("DocumentService");
        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId();

        /** @var CBPTaskService $taskService */
        $taskService = $this->workflow->GetService("TaskService");
        $this->writeToTrackingService(var_export($this->getArUsersIds(), true));
        $this->taskId = $taskService->CreateTask(
            array(
                "USERS" => $this->getArUsersIds(),
                "WORKFLOW_ID" => $this->GetWorkflowInstanceId(),
                "ACTIVITY" => static::CODE_ACTIVITY,
                "ACTIVITY_NAME" => $this->name,
//                "OVERDUE_DATE" => $overdueDate,
                "NAME" => $this->assignmentName,
                "DESCRIPTION" => $this->Description,
                "PARAMETERS" => $this->obProperties->getArValues(),
                'IS_INLINE' => 'N',
//                'DELEGATION_TYPE' => (int)$this->DelegationType,
                'DOCUMENT_NAME' => $documentService->GetDocumentName($documentId)
            )
        );
        $this->workflow->AddEventHandler($this->name, $eventHandler);
    }

    public function Unsubscribe(IBPActivityExternalEventListener $eventHandler)
    {

        $this->writeToTrackingService('Отписка ' . $this->name);
        $taskService = $this->workflow->GetService("TaskService");
        if ($this->taskStatus === false) {
            $taskService->DeleteTask($this->taskId);
        } else {
            $taskService->Update($this->taskId, array(
                'STATUS' => $this->taskStatus
            ));
        }
        $this->workflow->RemoveEventHandler($this->name, $eventHandler);
    }

    public function OnExternalEvent($arEventParameters = array())
    {
        $this->taskStatus = CBPTaskStatus::CompleteYes;
        $this->writeToTrackingService('Пользователь ознакомлен');
        $taskService = $this->workflow->GetService("TaskService");
        $taskService->MarkCompleted($this->taskId, $arEventParameters["REAL_USER_ID"], CBPTaskStatus::CompleteYes);
    }

    public static function PostTaskForm($arTask, $userId, $arRequest, &$arErrors, $userName = "", $realUserId = null)
    {
        CBPRuntime::SendExternalEvent($arTask["WORKFLOW_ID"], $arTask["ACTIVITY_NAME"], [
            "USER_ID" => $userId,
            "REAL_USER_ID" => $realUserId,
            "USER_NAME" => $userName,
        ]);
        return true;
    }

    public static function ShowTaskForm($arTask, $userId, $userName = "")
    {
        $runtime = CBPRuntime::GetRuntime();
        $form = $runtime->ExecuteResourceFile(
            __FILE__,
            "task_form.php",
            array(
                "arResult" => $arTask['PARAMETERS'],
            )
        );
        $buttons = $runtime->ExecuteResourceFile(
            __FILE__,
            "task_buttons.php",
            array(
                "arResult" => $arTask['PARAMETERS'],
            )
        );;

        return array($form, $buttons);
    }
}