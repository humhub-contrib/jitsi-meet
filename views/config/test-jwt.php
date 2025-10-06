<?php

use humhub\widgets\Button;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $testResults array */
/* @var $model \humhubContrib\modules\jitsiMeet\models\SettingsForm */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><?= Yii::t('JitsiMeetModule.base', 'JaaS JWT Debug Test') ?></h3>
    </div>
    <div class="panel-body">
        
        <div class="row">
            <div class="col-md-6">
                <h4>Configuration Status</h4>
                <table class="table table-striped">
                    <tr>
                        <td><strong>App ID:</strong></td>
                        <td><?= Html::encode($testResults['config']['appId'] ?: 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td><strong>API Key (kid):</strong></td>
                        <td><?= Html::encode($testResults['config']['kid'] ?: 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Private Key Path:</strong></td>
                        <td><?= Html::encode($testResults['config']['privateKeyPath'] ?: 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Domain:</strong></td>
                        <td><?= Html::encode($testResults['config']['domain'] ?: 'Not set') ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Private Key File Status</h4>
                <table class="table table-striped">
                    <tr>
                        <td><strong>File Path:</strong></td>
                        <td><?= Html::encode($testResults['keyFile']['path'] ?: 'Not set') ?></td>
                    </tr>
                    <tr>
                        <td><strong>File Exists:</strong></td>
                        <td>
                            <?php if ($testResults['keyFile']['exists']): ?>
                                <span class="label label-success">Yes</span>
                            <?php else: ?>
                                <span class="label label-danger">No</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>File Readable:</strong></td>
                        <td>
                            <?php if ($testResults['keyFile']['readable']): ?>
                                <span class="label label-success">Yes</span>
                            <?php else: ?>
                                <span class="label label-danger">No</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>File Size:</strong></td>
                        <td><?= $testResults['keyFile']['size'] ?> bytes</td>
                    </tr>
                    <?php if (isset($testResults['keyFile']['isPem'])): ?>
                    <tr>
                        <td><strong>Valid PEM:</strong></td>
                        <td>
                            <?php if ($testResults['keyFile']['isPem']): ?>
                                <span class="label label-success">Yes</span>
                            <?php else: ?>
                                <span class="label label-danger">No</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>JWT Generation Test</h4>
                <?php if (isset($testResults['jwt']['error'])): ?>
                    <div class="alert alert-danger">
                        <strong>Error:</strong> <?= Html::encode($testResults['jwt']['error']) ?>
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <tr>
                            <td><strong>JWT Generated:</strong></td>
                            <td>
                                <?php if ($testResults['jwt']['generated']): ?>
                                    <span class="label label-success">Yes</span>
                                <?php else: ?>
                                    <span class="label label-danger">No</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($testResults['jwt']['generated']): ?>
                        <tr>
                            <td><strong>JWT Length:</strong></td>
                            <td><?= $testResults['jwt']['length'] ?> characters</td>
                        </tr>
                        <tr>
                            <td><strong>JWT Token:</strong></td>
                            <td>
                                <textarea class="form-control" rows="3" readonly><?= Html::encode($testResults['jwt']['token']) ?></textarea>
                                <small class="text-muted">Copy this token to <a href="https://jwt.io" target="_blank">jwt.io</a> for debugging</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($testResults['jwt']['decoded'])): ?>
        <div class="row">
            <div class="col-md-6">
                <h4>JWT Header</h4>
                <pre class="bg-light p-3"><?= Html::encode(json_encode($testResults['jwt']['decoded']['header'], JSON_PRETTY_PRINT)) ?></pre>
            </div>
            <div class="col-md-6">
                <h4>JWT Payload</h4>
                <pre class="bg-light p-3"><?= Html::encode(json_encode($testResults['jwt']['decoded']['payload'], JSON_PRETTY_PRINT)) ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($testResults['jwt']['decodeError'])): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <strong>JWT Decode Error:</strong> <?= Html::encode($testResults['jwt']['decodeError']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <h4>Next Steps</h4>
                <ol>
                    <li>Ensure all configuration fields are filled correctly</li>
                    <li>Place your 8x8 private key file at: <code><?= Html::encode($testResults['config']['privateKeyPath']) ?></code></li>
                    <li>Set proper file permissions: <code>chmod 600 <?= Html::encode($testResults['config']['privateKeyPath']) ?></code></li>
                    <li>If JWT is generated, copy it to <a href="https://jwt.io" target="_blank">jwt.io</a> to verify the structure</li>
                    <li>Test a real meeting room to verify authentication works</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= Button::primary('Back to Configuration')->link(Url::to(['index'])) ?>
                <?= Button::primary('Run Test Again')->link(Url::to(['test-jwt'])) ?>
            </div>
        </div>

    </div>
</div>

