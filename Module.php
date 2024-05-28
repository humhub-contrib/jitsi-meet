<?php

namespace humhubContrib\modules\jitsiMeet;

use humhubContrib\modules\jitsiMeet\models\SettingsForm;
use yii\helpers\Url;

class Module extends \humhub\components\Module
{
    private $_settingsForm = null;

    /**
     * @return SettingsForm
     */
    public function getSettingsForm()
    {
        if ($this->_settingsForm === null) {
            $this->_settingsForm = new SettingsForm();
        }

        return $this->_settingsForm;
    }


    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/jitsi-meet/config']);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        return [
            new permissions\CanAccess(),
        ];
    }

}
