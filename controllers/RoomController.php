<?php

namespace humhubContrib\modules\jitsiMeetCloud8x8\controllers;

use Firebase\JWT\JWT;
use humhub\components\Controller;
use humhubContrib\modules\jitsiMeetCloud8x8\models\JoinRoomForm;
use humhubContrib\modules\jitsiMeetCloud8x8\Module;
use humhubContrib\modules\jitsiMeetCloud8x8\components\JaasJwtService;
use humhubContrib\modules\jitsiMeetCloud8x8\permissions\CanAccess;
use Yii;

/**
 * @property Module $module
 */
class RoomController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            ['permissions' => [CanAccess::class], 'actions' => ['index']]
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
        $settings = $this->module->getSettingsForm();

        // Enhanced logging for debugging
        Yii::info("RoomController::actionOpen - Room: {$name}", 'jitsi-meet');

        // Default modal route and params
        $jitsiRoomUrl = ['/jitsi-meet-cloud-8x8/room/modal', 'name' => $name];

        // Determine mode
        $mode = $settings->mode ?: 'self_hosted';
        Yii::info("RoomController::actionOpen - Mode: {$mode}", 'jitsi-meet');

        if ($mode === 'jaas') {
            Yii::info('RoomController::actionOpen - JaaS mode selected', 'jitsi-meet');
            
            if (Yii::$app->user->isGuest) {
                Yii::info('RoomController::actionOpen - User is guest, requiring login', 'jitsi-meet');
                Yii::$app->user->loginRequired();
            }
            
            $user = Yii::$app->user->getIdentity();
            $isModerator = $this->isModeratorForCurrentContext();
            
            Yii::info("RoomController::actionOpen - User: {$user->displayName} (ID: {$user->id}), Moderator: " . ($isModerator ? 'true' : 'false'), 'jitsi-meet');
            
            $jwt = JaasJwtService::createToken($user, $name, $isModerator);
            if (!empty($jwt)) {
                $jitsiRoomUrl['jwt'] = $jwt;
                Yii::info('RoomController::actionOpen - JWT generated and added to URL', 'jitsi-meet');
            } else {
                Yii::error('RoomController::actionOpen - JWT generation failed', 'jitsi-meet');
            }
        } else {
            Yii::info('RoomController::actionOpen - Self-hosted mode selected', 'jitsi-meet');
            // Legacy HS256 path
            if ($this->module->getSettingsForm()->enableJwt) {
                if (Yii::$app->user->isGuest) {
                    Yii::$app->user->loginRequired();
                }
                $jitsiRoomUrl['jwt'] = $this->createJWT($name);
                Yii::info('RoomController::actionOpen - Legacy JWT generated', 'jitsi-meet');
            }
        }

        $domain = $mode === 'jaas' ? $settings->jaasDomain : $settings->jitsiDomain;
        Yii::info("RoomController::actionOpen - Using domain: {$domain}", 'jitsi-meet');

        $this->layout = "@humhub/modules/user/views/layouts/main";
        return $this->render('open', [
            'jitsiDomain' => $domain,
            'jitsiRoomUrl' => $jitsiRoomUrl,
        ]);
    }

    private function createJWT($roomName)
    {
        // security measure: if the current user is not authenticated, don‘t create a token
        if (Yii::$app->user->isGuest) {
            return "";
        }
        $user = Yii::$app->user->getIdentity();
        // security measure: if we can‘t get the user‘s identity, don‘t create a token
        if (is_null($user)) {
            return "";
        }
        $userEmail = $user->email;
        $userName = $user->displayName;
        $issuedAt = time();
        $notBefore = $issuedAt + 10; //Adding 10 seconds
        $expire = $notBefore + 60; // Adding 60 seconds
        $jitsi = $this->module->getSettingsForm()->jitsiDomain;
        $appID = $this->module->getSettingsForm()->jitsiAppID;
        $prefix = $this->module->getSettingsForm()->roomPrefix;
        $token = [
            'iss' => $appID,
            'aud' => $jitsi,
            'sub' => $jitsi,
            'exp' => $expire,
            'room' => $prefix . $roomName,
            'context' => [
                'user' => [
                    'name' => $userName,
                    'email' => $userEmail,
                ],
            ],
        ];

        return JWT::encode($token, (string) $this->module->getSettingsForm()->jitsiAppSecret, 'HS256');
    }

    public function actionModal()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));
        $jwt = Yii::$app->request->get('jwt');

        Yii::info("RoomController::actionModal - Room: {$name}, JWT present: " . (!empty($jwt) ? 'yes' : 'no'), 'jitsi-meet');

        if (!Yii::$app->request->isAjax) {
            Yii::info('RoomController::actionModal - Not AJAX request, redirecting', 'jitsi-meet');
            return $this->redirect(['open', 'name' => $name]);
        }

        return $this->renderAjax('modal', [
            'jwt' => $jwt,
            'name' => $name
        ]);

    }

    private function fixRoomName($name)
    {

        if (empty($name)) {
            $name = Yii::$app->user->getIdentity()->profile->firstname;
            $name .= " Square";
        }
        $name = ucwords($name);
        $name = preg_replace("/[^A-Za-z0-9]/", '', $name);

        return $name;
    }

    private function isModeratorForCurrentContext(): bool
    {
        // Simple default: logged-in users can be moderators; adjust for space roles as needed
        if (Yii::$app->user->isGuest) {
            return false;
        }
        // Extend here: map HumHub space owners/admins to moderators
        return true;
    }
}
