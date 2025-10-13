<?php

use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhubContrib\modules\jitsiMeet\models\SettingsForm;

/* @var $model SettingsForm */

$script = <<< JS
$(document).ready(function () {
    function displayJwtParams(){
        if ( $('#settingsform-enablejwt').is(':checked') ) {
          $('.field-settingsform-jitsiappid').removeClass('d-none');
          $('.field-settingsform-jitsiappsecret').removeClass('d-none');
        } else {
          $('.field-settingsform-jitsiappid').addClass('d-none');
          $('.field-settingsform-jitsiappsecret').addClass('d-none');
        }
    }

    function toggleJitsiDomainTextInput() {
        var dropdown = $('#settingsform-jitsidomain');
        var textInput = $('.field-settingsform-jitsidomain-text');
        textInput.children('.form-label').detach();

        if (dropdown.val() === '') {
            textInput.removeClass('d-none');
        } else {
            textInput.addClass('d-none');
        }
    }

    function disabledJitsiDomainField() {
        var dropdown = $('#settingsform-jitsidomain');
        var textInputField = $('#settingsform-jitsidomain-text');

        if (dropdown.val() === '') {
            dropdown.prop('disabled', true);
        } else {
            textInputField.prop('disabled', true);
        }
    }

    displayJwtParams();
    toggleJitsiDomainTextInput();
    $(document.body).on('change', '#settingsform-enablejwt', displayJwtParams);
    $(document.body).on('change', '#settingsform-jitsidomain', toggleJitsiDomainTextInput);
    $('#configure-form').on('submit', disabledJitsiDomainField);
});
JS;
$this->registerJs($script);
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('JitsiMeetModule.base', '<strong>Jitsi</strong> module configuration') ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']) ?>

        <?= $form->field($model, 'jitsiDomain')->dropDownList(SettingsForm::defaultJitsiDomainOptions(), ['prompt' => Yii::t('JitsiMeetModule.base', 'Custom domain')])->hint('') ?>
        <?= $form->field($model, 'jitsiDomain')->textInput(['id' => 'settingsform-jitsidomain-text'])->label('') ?>
        <?= $form->field($model, 'roomPrefix') ?>
        <?= $form->field($model, 'menuTitle') ?>
        <?= $form->field($model, 'enableJwt')->checkbox() ?>
        <?= $form->field($model, 'jitsiAppID') ?>
        <?= $form->field($model, 'jitsiAppSecret') ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
