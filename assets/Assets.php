<?php

namespace humhubContrib\modules\jitsiMeet\assets;

use humhub\components\assets\AssetBundle;
use yii\web\View;

class Assets extends AssetBundle
{
    public $sourcePath = '@jitsi-meet/resources';

    public $forceCopy = false;

    public $jsOptions = [
        'position' => View::POS_BEGIN,
    ];

    /**
     * The Jitsi `external_api.js` is loaded by the `room/open` view
     * @inheritdoc
     */
    public $js = [
        'humhub.jitsiMeet.js',
    ];
}
