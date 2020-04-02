<?php

use humhub\libs\Html;
use humhubContrib\modules\jitsiMeet\widgets\RoomWidget;

/* @var $jitsiDomain string */
/* @var $jwt string */
/* @var $name string */
?>
<div class="modal-dialog animated fadeIn" style="width:96%">
    <div class="modal-content jitsiModal" id="jitsiModal" style="background-color:transparent;">
        <?=
        RoomWidget::widget(['roomName' => $name, 'jwt' => $jwt]);
        ?>
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
