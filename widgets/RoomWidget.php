<?php

namespace humhubContrib\modules\jitsiMeet\widgets;

use Yii;
use humhub\widgets\JsWidget;
use humhubContrib\modules\jitsiMeet\Module;

class RoomWidget extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'jitsiMeet.Room';

    /**
     * @inheritdoc
     */
    public $init = true;

    public $roomName = 'Unnamed';

    public $jwt = '';

    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet-cloud');

        return $this->render('room', [
            'options' => $this->getOptions(),
            'roomName' => $this->roomName,
            # Allow overwriting via translation config
            'moduleLabel' => Yii::t('JitsiMeetModule.base', $module->getSettingsForm()->menuTitle),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet-cloud');

        $name = '';
        $email = '';

        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
            $email = $user->email;
            $name = $user->displayName;
        }

        $data = [
            'jwt' => $this->jwt,
            'roomName' => $this->roomName,
            'roomPrefix' => $module->getSettingsForm()->roomPrefix,
            // Domains and mode for dual-mode support
            'jitsiDomain' => $module->getSettingsForm()->jitsiDomain,
            'jaasDomain' => $module->getSettingsForm()->jaasDomain,
            'mode' => $module->getSettingsForm()->mode,
            'jaasAppId' => $module->getSettingsForm()->jaasAppId,
            'usermail' => $email,
            'userdisplayname' => $name
        ];

        // Enhanced logging for debugging
        Yii::info('RoomWidget::getData - Data being passed to frontend: ' . json_encode($data, JSON_PRETTY_PRINT), 'jitsi-meet');

        return $data;
    }

}
