<?php

use humhub\helpers\Html;
use humhub\widgets\SiteLogo;
use yii\helpers\Url;

/* @var $jitsiDomain string */
/* @var $jitsiRoomUrl array */
?>
<?php
/*
 * WORKAROUND: Jitsi `external_api.js` vs. HumHub's Bluebird Promise
 *
 * Problem:
 *   The Jitsi API script (it bundles a Promise polyfill, `Promise @ external_api.js` in stack traces) modifies the
 *   global `Promise` when it is loaded:
 *   - it replaces `window.Promise`, so HumHub's Bluebird is gone and `promise.done()` in `humhub.client.js`
 *     fails with "promise.done is not a function" (on every ajax request, e.g. the live poll);
 *   - it also changes `prototype.constructor` of the Promise that was global at that moment, so even a restored
 *     Bluebird fails with "the promise constructor cannot be invoked directly" and the modal does not open.
 *
 * Solution:
 *   1. Save HumHub's Promise (`window.humhubPromise`).
 *   2. Make a pristine native Promise (taken from a hidden iframe, it has its own JS realm) the global one,
 *      so the Jitsi script patches this throwaway object instead of Bluebird.
 *   3. Load `external_api.js`.
 *   4. Restore HumHub's Promise.
 *
 * Possible reasons for this to break (then the workaround may be removed or has to be adapted):
 *   - Jitsi stops touching the global Promise: the workaround is then useless and can be deleted.
 *   - Jitsi starts to rely on `window.Promise` being its own/patched implementation after loading.
 *   - Another script is added to this page between the two inline scripts below: it would get the temporary Promise.
 *   - HumHub core stops using Bluebird (`promise.done()`) or loads it differently.
 *   - `external_api.js` is loaded somewhere else again (e.g. in `Assets.php`): that load is not protected.
 *
 * How to check: open the browser console on a conference page and run `typeof Promise.prototype.done`.
 * It must be `function` (the same as on the dashboard). Errors from `humhub.client.js` or Bluebird mean the
 * workaround does not work anymore.
 *
 * Cleaner alternative (not implemented): load Jitsi in an isolated page/iframe without the HumHub scripts.
 */
?>
<script <?= Html::nonce() ?>>
    (function () {
        // Hidden iframe: gives us an untouched native Promise (must stay in the DOM while the Jitsi API is used)
        var frame = document.createElement('iframe');
        frame.style.display = 'none';
        document.body.appendChild(frame);

        // Remember HumHub's (Bluebird) Promise and use the native one while the Jitsi script is loading
        window.humhubPromise = window.Promise;
        window.Promise = frame.contentWindow.Promise;
    })();
</script>
<script <?= Html::nonce() ?> src='https://<?= Html::encode($jitsiDomain) ?>/external_api.js'></script>
<script <?= Html::nonce() ?>>
    // Restore HumHub's Promise (see the comment above)
    window.Promise = window.humhubPromise;
</script>
<div class="container" style="text-align: center;">
    <?= SiteLogo::widget(['place' => 'login']) ?>
    <br>
    <br>
    <br>
    <script <?= Html::nonce() ?>>
        $(function () {
            var modalModule = humhub.require("ui.modal");
            var modal = modalModule.get("jitsiMeet-modal");
            modal.load("<?= Url::to($jitsiRoomUrl) ?>");
            modal.$.on('hidden.bs.modal', function (e) {
                window.location = "<?= Url::home() ?>";
            });
        });
    </script>
</div>
