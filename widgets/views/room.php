<?php

use yii\helpers\Url;
use humhub\libs\Html;

\humhubContrib\modules\jitsiMeet\assets\Assets::register($this);

?>
<?= Html::beginTag('div', $options) ?>
<div style="height:50px; border-radius: 8px 8px 0px 0px; background-color:#1C2025; padding-top:6px; padding-right:12px">
    <div class="pull-right" style="margin-top:2px;margin-right:12px">
        <!--
        <?= Html::a(Yii::t('JitsiMeetModule.base', 'Invite'), '#', ['class' => 'btn btn btn-default', 'data-action-click' => 'share', 'data-action-block' => 'sync', 'data-action-url' => Url::to(['/jitsi-meet/room/share'])]); ?>
        -->
        <?= Html::a(Yii::t('JitsiMeetModule.base', 'Close'), '#', ['class' => 'btn btn btn-default', 'data-ui-loader' => '', 'data-action-click' => 'close', 'data-action-block' => 'manual']); ?>
    </div>
    <div style="color:white;padding-left:12px;font-size:24px;padding-top:3px;padding-left:18px">
        <?= Html::encode($moduleLabel) ?>
        <span style="font-size:16px;"><?= Html::encode($roomName) ?></span>

    </div>
</div>
<div id="jitsiMeetD" style="borderx:1px solid red;">
</div>

<?= Html::endTag('div'); ?>


