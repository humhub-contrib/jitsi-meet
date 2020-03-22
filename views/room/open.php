<?php

use humhub\widgets\SiteLogo;
use yii\helpers\Url;

/* @var $jitsiDomain string */
/* @var $jwt string */
?>
<script src='https://<?= $jitsiDomain ?>/external_api.js'></script>
<div class="container" style="text-align: center;">
    <?= SiteLogo::widget(['place' => 'login']); ?>
    <br>
    <br>
    <br>
    <script>
        var modalM = humhub.require("ui.modal");
        x = modalM.get("#jitsiMeet-modal")
        x.load("<?= Url::to(['/jitsi-meet/room/modal', 'name' => $name]); ?>");
        x.$.on('hidden.bs.modal', function (e) {
            window.location = "<?= Url::home(); ?>";
        });
    </script>
</div>
