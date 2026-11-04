<?php

namespace humhubContrib\modules\jitsiMeet\models;

use Yii;

class JoinRoomForm extends \yii\base\Model
{
    public $room;
    public $newWindow;

    public function rules()
    {
        return [
            [['room'], 'string'],
            [['newWindow'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'room' => Yii::t('JitsiMeetModule.base', 'Name of conference room'),
            'newWindow' => Yii::t('JitsiMeetModule.base', 'Open in new window?'),
        ];
    }
}
