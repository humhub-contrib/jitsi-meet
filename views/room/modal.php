<?php

use humhub\helpers\Html;
use humhubContrib\modules\jitsiMeet\widgets\RoomWidget;

/* @var $jwt string */
/* @var $name string */
?>

<div class="modal-dialog animated fadeIn" style="max-width:96%">
    <div id="jitsiModal" class="modal-content jitsiModal p-0" style="background-color:transparent;">
        <?= RoomWidget::widget(['roomName' => $name, 'jwt' => $jwt]) ?>
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
        $('#jitsiModal').css('height', window.innerHeight - 110 + 'px');
    }
</script>
