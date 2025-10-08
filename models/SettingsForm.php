<?php

namespace humhubContrib\modules\jitsiMeetCloud8x8\models;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{

    public $jitsiDomain;
    public $menuTitle;
    public $roomPrefix;
    public $jitsiAppID;
    public $jitsiAppSecret;
    public $enableJwt;

    // Dual-mode support
    public $mode; // self_hosted | jaas

    // JaaS-specific settings
    public $jaasAppId; // sub
    public $jaasKid; // kid
    public $jaasPrivateKeyPath; // filesystem path to RS256 private key
    public $jaasDomain; // usually 8x8.vc
    public $jaasEnableRecording;
    public $jaasEnableLivestreaming;
    public $jaasEnableModeration;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['jitsiDomain', 'string'],
            [['menuTitle', 'jitsiAppID', 'jitsiAppSecret', 'roomPrefix'], 'string'],
            ['enableJwt', 'boolean'],
            [['jitsiAppID', 'jitsiAppSecret'], 'required', 'when' => function($model) {
                return $model->enableJwt;
            }, 'whenClient' => "function (attribute, value) {
                return $('#settingsform-enablejwt').is(':checked');
            }"],
            [['mode'], 'in', 'range' => ['self_hosted', 'jaas']],
            [['jaasAppId', 'jaasKid', 'jaasPrivateKeyPath', 'jaasDomain'], 'string'],
            [['jaasEnableRecording', 'jaasEnableLivestreaming', 'jaasEnableModeration'], 'boolean'],
            
            // JaaS mode validation
            [['jaasAppId', 'jaasKid', 'jaasPrivateKeyPath'], 'required', 'when' => function($model) {
                return $model->mode === 'jaas';
            }, 'whenClient' => "function (attribute, value) {
                return $('#settingsform-mode').val() === 'jaas';
            }"],
            
            // Custom validation for JaaS settings
            ['jaasAppId', 'validateJaasAppId'],
            ['jaasKid', 'validateJaasKid'],
            ['jaasPrivateKeyPath', 'validateJaasPrivateKeyPath'],
        ];
    }

    /**
     * Validate JaaS App ID format
     */
    public function validateJaasAppId($attribute, $params)
    {
        if ($this->mode === 'jaas' && !empty($this->jaasAppId)) {
            if (!preg_match('/^vpaas-magic-cookie-[a-f0-9]{32}$/', $this->jaasAppId)) {
                $this->addError($attribute, 'JaaS App ID must be in format: vpaas-magic-cookie-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
            }
        }
    }

    /**
     * Validate JaaS Kid format
     */
    public function validateJaasKid($attribute, $params)
    {
        if ($this->mode === 'jaas' && !empty($this->jaasKid)) {
            if (!preg_match('/^[a-f0-9]{6}$/', $this->jaasKid)) {
                $this->addError($attribute, 'JaaS API Key (kid) must be 6 hexadecimal characters');
            }
        }
    }

    /**
     * Validate JaaS Private Key Path
     */
    public function validateJaasPrivateKeyPath($attribute, $params)
    {
        if ($this->mode === 'jaas' && !empty($this->jaasPrivateKeyPath)) {
            $keyPath = getenv('HUMHUB_JAAS_PRIVATE_KEY_PATH') ?: $this->jaasPrivateKeyPath;
            
            if (!file_exists($keyPath)) {
                $this->addError($attribute, "Private key file does not exist at: {$keyPath}");
                return;
            }
            
            if (!is_readable($keyPath)) {
                $this->addError($attribute, "Private key file is not readable at: {$keyPath}");
                return;
            }
            
            $keyContent = @file_get_contents($keyPath);
            if ($keyContent === false || trim($keyContent) === '') {
                $this->addError($attribute, "Private key file is empty or could not be read at: {$keyPath}");
                return;
            }
            
            // Basic validation that it looks like a private key
            if (strpos($keyContent, 'BEGIN PRIVATE KEY') === false && strpos($keyContent, 'BEGIN RSA PRIVATE KEY') === false) {
                $this->addError($attribute, "Private key file does not appear to be a valid PEM format private key");
            }
        }
    }

    public function attributeHints()
    {
        return [
            'jitsiDomain' => Yii::t('JitsiMeetCloud8x8Module.base', 'Default is meet.jit.si without "https://" prefix.'),
            'jitsiAppID' => Yii::t('JitsiMeetCloud8x8Module.base', 'Application ID shared with a private Jitsi server used to generate JWT token for authentication. Default: empty, no JWT token authentication will be used.'),
            'jitsiAppSecret' => Yii::t('JitsiMeetCloud8x8Module.base', 'Application secret shared with a private Jitsi server used to sign JWT token for authentication. Default: empty, needed if JWT token should be generated.'),
            'menuTitle' => Yii::t('JitsiMeetCloud8x8Module.base', 'Default: Jitsi Meet'),
            'roomPrefix' => Yii::t('JitsiMeetCloud8x8Module.base', 'Default: empty, useful for public Jitsi server'),
            'enableJwt' => Yii::t('JitsiMeetCloud8x8Module.base', 'Enable JWT Authentication'),
            'mode' => Yii::t('JitsiMeetCloud8x8Module.base', 'Select meeting mode: self-hosted Jitsi or 8x8 JaaS'),
            'jaasAppId' => Yii::t('JitsiMeetCloud8x8Module.base', '8x8 JaaS App ID (sub).'),
            'jaasKid' => Yii::t('JitsiMeetCloud8x8Module.base', '8x8 JaaS API Key (kid).'),
            'jaasPrivateKeyPath' => Yii::t('JitsiMeetCloud8x8Module.base', 'Filesystem path to the RS256 private key (not stored in DB).'),
            'jaasDomain' => Yii::t('JitsiMeetCloud8x8Module.base', '8x8 JaaS domain, default: 8x8.vc'),
            'jaasEnableRecording' => Yii::t('JitsiMeetCloud8x8Module.base', 'Enable recording feature for JaaS users.'),
            'jaasEnableLivestreaming' => Yii::t('JitsiMeetCloud8x8Module.base', 'Enable livestreaming feature for JaaS users.'),
            'jaasEnableModeration' => Yii::t('JitsiMeetCloud8x8Module.base', 'Enable moderation features for JaaS users.'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->menuTitle = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('menuTitle');
        if (empty($this->menuTitle)) {
            $this->menuTitle = 'Jitsi Meet';
        }

        $this->jitsiDomain = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jitsiDomain');
        if (empty($this->jitsiDomain)) {
            $this->jitsiDomain = 'meet.jit.si';
        }

        $this->jitsiAppID = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jitsiAppID');
        if (empty($this->jitsiAppID)) {
            $this->jitsiAppID = '';
        }
        $this->jitsiAppSecret = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jitsiAppSecret');
        if (empty($this->jitsiAppSecret)) {
            $this->jitsiAppSecret = '';
        }

        $this->roomPrefix = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('roomPrefix');
        if (empty($this->roomPrefix)) {
            $this->roomPrefix = '';
        }

        $this->enableJwt = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('enableJwt');
        if (empty($this->enableJwt)) {
            $this->enableJwt = 0;
        }

        // New: dual-mode and JaaS settings
        $this->mode = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('mode');
        if (empty($this->mode)) {
            $this->mode = 'self_hosted';
        }

        $this->jaasAppId = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasAppId');
        if (empty($this->jaasAppId)) {
            $this->jaasAppId = '';
        }

        $this->jaasKid = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasKid');
        if (empty($this->jaasKid)) {
            $this->jaasKid = '';
        }

        $this->jaasPrivateKeyPath = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasPrivateKeyPath');
        if (empty($this->jaasPrivateKeyPath)) {
            $this->jaasPrivateKeyPath = '';
        }

        $this->jaasDomain = Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasDomain');
        if (empty($this->jaasDomain)) {
            $this->jaasDomain = '8x8.vc';
        }

        $this->jaasEnableRecording = (int) Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasEnableRecording');
        $this->jaasEnableLivestreaming = (int) Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasEnableLivestreaming');
        $this->jaasEnableModeration = (int) Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->get('jaasEnableModeration');
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('menuTitle', $this->menuTitle);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jitsiDomain', $this->jitsiDomain);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jitsiAppID', $this->jitsiAppID);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jitsiAppSecret', $this->jitsiAppSecret);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('enableJwt', $this->enableJwt);

        // Dual-mode and JaaS settings
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('mode', $this->mode);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasAppId', $this->jaasAppId);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasKid', $this->jaasKid);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasPrivateKeyPath', $this->jaasPrivateKeyPath);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasDomain', $this->jaasDomain);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasEnableRecording', (int)$this->jaasEnableRecording);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasEnableLivestreaming', (int)$this->jaasEnableLivestreaming);
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('jaasEnableModeration', (int)$this->jaasEnableModeration);

        $this->roomPrefix = ucwords(preg_replace("/[^A-Za-z0-9]/", '', $this->roomPrefix));
        Yii::$app->getModule('jitsi-meet-cloud-8x8')->settings->set('roomPrefix', $this->roomPrefix);

        return true;
    }
}
