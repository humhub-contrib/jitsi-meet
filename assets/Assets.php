<?php

namespace humhubContrib\modules\jitsiMeet\assets;

use humhubContrib\modules\jitsiMeet\Module;
use Yii;
use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $publishOptions = [
        'forceCopy' => true
    ];
    public $css = [];
    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];

    public function init()
    {
        $this->js = [
            $this->getJitsiApiJs(),
            'humhub.jitsiMeet.js'
        ];

        $this->sourcePath = dirname(__FILE__) . '/../resources';
        parent::init();
    }

    private function getJitsiApiJs()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet');
        return 'https://' . $module->getSettingsForm()->jitsiDomain . '/external_api.js';
    }

}