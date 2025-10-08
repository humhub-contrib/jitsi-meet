<?php

namespace humhubContrib\modules\jitsiMeetCloud8x8\controllers;

use humhubContrib\modules\jitsiMeetCloud8x8\Module;
use humhubContrib\modules\jitsiMeetCloud8x8\components\JaasJwtService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;

/**
 * @property Module $module
 */
class ConfigController extends \humhub\modules\admin\components\Controller
{

    public function actionIndex()
    {
        $form = $this->module->getSettingsForm();

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
        }

        return $this->render('index', ['model' => $form]);
    }

    /**
     * Test JWT generation for debugging
     */
    public function actionTestJwt()
    {
        $settings = $this->module->getSettingsForm();
        
        if ($settings->mode !== 'jaas') {
            Yii::$app->session->setFlash('error', 'JWT test is only available in JaaS mode.');
            return $this->redirect(['index']);
        }

        $testResults = [];
        
        // Test 1: Check configuration
        $testResults['config'] = [
            'appId' => $settings->jaasAppId,
            'kid' => $settings->jaasKid,
            'privateKeyPath' => getenv('HUMHUB_JAAS_PRIVATE_KEY_PATH') ?: $settings->jaasPrivateKeyPath,
            'domain' => $settings->jaasDomain,
        ];

        // Test 2: Check private key file
        $keyPath = $testResults['config']['privateKeyPath'];
        $testResults['keyFile'] = [
            'path' => $keyPath,
            'exists' => file_exists($keyPath),
            'readable' => is_readable($keyPath),
            'size' => file_exists($keyPath) ? filesize($keyPath) : 0,
        ];

        if ($testResults['keyFile']['exists'] && $testResults['keyFile']['readable']) {
            $keyContent = @file_get_contents($keyPath);
            $testResults['keyFile']['content'] = $keyContent !== false ? 'Present' : 'Failed to read';
            $testResults['keyFile']['isPem'] = strpos($keyContent, 'BEGIN PRIVATE KEY') !== false || strpos($keyContent, 'BEGIN RSA PRIVATE KEY') !== false;
        }

        // Test 3: Generate test JWT
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
            $testJwt = JaasJwtService::createToken($user, 'test-room', true);
            
            $testResults['jwt'] = [
                'generated' => !empty($testJwt),
                'token' => $testJwt,
                'length' => strlen($testJwt),
            ];

            // Test 4: Decode JWT to verify structure
            if (!empty($testJwt)) {
                try {
                    $decoded = JWT::decode($testJwt, new Key('', 'RS256'));
                    $testResults['jwt']['decoded'] = [
                        'header' => $decoded->header ?? null,
                        'payload' => $decoded->payload ?? null,
                    ];
                } catch (\Exception $e) {
                    $testResults['jwt']['decodeError'] = $e->getMessage();
                }
            }
        } else {
            $testResults['jwt'] = ['error' => 'User not logged in'];
        }

        return $this->render('test-jwt', [
            'testResults' => $testResults,
            'model' => $settings,
        ]);
    }
}

?>