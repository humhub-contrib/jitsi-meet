<?php

namespace humhubContrib\modules\jitsiMeetCloud8x8;


use humhubContrib\modules\jitsiMeetCloud8x8\models\SettingsForm;
use yii\helpers\Url;

class Module extends \humhub\components\Module
{
    public $resourcesPath = 'resources';

    private $_settingsForm = null;

    /**
     * @return SettingsForm
     */
    public function getSettingsForm()
    {
        if ($this->_settingsForm === null) {
            $this->_settingsForm = new SettingsForm();
        }

        return $this->_settingsForm;
    }


    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/jitsi-meet-cloud-8x8/config']);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        return [
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\CanAccess(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\CreateVideoChat(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\JoinVideoChat(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\CanBeModerator(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\EnableRecording(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\EnableLivestreaming(),
            new \humhubContrib\modules\jitsiMeetCloud8x8\permissions\ManageRecordings(),
        ];
    }

}
