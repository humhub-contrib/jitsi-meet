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

    /**
     * Generate the correct public room URL for sharing
     * Uses the /conference/{roomName} format instead of including app ID in path
     * 
     * @param string $roomName The room name
     * @param bool $absolute Whether to return absolute URL (default: true)
     * @return string The room URL
     */
    public function getRoomUrl($roomName, $absolute = true)
    {
        return Url::to(['/conference/' . $roomName], $absolute);
    }

    /**
     * Generate room URL with silent audio config for dial-in scenarios
     * 
     * @param string $roomName The room name
     * @param bool $absolute Whether to return absolute URL (default: true)
     * @return string The room URL with silent audio config
     */
    public function getRoomUrlSilent($roomName, $absolute = true)
    {
        $url = $this->getRoomUrl($roomName, $absolute);
        return $url . '#config.startSilent=true';
    }

    /**
     * Generate dial-in numbers page URL
     * 
     * @param string $roomName The room name
     * @param bool $absolute Whether to return absolute URL (default: true)
     * @return string The dial-in numbers page URL
     */
    public function getDialInNumbersUrl($roomName, $absolute = true)
    {
        return Url::to(['/jitsi-meet-cloud-8x8/room', 'room' => $roomName], $absolute);
    }

}
