<?php

namespace humhubContrib\modules\jitsiMeet\assets;

use humhubContrib\modules\jitsiMeet\Module;
use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class Assets extends AssetBundle
{
    public $publishOptions = [
        'forceCopy' => true,
    ];

    public $jsOptions = [
        'position' => View::POS_BEGIN,
    ];

    public function init()
    {
        $this->initJitsiApiJs();
        $this->sourcePath = __DIR__ . '/../resources';
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
