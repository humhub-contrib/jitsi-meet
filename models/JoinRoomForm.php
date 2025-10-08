<?php

namespace humhubContrib\modules\jitsiMeetCloud8x8\models;

use Firebase\JWT\JWT;
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
            'room' => Yii::t('JitsiMeetCloud8x8Module.base', 'Name'),
            'newWindow' => Yii::t('JitsiMeetCloud8x8Module.base', 'Open in new window?'),
        ];
    }

   /**
     * WIP!
     */
    public function getJwt() {
        $key = "my_jitsi_app_secret2";
        $payload = [
            "iss" => "my_web_client",
            "aud" => "my_jitsi_app_id",
            "sub" => "meet.jitsi",
            "room" => "*",
            "context" => [
                "user" => [
                    'avatar' => "https:/gravatar.com/avatar/abc123",
                    'name' => 'XXXYYY',
                    'email' => 'jdoe@example.com',
                    //                    'id' => 'asd'
                ],
            ]
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

    }
}