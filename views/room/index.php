<?php

use humhub\helpers\DeviceDetectorHelper;
use humhub\helpers\Html;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;

/* @var $model \humhubContrib\modules\jitsiMeet\\models\JoinRoomForm */

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t('JitsiMeetModule.base', 'Open conference room'); ?>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'jrform']); ?>

                    <?= $form->field($model, 'room') ?>

                    <?php if (!DeviceDetectorHelper::isAppRequest()) : ?>
                        <?= $form->field($model, 'newWindow')->checkbox() ?>
                    <?php endif; ?>

                    <?= Button::save(Yii::t('JitsiMeetModule.base', 'Join'))->loader(false)->submit() ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script <?= Html::nonce() ?>>
    $('#jrform').on('beforeSubmit', function(e) {
        if ($('#joinroomform-newwindow'). prop("checked") == true) {
            $('#jrform').attr('target','_blank');
        }
        return true;
    });
</script>
