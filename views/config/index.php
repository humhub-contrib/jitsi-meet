<?php

use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model \humhubContrib\modules\jitsiMeet\models\SettingsForm */

$script = <<< JS
$(document).ready(function () {
    function displayJwtParams(){
        if ( $('#settingsform-enablejwt').is(':checked') ) {
          $('.field-settingsform-jitsiappid').show();
          $('.field-settingsform-jitsiappsecret').show();
        } else {
          $('.field-settingsform-jitsiappid').hide();
          $('.field-settingsform-jitsiappsecret').hide();
        }
    }

    displayJwtParams();
    $(document.body).on('change', '#settingsform-enablejwt', function(){ displayJwtParams(); });
});
JS;
$this->registerJs($script);

?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('JitsiMeetModule.base', '<strong>Jitsi</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

        <?= $form->field($model, 'jitsiDomain'); ?>
        <?= $form->field($model, 'roomPrefix'); ?>
        <?= $form->field($model, 'menuTitle'); ?>
        <?= $form->field($model, 'enableJwt')->checkbox(); ?>
        <?= $form->field($model, 'jitsiAppID'); ?>
        <?= $form->field($model, 'jitsiAppSecret'); ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
