<?php

/** @noinspection MissedFieldInspection */

use humhub\widgets\TopMenu;

return [
    'id' => 'jitsi-meet',
    'class' => 'humhubContrib\modules\jitsiMeet\Module',
    'namespace' => 'humhubContrib\modules\jitsiMeet',
    'events' => [
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhubContrib\modules\jitsiMeet\Events', 'onTopMenuInit']],
    ],
    'urlManagerRules' => [
        '/conference/<name>' => 'jitsi-meet/room/open'
    ]
];
?>