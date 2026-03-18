<?php

use humhub\helpers\Html;
use humhub\widgets\bootstrap\Button;
use humhubContrib\modules\jitsiMeet\assets\Assets;

Assets::register($this);

/* @var array $options */
/* @var string $moduleLabel */
/* @var string $roomName */
?>
<?= Html::beginTag('div', $options) ?>
<div style="height:50px; border-radius: 8px 8px 0px 0px; background-color:#1C2025; padding-top:6px; padding-right:12px">
    <div class="float-end" style="margin-top:2px;margin-right:12px">
        <?= Button::light(Yii::t('JitsiMeetModule.base', 'Close'))
            ->action('close')
            ->onAction('block', 'manual') ?>
    </div>
    <div style="color:white;padding-left:12px;font-size:24px;padding-top:3px;padding-left:18px">
        <?= Html::encode($moduleLabel) ?>
        <span style="font-size:16px;"><?= Html::encode($roomName) ?></span>
    </div>
</div>
<div id="jitsiMeetD"></div>

<?= Html::endTag('div'); ?>
