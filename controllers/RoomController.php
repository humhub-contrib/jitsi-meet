<?php

namespace humhubContrib\modules\jitsiMeet\controllers;

use Firebase\JWT\JWT;
use humhub\components\Controller;
use humhubContrib\modules\jitsiMeet\models\JoinRoomForm;
use humhubContrib\modules\jitsiMeet\Module;
use Yii;

/**
 * @property Module $module
 */
class RoomController extends Controller
{
    public function getAccessRules()
    {
        return [
            ['login' => ['index']]
        ];
    }

    public function actionIndex()
    {
        $model = new JoinRoomForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect(['open', 'name' => $this->fixRoomName($model->room)]);
        }

        return $this->render('index', [
            'model' => $model,
            'jitsiDomain' => $this->module->getSettingsForm()->jitsiDomain
        ]);
    }

    public function actionOpen()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));

        $jwt = '';
        if ($this->module->getSettingsForm()->jitsiAppID != '') {
            $jwt = $this->createJWT($name);
        }
        $this->layout = "@humhub/modules/user/views/layouts/main";
        return $this->render('open', [
            'jitsiDomain' => $this->module->getSettingsForm()->jitsiDomain,
            'jwt' => $jwt,
            'name' => $name
        ]);
    }

    private function createJWT($roomName)
    {
        $appID = $this->module->getSettingsForm()->jitsiAppID;
        $userName = '';
        $userEmail = '';
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
            $email = $user->email;
            $userName = $user->displayName;
        }
        $issuedAt = time();
        $notBefore = $issuedAt + 10; //Adding 10 seconds
        $expire = $notBefore + 60; // Adding 60 seconds
        $jitsi = $this->module->getSettingsForm()->jitsiDomain;
        $token = array(
            'iss' => $appID,
            'aud' => $jitsi,
            'sub' => $jitsi,
            'exp' => $expire,
            'room' => $roomName,
            'context' => array(
                'user' => array(
                    'name' => $userName,
                    'email' => $userEmail
                )
            )
        );
        $jwt = JWT::encode($token, $this->module->getSettingsForm()->jitsiAppSecret);
        return $jwt;
    }

    public function actionModal()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));
        $jwt = Yii::$app->request->get('jwt');

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['open', 'name' => $name]);
        }

        return $this->renderAjax('modal', [
            'jitsiDomain' => 'meet.jit.si',
            'jwt' => $jwt,
            'name' => $name
        ]);

    }

    private function fixRoomName($name) {

        if (empty($name)) {
            $name = Yii::$app->user->getIdentity()->profile->firstname;
            $name .= " Square";
        }
        $name = ucwords($name);
        $name = preg_replace("/[^A-Za-z0-9]/", '', $name);
        
        return $name;
    }
    
}
