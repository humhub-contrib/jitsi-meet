<?php

namespace humhubContrib\modules\jitsiMeet;

use humhub\widgets\TopMenu;
use Yii;
use yii\helpers\Url;

class Events
{

    public static function onTopMenuInit($event)
    {
        /** @var TopMenu $topNav */
        $topNav = $event->sender;

        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet');

        // Allow overwrite using translation
        $label =  Yii::t('JitsiMeetModule.base', $module->getSettingsForm()->menuTitle);

        $topNav->addItem([
            'label' => $label,
            'url' => Url::to(['/jitsi-meet/room']),
            'icon' => '<i class="fa fa-video-camera"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'jitsi-meet'),
            'sortOrder' => 400,
        ]);

    }


}