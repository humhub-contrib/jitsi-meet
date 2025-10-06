<?php

namespace humhubContrib\modules\jitsiMeet\components;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use humhubContrib\modules\jitsiMeet\Module;
use Yii;

class JaasJwtService
{
    /**
     * Generate a JaaS JWT for a user and room.
     *
     * @param \humhub\modules\user\models\User $user
     * @param string $roomName single-level room name (no slash)
     * @param bool $isModerator
     * @return string JWT token or empty string on failure
     */
    public static function createToken($user, $roomName, $isModerator = false)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('jitsi-meet');
        $settings = $module->getSettingsForm();

        $appId = $settings->jaasAppId;
        $kid = $settings->jaasKid;
        $privateKeyPath = getenv('HUMHUB_JAAS_PRIVATE_KEY_PATH') ?: $settings->jaasPrivateKeyPath;

        // Enhanced logging for debugging
        Yii::info('JaaS JWT Generation Started', 'jitsi-meet');
        Yii::info("AppId: {$appId}, Kid: {$kid}, KeyPath: {$privateKeyPath}", 'jitsi-meet');

        if (!$appId || !$kid || !$privateKeyPath) {
            Yii::error('JaaS JWT not generated: missing appId/kid/private key path.', 'jitsi-meet');
            return '';
        }

        if (!is_readable($privateKeyPath)) {
            Yii::error("JaaS JWT not generated: private key file not readable at {$privateKeyPath}", 'jitsi-meet');
            return '';
        }

        $privateKey = @file_get_contents($privateKeyPath);
        if ($privateKey === false || trim($privateKey) === '') {
            Yii::error("JaaS JWT not generated: failed reading private key from {$privateKeyPath}", 'jitsi-meet');
            return '';
        }

        $issuedAt = time();
        $notBefore = $issuedAt - 5;
        $expire = $issuedAt + 300; // 5 minutes

        // For JaaS, the room in JWT should be just the room name (without appId prefix)
        // The appId prefix is handled by the domain/URL structure
        $fullRoom = $roomName;

        // CRITICAL FIX: Use boolean values instead of strings for features
        $features = [
            'recording' => (bool)$settings->jaasEnableRecording,
            'livestreaming' => (bool)$settings->jaasEnableLivestreaming,
            'moderation' => (bool)$settings->jaasEnableModeration,
        ];

        $payload = [
            'aud' => 'jitsi',
            'iss' => 'chat',
            'sub' => $appId,
            'room' => $fullRoom,
            'nbf' => $notBefore,
            'exp' => $expire,
            'context' => [
                'user' => [
                    'id' => (string)$user->id,
                    'name' => (string)$user->displayName,
                    'avatar' => (string)($user->getProfileImage() ? $user->getProfileImage()->getUrl() : ''),
                    'email' => (string)$user->email,
                    // CRITICAL FIX: Use boolean instead of string for moderator
                    'moderator' => (bool)$isModerator,
                ],
                'features' => $features,
                'room' => [
                    'regex' => false
                ],
            ],
        ];

        // CRITICAL FIX: Ensure kid format matches 8x8 requirements (appId/kid)
        $fullKid = $appId . '/' . $kid;
        $headers = [
            'kid' => $fullKid,
            'typ' => 'JWT',
            'alg' => 'RS256',
        ];

        // Log payload for debugging (always log for now to debug the auth issue)
        Yii::info('JaaS JWT Payload: ' . json_encode($payload, JSON_PRETTY_PRINT), 'jitsi-meet');
        Yii::info('JaaS JWT Headers: ' . json_encode($headers, JSON_PRETTY_PRINT), 'jitsi-meet');

        try {
            $jwt = JWT::encode($payload, $privateKey, 'RS256', null, $headers);
            Yii::info('JaaS JWT generated successfully', 'jitsi-meet');
            return $jwt;
        } catch (\Throwable $e) {
            Yii::error('JaaS JWT generation failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 'jitsi-meet');
            return '';
        }
    }
}


