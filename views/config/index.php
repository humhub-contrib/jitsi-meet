<?php

use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model \humhubContrib\modules\jitsiMeetCloud8x8\models\SettingsForm */

$script = <<< JS
$(document).ready(function () {
    function displayJwtParams(){
        if ( $('#settingsform-enablejwt').is(':checked') ) {
          $('.field-settingsform-jitsiappid').show();
          $('.field-settingsform-jitsiappsecret').show();
        } else {
          $('.field-settingsform-jitsiappid').hide();
          $('.field-settingsform-jitsiappsecret').hide();
        }
    }

    function displayJaas(){
        if ($('#settingsform-mode').val() === 'jaas') {
            $('.field-settingsform-jaasappid').show();
            $('.field-settingsform-jaaskid').show();
            $('.field-settingsform-jaasprivatekeypath').show();
            $('.field-settingsform-jaasdomain').show();
            $('.field-settingsform-jaasenablerecording').show();
            $('.field-settingsform-jaasenablelivestreaming').show();
            $('.field-settingsform-jaasenablemoderation').show();
        } else {
            $('.field-settingsform-jaasappid').hide();
            $('.field-settingsform-jaaskid').hide();
            $('.field-settingsform-jaasprivatekeypath').hide();
            $('.field-settingsform-jaasdomain').hide();
            $('.field-settingsform-jaasenablerecording').hide();
            $('.field-settingsform-jaasenablelivestreaming').hide();
            $('.field-settingsform-jaasenablemoderation').hide();
        }
    }

    displayJwtParams();
    displayJaas();
    $(document.body).on('change', '#settingsform-enablejwt', function(){ displayJwtParams(); });
    $(document.body).on('change', '#settingsform-mode', function(){ displayJaas(); });
});
JS;
$this->registerJs($script);

?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('JitsiMeetCloud8x8Module.base', '<strong>Jitsi</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

        <?= $form->field($model, 'mode')->dropDownList(['self_hosted' => 'Self-Hosted Jitsi', 'jaas' => '8x8 JaaS (Cloud)']); ?>

        <?= $form->field($model, 'jitsiDomain'); ?>
        <?= $form->field($model, 'roomPrefix'); ?>
        <?= $form->field($model, 'menuTitle'); ?>

        <?= $form->field($model, 'enableJwt')->checkbox(); ?>
        <?= $form->field($model, 'jitsiAppID'); ?>
        <?= $form->field($model, 'jitsiAppSecret'); ?>

        <?= $form->field($model, 'jaasAppId'); ?>
        <?= $form->field($model, 'jaasKid'); ?>
        <?= $form->field($model, 'jaasPrivateKeyPath'); ?>
        <?= $form->field($model, 'jaasDomain'); ?>
        <?= $form->field($model, 'jaasEnableRecording')->checkbox(); ?>
        <?= $form->field($model, 'jaasEnableLivestreaming')->checkbox(); ?>
        <?= $form->field($model, 'jaasEnableModeration')->checkbox(); ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php if ($model->mode === 'jaas'): ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h4><?= Yii::t('JitsiMeetCloud8x8Module.base', 'JaaS Debug Information') ?></h4>
    </div>
    <div class="panel-body">
        
        <div class="row">
            <div class="col-md-6">
                <h5>Configuration Status</h5>
                <ul class="list-unstyled">
                    <li>
                        <strong>App ID:</strong> 
                        <?php if (!empty($model->jaasAppId)): ?>
                            <span class="label label-success">Set</span>
                        <?php else: ?>
                            <span class="label label-danger">Missing</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>API Key:</strong> 
                        <?php if (!empty($model->jaasKid)): ?>
                            <span class="label label-success">Set</span>
                        <?php else: ?>
                            <span class="label label-danger">Missing</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>Private Key Path:</strong> 
                        <?php if (!empty($model->jaasPrivateKeyPath)): ?>
                            <span class="label label-success">Set</span>
                        <?php else: ?>
                            <span class="label label-danger">Missing</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
            
            <div class="col-md-6">
                <h5>Private Key File Status</h5>
                <?php 
                $keyPath = getenv('HUMHUB_JAAS_PRIVATE_KEY_PATH') ?: $model->jaasPrivateKeyPath;
                $keyExists = !empty($keyPath) && file_exists($keyPath);
                $keyReadable = $keyExists && is_readable($keyPath);
                ?>
                <ul class="list-unstyled">
                    <li>
                        <strong>File Exists:</strong> 
                        <?php if ($keyExists): ?>
                            <span class="label label-success">Yes</span>
                        <?php else: ?>
                            <span class="label label-danger">No</span>
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>File Readable:</strong> 
                        <?php if ($keyReadable): ?>
                            <span class="label label-success">Yes</span>
                        <?php else: ?>
                            <span class="label label-danger">No</span>
                        <?php endif; ?>
                    </li>
                    <?php if ($keyExists): ?>
                    <li>
                        <strong>File Size:</strong> <?= filesize($keyPath) ?> bytes
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h5>Quick Actions</h5>
                <p>
                    <?= Button::primary('Test JWT Generation')->link(Url::to(['test-jwt'])) ?>
                    <small class="text-muted">Generate a test JWT to verify your configuration</small>
                </p>
                
                <h5>Setup Instructions</h5>
                <ol>
                    <li>Place your 8x8 private key file at: <code><?= Html::encode($keyPath ?: '/var/www/keys/jaas_private.pem') ?></code></li>
                    <li>Set proper permissions: <code>chmod 600 <?= Html::encode($keyPath ?: '/var/www/keys/jaas_private.pem') ?></code></li>
                    <li>Ensure the file owner matches the PHP process user</li>
                    <li>Test JWT generation using the button above</li>
                </ol>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>
