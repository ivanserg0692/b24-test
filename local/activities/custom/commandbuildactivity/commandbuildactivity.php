<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use \IvanSerg\Main\Activities\CommandBuild\Properties;

\CModule::IncludeModule('ivanserg.main');

class CBPCommandBuildActivity extends CBPActivity
    implements IBPEventActivity, IBPActivityExternalEventListener
{

    const CODE_ACTIVITY = 'CommandBuildActivity';

    protected Properties $obProperties;
    protected int $taskId = 0;
    protected array $arUserStaffIds;
    protected array $arUserBossIds;


    protected function getArUsersIds(): array
    {
        return array_unique(array_merge($this->getArStaffIds(), $this->getArBossIds()));
    }

    protected function getArBossIds(): array
    {
        if(!isset($this->arUserBossIds)) {
            $rootActivity = $this->GetRootActivity();
            $documentId = $rootActivity->GetDocumentId();
            $this->arUserBossIds = $this->obProperties
                ->getArUserIdsByPropertyKey(Properties::PROPERTY_BOSSES, $documentId);
        }
        return $this->arUserBossIds;
    }

    protected function getArStaffIds(): array
    {
        if(!isset($this->arUserStaffIds)) {
            $rootActivity = $this->GetRootActivity();
            $documentId = $rootActivity->GetDocumentId();
            $this->arUserStaffIds = $this->obProperties
                ->getArUserIdsByPropertyKey(Properties::PROPERTY_USERS, $documentId);
        }
        return $this->arUserStaffIds;
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
                $obProperties = new Properties;
                $obProperties->setArValues($arCurrentActivity["Properties"]);
                $obProperties->processForShowingDialog($documentType, $arWorkflowTemplate);
                $arCurrentValues = $obProperties->getArValues() + $arCurrentValues;
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

        $obProperties = new Properties();
        $obProperties->setArValues($arCurrentValues);
        $obProperties->processForSavingDialog($documentType);
        $arProperties = $obProperties->getArValues();


        $arErrors = self::ValidateProperties($arProperties, new CBPWorkflowTemplateUser(CBPWorkflowTemplateUser::CurrentUser));
        $arErrors = array_merge($arErrors, $obProperties->getArrErrors());
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
        $this->obProperties->proccessForTask($documentId);
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
        $this->writeToTrackingService('Создана задача ' . $this->taskId);
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
        global $USER;
        $this->taskStatus = CBPTaskStatus::CompleteYes;
        $this->writeToTrackingService('Пользователь ознакомлен');
        $userId = $USER->GetID();
        $isBoss = in_array($userId, $this->getArBossIds());
        $isStaff = in_array($userId, $this->getArStaffIds());
        if(!$isBoss && $isStaff) {
            //just a staff
            $this->onWilParticipate($arEventParameters);
            $taskService = $this->workflow->GetService("TaskService");
            $taskService->MarkCompleted($this->taskId, $arEventParameters["REAL_USER_ID"], CBPTaskStatus::CompleteYes);
        } elseif($isBoss && $isStaff) {
            //staff and boss
            if($arEventParameters['WILL_PARTICIPATE']) {
                $this->onWilParticipate($arEventParameters);
            }
            if($arEventParameters['STOP']) {
                $this->onStop($arEventParameters);
            }
        } else {
            //just a boss
            $this->onStop($arEventParameters);
        }
    }

    protected function onWilParticipate($arEventParameters = array())
    {
        $this->writeToTrackingService("Событие буду участвовать");
    }

    protected function onStop($arEventParameters = array())
    {
        $this->writeToTrackingService("Событие остановки");
    }

    public static function PostTaskForm($arTask, $userId, $arRequest, &$arErrors, $userName = "", $realUserId = null)
    {
        CBPRuntime::SendExternalEvent($arTask["WORKFLOW_ID"], $arTask["ACTIVITY_NAME"], [
            "USER_ID" => $userId,
            "REAL_USER_ID" => $realUserId,
            "USER_NAME" => $userName,
            "STOP" => boolval($arRequest['stop']),
            "WILL_PARTICIPATE" => boolval($arRequest['will-participate']),
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