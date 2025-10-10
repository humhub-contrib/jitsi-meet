<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhubContrib\modules\jitsiMeetCloud8x8\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

class ManageRecordings extends BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        Space::USERGROUP_USER,
        Space::USERGROUP_MODERATOR,
        User::USERGROUP_FRIEND,
        User::USERGROUP_USER,
        User::USERGROUP_GUEST,
    ];

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_DENY;

    /**
     * @inheritdoc
     */
    protected $moduleId = 'jitsi-meet-cloud-8x8';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('JitsiMeetCloud8x8Module.base', 'Can manage recordings');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('JitsiMeetCloud8x8Module.base', 'Allows users to download, delete, and manage video recordings. This is a sensitive permission that should be restricted to administrators only.');
    }
}
