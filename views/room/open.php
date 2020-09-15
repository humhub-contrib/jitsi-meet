<?php

use humhub\libs\Html;
use humhub\widgets\SiteLogo;
use yii\helpers\Url;

/* @var $jitsiDomain string */
/* @var $jitsiRoomUrl array */
?>
<script <?= Html::nonce() ?> src='https://<?= $jitsiDomain ?>/external_api.js'></script>
<div class="container" style="text-align: center;">
    <?= SiteLogo::widget(['place' => 'login']) ?>
    <br>
    <br>
    <br>
    <script <?= Html::nonce() ?>>
        $(function () {
            var modalM = humhub.require("ui.modal");
            var x = modalM.get("#jitsiMeet-modal");
            x.load("<?= Url::to($jitsiRoomUrl); ?>");
            x.$.on('hidden.bs.modal', function (e) {
                window.location = "<?= Url::home() ?>";
            });
        });
    </script>
</div>
