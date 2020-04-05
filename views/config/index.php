<?php

use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model \humhubContrib\modules\jitsiMeet\models\SettingsForm */
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('JitsiMeetModule.base', '<strong>Jitsi</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

        <?= $form->field($model, 'jitsiDomain'); ?>
        <?= $form->field($model, 'jitsiAppID'); ?>
        <?= $form->field($model, 'jitsiAppSecret'); ?>
        <?= $form->field($model, 'roomPrefix'); ?>
        <?= $form->field($model, 'menuTitle'); ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
