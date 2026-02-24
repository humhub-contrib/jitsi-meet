<?php

namespace humhubContrib\modules\jitsiMeet\assets;

use humhub\components\assets\AssetBundle;
use humhubContrib\modules\jitsiMeet\Module;
use Yii;
use yii\web\View;

class Assets extends AssetBundle
{
    public $sourcePath = '@jitsi-meet/resources';

    public $forceCopy = false;

    public $jsOptions = [
        'position' => View::POS_BEGIN,
    ];

    public function init()
    {
        $this->initJitsiApiJs();
        parent::init();
    }

    private function initJitsiApiJs()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet');
        if ($module instanceof Module) {
            $this->js = [
                'https://' . $module->getSettingsForm()->jitsiDomain . '/external_api.js',
                'humhub.jitsiMeet.js',
            ];
        }
    }

}
