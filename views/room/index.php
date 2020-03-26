<?php

use humhub\libs\Html;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model \humhubContrib\modules\jitsiMeet\\models\JoinRoomForm */

?>
<div class="container">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Yii::t('JitsiMeetModule.base', 'Open conference room'); ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'jrform']); ?>

                <?= $form->field($model, 'room'); ?>
                <?= $form->field($model, 'newWindow')->checkbox(); ?>
                
                <?= Button::save(Yii::t('JitsiMeetModule.base', 'Join'))->loader(false)->submit() ?>

                <?php ActiveForm::end(); ?>
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

