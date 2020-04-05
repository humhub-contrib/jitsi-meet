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
        $module = Yii::$app->getModule('jitsi-meet');

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
        $module = Yii::$app->getModule('jitsi-meet');

        $name = '';
        $email = '';

        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
            $email = $user->email;
            $name = $user->displayName;
        }

        return [
            'jwt' => $this->jwt,
            'roomName' => $this->roomName,
            'roomPrefix' => $module->getSettingsForm()->roomPrefix,
            'jitsiDomain' => $module->getSettingsForm()->jitsiDomain,
            'usermail' => $email,
            'userdisplayname' => $name
        ];
    }

}
