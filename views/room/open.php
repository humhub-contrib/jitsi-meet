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
            var modalModule = humhub.require("ui.modal");
            var modal = modalModule.get("jitsiMeet-modal");
            modal.load("<?= Url::to($jitsiRoomUrl); ?>");
            modal.$.on('hidden.bs.modal', function (e) {
                window.location = "<?= Url::home() ?>";
            });
        });
    </script>
</div>
