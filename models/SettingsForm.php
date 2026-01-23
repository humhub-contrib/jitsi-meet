<?php

namespace humhubContrib\modules\jitsiMeet\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    public const DEFAULT_JITSI_DOMAINS = [
        'meet.ffmuc.net',
        'kmeet.infomaniak.com',
        'jitsi.hamburg.ccc.de',
    ];

    public $jitsiDomain;
    public $menuTitle;
    public $roomPrefix;
    public $jitsiAppID;
    public $jitsiAppSecret;
    public $enableJwt;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['jitsiDomain', 'string'],
            [['menuTitle', 'jitsiAppID', 'jitsiAppSecret', 'roomPrefix'], 'string'],
            ['enableJwt', 'boolean'],
            [['jitsiAppID', 'jitsiAppSecret'], 'required', 'when' => fn($model) => $model->enableJwt, 'whenClient' => "function (attribute, value) {
                return $('#settingsform-enablejwt').is(':checked');
            }"],
        ];
    }

    public function attributeHints()
    {
        return [
            'jitsiDomain' => Yii::t('JitsiMeetModule.base', 'Without "https://" prefix.'),
            'jitsiAppID' => Yii::t('JitsiMeetModule.base', 'Application ID shared with a private Jitsi server used to generate JWT token for authentication. Default: empty, no JWT token authentication will be used.'),
            'jitsiAppSecret' => Yii::t('JitsiMeetModule.base', 'Application secret shared with a private Jitsi server used to sign JWT token for authentication. Default: empty, needed if JWT token should be generated.'),
            'menuTitle' => Yii::t('JitsiMeetModule.base', 'Default: Jitsi Meet'),
            'roomPrefix' => Yii::t('JitsiMeetModule.base', 'Default: empty, useful for public Jitsi server'),
            'enableJwt' => Yii::t('JitsiMeetModule.base', 'Enable JWT Authentication'),
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
            $this->jitsiDomain = static::DEFAULT_JITSI_DOMAINS[0];
        }

        $this->jitsiAppID = Yii::$app->getModule('jitsi-meet')->settings->get('jitsiAppID');
        if (empty($this->jitsiAppID)) {
            $this->jitsiAppID = '';
        }
        $this->jitsiAppSecret = Yii::$app->getModule('jitsi-meet')->settings->get('jitsiAppSecret');
        if (empty($this->jitsiAppSecret)) {
            $this->jitsiAppSecret = '';
        }

        $this->roomPrefix = Yii::$app->getModule('jitsi-meet')->settings->get('roomPrefix');
        if (empty($this->roomPrefix)) {
            $this->roomPrefix = '';
        }

        $this->enableJwt = Yii::$app->getModule('jitsi-meet')->settings->get('enableJwt');
        if (empty($this->enableJwt)) {
            $this->enableJwt = 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        Yii::$app->getModule('jitsi-meet')->settings->set('menuTitle', $this->menuTitle);
        Yii::$app->getModule('jitsi-meet')->settings->set('jitsiDomain', $this->jitsiDomain);
        Yii::$app->getModule('jitsi-meet')->settings->set('jitsiAppID', $this->jitsiAppID);
        Yii::$app->getModule('jitsi-meet')->settings->set('jitsiAppSecret', $this->jitsiAppSecret);
        Yii::$app->getModule('jitsi-meet')->settings->set('enableJwt', $this->enableJwt);

        $this->roomPrefix = ucwords((string) preg_replace("/[^A-Za-z0-9]/", '', (string) $this->roomPrefix));
        Yii::$app->getModule('jitsi-meet')->settings->set('roomPrefix', $this->roomPrefix);

        return true;
    }

    public static function defaultJitsiDomainOptions()
    {
        return array_combine(self::DEFAULT_JITSI_DOMAINS, self::DEFAULT_JITSI_DOMAINS);
    }
}
