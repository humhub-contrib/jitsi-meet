<?php

namespace humhubContrib\modules\jitsiMeet\assets;

use humhubContrib\modules\jitsiMeet\Module;
use Yii;
use yii\web\AssetBundle;
use yii\web\View;

class Assets extends AssetBundle
{

    public $publishOptions = [
        'forceCopy' => true
    ];

    public $jsOptions = [
        'position' => View::POS_BEGIN
    ];

    public function init()
    {
        $this->initJitsiApiJs();
        $this->sourcePath = dirname(__FILE__) . '/../resources';
        parent::init();
    }

    private function initJitsiApiJs()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet-cloud');
        if ($module instanceof Module) {
            $mode = $module->getSettingsForm()->mode;
            if ($mode === 'jaas') {
                $domain = $module->getSettingsForm()->jaasDomain ?: '8x8.vc';
                $this->js = [
                    'https://' . $domain . '/libs/external_api.min.js',
                    'humhub.jitsiMeet.js'
                ];
            } else {
                $this->js = [
                    'https://' . $module->getSettingsForm()->jitsiDomain . '/external_api.js',
                    'humhub.jitsiMeet.js'
                ];
            }
        }
    }

}