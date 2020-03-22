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
            ['login'],
        ];
    }

    public function actionIndex()
    {
        $model = new JoinRoomForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->redirect(['open', 'name' => $model->room]);
        }

        return $this->render('index', [
            'model' => $model,
            'jitsiDomain' => $this->module->getSettingsForm()->jitsiDomain
        ]);
    }

    public function actionOpen()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));

        $this->layout = "@humhub/modules/user/views/layouts/main";
        return $this->render('open', [
            'jitsiDomain' => $this->module->getSettingsForm()->jitsiDomain,
            'name' => $name
        ]);
    }

    public function actionModal()
    {
        $name = $this->fixRoomName(Yii::$app->request->get('name'));

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['open', 'name' => $name]);
        }

        return $this->renderAjax('modal', [
            'jitsiDomain' => 'meet.jit.si',
            'jwt' => '',
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