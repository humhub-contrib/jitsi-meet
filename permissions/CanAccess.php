<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhubContrib\modules\jitsiMeetCloud8x8\permissions;

use humhub\libs\BasePermission;
use humhub\modules\user\models\Group;
use Yii;

class CanAccess extends BasePermission
{

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [];

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    protected $moduleId = 'jitsi-meet-cloud-8x8';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('JitsiMeetCloud8x8Module.base', 'Can access Jitsi Meet');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('JitsiMeetCloud8x8Module.base', 'Can access Jitsi Meet from main navigation.');
    }

}
