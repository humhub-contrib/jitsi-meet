<?php

use humhub\libs\Html;
use humhubContrib\modules\jitsiMeetCloud8x8\widgets\RoomWidget;

/* @var $jwt string */
/* @var $name string */
/* @var $startSilent bool */
?>
<div class="modal-dialog animated fadeIn" style="width:96%">
    <div class="modal-content jitsiModal" id="jitsiModal" style="background-color:transparent;">
        <?= RoomWidget::widget(['roomName' => $name, 'jwt' => $jwt, 'startSilent' => $startSilent ?? false]) ?>
    </div>
</div>

<script <?= Html::nonce() ?>>
    window.onload = function (evt) {
        setSize();
    };
    window.onresize = function (evt) {
        setSize();
    };
    setSize();

    function setSize() {
        $('.jitsiModal').css('height', window.innerHeight - 110 + 'px');
    }
</script>
