<?php

namespace humhubContrib\modules\jitsiMeet\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{

    public $jitsiDomain;
    public $menuTitle;
    public $roomPrefix;
    public $jwtToken;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['jitsiDomain', 'string'],
            [['menuTitle', 'jwtToken', 'roomPrefix'], 'string'],
        ];
    }

    public function attributeHints()
    {
        return [
            'jitsiDomain' => Yii::t('JitsiMeetModule.base', 'Default is meet.jit.si without "https://" prefix.'),
            'menuTitle' => Yii::t('JitsiMeetModule.base', 'Default: Jitsi Meet'),
            'jwtToken' => Yii::t('JitsiMeetModule.base', 'Specify JWT token to enable authentication'),
            'roomPrefix' => Yii::t('JitsiMeetModule.base', 'Default: empty, useful for public Jitsi server'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->menuTitle = Yii::$app->getModule('jitsi-meet')->settings->get('menuTitle');
        if (empty($this->menuTitle)) {
            $this->menuTitle = 'Jitsi Meet';
        }

        $this->jitsiDomain = Yii::$app->getModule('jitsi-meet')->settings->get('jitsiDomain');
        if (empty($this->jitsiDomain)) {
            $this->jitsiDomain = 'meet.jit.si';
        }

        $this->roomPrefix = Yii::$app->getModule('jitsi-meet')->settings->get('roomPrefix');
        if (empty($this->roomPrefix)) {
            $this->roomPrefix = '';
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        Yii::$app->getModule('jitsi-meet')->settings->set('menuTitle', $this->menuTitle);
        Yii::$app->getModule('jitsi-meet')->settings->set('jitsiDomain', $this->jitsiDomain);

        $this->roomPrefix = ucwords(preg_replace("/[^A-Za-z0-9]/", '', $this->roomPrefix));
        Yii::$app->getModule('jitsi-meet')->settings->set('roomPrefix', $this->roomPrefix);

        return true;
    }
}
