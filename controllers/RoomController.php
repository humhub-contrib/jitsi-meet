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

        // Check if URL has config.startSilent fragment (for silent join)
        $startSilent = false;
        $requestUri = Yii::$app->request->getUrl();
        if (strpos($requestUri, '#config.startSilent=true') !== false || 
            Yii::$app->request->get('startSilent') === 'true') {
            $startSilent = true;
            Yii::info("RoomController::actionOpen - Silent join requested", 'jitsi-meet');
        }

        // Default modal route and params
        $jitsiRoomUrl = ['/jitsi-meet-cloud-8x8/room/modal', 'name' => $name];
        if ($startSilent) {
            $jitsiRoomUrl['startSilent'] = 'true';
        }

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
            $isModerator = $this->isModeratorForCurrentContext($name);
            
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
            'startSilent' => $startSilent,
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
        $startSilent = Yii::$app->request->get('startSilent') === 'true';

        Yii::info("RoomController::actionModal - Room: {$name}, JWT present: " . (!empty($jwt) ? 'yes' : 'no') . ", StartSilent: " . ($startSilent ? 'yes' : 'no'), 'jitsi-meet');

        if (!Yii::$app->request->isAjax) {
            Yii::info('RoomController::actionModal - Not AJAX request, redirecting', 'jitsi-meet');
            $redirectUrl = ['open', 'name' => $name];
            if ($startSilent) {
                $redirectUrl['startSilent'] = 'true';
            }
            return $this->redirect($redirectUrl);
        }

        return $this->renderAjax('modal', [
            'jwt' => $jwt,
            'name' => $name,
            'startSilent' => $startSilent,
        ]);

    }

    /**
     * Redirect action - handles old vpaas-magic-cookie URL format and redirects to new /conference/{name} format
     * This ensures backward compatibility with links generated by recording bot
     */
    public function actionRedirect()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));
        $appId = Yii::$app->request->get('appId');
        
        Yii::info("RoomController::actionRedirect - Redirecting from old format (appId: {$appId}, room: {$name}) to new format", 'jitsi-meet');
        
        // Redirect to the new /conference/{name} format
        return $this->redirect(['/conference/' . $name], 301); // 301 permanent redirect
    }

    /**
     * Invite service action - custom invite service for Jitsi Meet
     * This endpoint handles invitation requests and can also return room URL information
     * Note: inviteServiceUrl is primarily for sending invitations, but we can use it to override URL format
     * Handles both GET and POST requests
     */
    public function actionInvite()
    {
        $request = Yii::$app->request;
        $method = $request->method;
        
        // For POST requests, this is an invitation send request
        // For GET requests, this might be a request for room URL
        if ($method === 'POST') {
            // Handle invitation send request
            $invitees = $request->post('invitees', []);
            Yii::info("RoomController::actionInvite - POST request with invitees: " . json_encode($invitees), 'jitsi-meet');
            
            // Get room name from referer or other source
            // The room name might be in the full URL format
            $referer = $request->getReferrer();
            $roomName = null;
            
            if ($referer) {
                // Extract room name from referer URL
                if (preg_match('/\/conference\/([^\/\?#]+)/', $referer, $matches)) {
                    $roomName = $matches[1];
                } elseif (preg_match('/vpaas-magic-cookie-[a-f0-9]{32}\/([^\/\?#]+)/', $referer, $matches)) {
                    $roomName = $matches[1];
                }
            }
            
            // Also check if room name is passed directly
            $roomName = $roomName ?: $request->post('room') ?: $request->get('room');
            
            if ($roomName) {
                // Extract just the room name if it contains app ID
                if (preg_match('/vpaas-magic-cookie-[a-f0-9]{32}\/(.+)$/', $roomName, $matches)) {
                    $roomName = $matches[1];
                } elseif (preg_match('/^(.+)\/(.+)$/', $roomName, $matches)) {
                    $roomName = $matches[2];
                }
                $roomName = $this->fixRoomName($roomName);
            }
            
            // Return success response
            return $this->asJson([
                'success' => true,
                'roomName' => $roomName,
                'inviteURL' => $roomName ? $this->module->getRoomUrl($roomName, true) : null,
            ]);
        } else {
            // GET request - return room URL information
            $roomName = $request->get('room') ?: $request->get('name');
            
            // If room name contains app ID prefix, extract just the room name
            if ($roomName && preg_match('/vpaas-magic-cookie-[a-f0-9]{32}\/(.+)$/', $roomName, $matches)) {
                $roomName = $matches[1];
            } elseif ($roomName && preg_match('/^(.+)\/(.+)$/', $roomName, $matches)) {
                $roomName = $matches[2];
            }
            
            if ($roomName) {
                $roomName = $this->fixRoomName($roomName);
                $conferenceUrl = $this->module->getRoomUrl($roomName, true);
                $conferenceUrlSilent = $this->module->getRoomUrlSilent($roomName, true);
                
                return $this->asJson([
                    'inviteURL' => $conferenceUrl,
                    'inviteURLSilent' => $conferenceUrlSilent,
                    'roomName' => $roomName,
                ]);
            }
        }
        
        Yii::info("RoomController::actionInvite - Request processed, Method: {$method}", 'jitsi-meet');
        return $this->asJson(['success' => true]);
    }

    /**
     * Share action - returns room URL information for sharing/invitations
     * This ensures correct URL format is used instead of vpaas-magic-cookie format
     */
    public function actionShare()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));
        
        if (empty($name)) {
            return $this->asJson([
                'error' => 'Room name is required'
            ]);
        }

        $roomUrl = $this->module->getRoomUrl($name, true);
        $roomUrlSilent = $this->module->getRoomUrlSilent($name, true);
        $dialInNumbersUrl = $this->module->getDialInNumbersUrl($name, true);

        return $this->asJson([
            'roomName' => $name,
            'roomUrl' => $roomUrl,
            'roomUrlSilent' => $roomUrlSilent,
            'dialInNumbersUrl' => $dialInNumbersUrl,
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

    private function isModeratorForCurrentContext(?string $roomName = null): bool
    {
        /**
         * IMPORTANT:
         * Join rules vs moderator rules:
         *
         * - Any authenticated HumHub member can **join or start** a video chat.
         *   (Join is not restricted by this method, only the moderator flag.)
         * - Moderator status requires BOTH:
         *      • Having the "Can be moderator" permission (controlled per group in HumHub)
         *      • AND being either:
         *          - System admin (always allowed)
         *          - OR the "chat starter" (first user who opened the room name)
         *
         * - Guests are never moderators.
         * - Recording / livestreaming capabilities are STILL controlled separately
         *   via the dedicated permissions in `JaasJwtService::$features`.
         *
         * This keeps "join" completely open to all members while giving you
         * explicit control over who can be moderators via the permission system.
         */

        Yii::info("isModeratorForCurrentContext: evaluating moderator status for room='{$roomName}'", 'jitsi-meet');

        // Guest users are never moderators
        if (Yii::$app->user->isGuest) {
            Yii::info('isModeratorForCurrentContext: guest user => moderator=false', 'jitsi-meet');
            return false;
        }

        $user = Yii::$app->user->getIdentity();

        // Check if user has the "Can be moderator" permission
        $hasModeratorPermission = Yii::$app->user->can(\humhubContrib\modules\jitsiMeetCloud8x8\permissions\CanBeModerator::class);
        Yii::info("isModeratorForCurrentContext: user {$user->id} has CanBeModerator permission: " . ($hasModeratorPermission ? 'yes' : 'no'), 'jitsi-meet');

        // Global admins are always moderators (bypass permission check for admins)
        if ($user->isSystemAdmin()) {
            Yii::info("isModeratorForCurrentContext: user {$user->id} is system admin => moderator=true", 'jitsi-meet');
            return true;
        }

        // If user doesn't have moderator permission, they can never be moderator
        // (even if they're the chat starter)
        if (!$hasModeratorPermission) {
            Yii::info("isModeratorForCurrentContext: user {$user->id} lacks CanBeModerator permission => moderator=false", 'jitsi-meet');
            return false;
        }

        // User has permission, now check if they're the chat starter
        if (!empty($roomName)) {
            $cache = Yii::$app->cache ?? null;
            if ($cache !== null) {
                $cacheKey = 'jitsiMeetCloud8x8:roomCreator:' . $roomName;
                $creatorId = $cache->get($cacheKey);

                if ($creatorId === false) {
                    // No creator yet -> current user becomes the chat starter
                    // TTL 1 hour; adjust if you want longer/shorter "ownership"
                    $cache->set($cacheKey, $user->id, 3600);
                    Yii::info("isModeratorForCurrentContext: user {$user->id} set as chat starter for room '{$roomName}' (cacheKey={$cacheKey}) => moderator=true", 'jitsi-meet');
                    return true;
                }

                if ((int)$creatorId === (int)$user->id) {
                    Yii::info("isModeratorForCurrentContext: user {$user->id} is existing chat starter for room '{$roomName}' (cacheKey={$cacheKey}) => moderator=true", 'jitsi-meet');
                    return true;
                }

                Yii::info("isModeratorForCurrentContext: user {$user->id} is NOT chat starter for room '{$roomName}' (creatorId={$creatorId}, cacheKey={$cacheKey}) => moderator=false", 'jitsi-meet');
            } else {
                Yii::warning("isModeratorForCurrentContext: cache component not available for room '{$roomName}', cannot determine chat starter => moderator=false", 'jitsi-meet');
            }
        }

        // User has permission but is not chat starter and not admin
        Yii::info("isModeratorForCurrentContext: user {$user->id} has permission but is not chat starter => moderator=false", 'jitsi-meet');
        return false;
    }
}
