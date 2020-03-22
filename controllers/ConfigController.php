<?php

namespace humhubContrib\modules\jitsiMeet\controllers;

use humhubContrib\modules\jitsiMeet\Module;
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
}

?>