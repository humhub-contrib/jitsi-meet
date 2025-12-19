<?php

/** @noinspection MissedFieldInspection */

use humhub\widgets\TopMenu;

return [
    'id' => 'jitsi-meet-cloud-8x8',
    'class' => 'humhubContrib\modules\jitsiMeetCloud8x8\Module',
    'namespace' => 'humhubContrib\modules\jitsiMeetCloud8x8',
    'events' => [
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhubContrib\modules\jitsiMeetCloud8x8\Events', 'onTopMenuInit']],
    ],
    'urlManagerRules' => [
        '/conference/<name>' => 'jitsi-meet-cloud-8x8/room/open',
        // Handle old vpaas-magic-cookie URL format and redirect to new format
        // Pattern: /vpaas-magic-cookie-{32-hex-chars}/{roomName}
        '/vpaas-magic-cookie-<appId:[a-f0-9]{32}>/<name>' => 'jitsi-meet-cloud-8x8/room/redirect',
    ]
];
?>