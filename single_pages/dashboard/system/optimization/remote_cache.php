<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Validation\CSRF\Token $token */
/** @var \Concrete\Core\View\View $view */
/** @var \Concrete\Core\Localization\Service\Date $dh */

$remote = $remote ?? null;
$key = $key ?? null;
$path = $path ?? null;
$cloudfront_key = $cloudfront_key ?? null;
$cloudfront_secret = $cloudfront_secret ?? null;
$response = $response ?? null;
?>
<div class="container">
    <div class="row">
        <div class="col">
            <form action="<?= $view->action('save_server') ?>" method="post">
                <?php $token->output('save_server') ?>
                <fieldset>
                    <legend><?= t('Server') ?></legend>
                    <div class="form-group">
                        <?= $form->label('remote', t('Remote Host')) ?>
                        <?= $form->url('remote', $remote, ['placeholder' => 'https://www.example.com']) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->label('key', t('Key')) ?>
                        <div class="input-group">
                            <?= $form->password('key', $key) ?>
                            <button id="showkey" class="btn btn-outline-secondary"
                                    title="<?= t('Show secret key') ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="help-block">
                            <?= t('Please input passphrase to connect to remote API.') ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><?= t('CloudFront') ?></legend>
                    <div class="form-group">
                        <?= $form->label('cloudfront_key', t('Key')) ?>
                        <?= $form->test('cloudfront_key', $cloudfront_key) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->label('cloudfront_secret', t('Secret')) ?>
                        <div class="input-group">
                            <?= $form->password('cloudfront_secret', $cloudfront_secret) ?>
                            <button id="showsecret" class="btn btn-outline-secondary"
                                    title="<?= t('Show secret key') ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </fieldset>
                <?= $form->submit('submit', t('Save'), ['class' => 'btn-primary']) ?>
            </form>
        </div>
        <div class="col">
            <form action="<?= $view->action('check_remote') ?>" method="post">
                <?php $token->output('check_remote') ?>
                <fieldset>
                    <legend><?= t('Remote Cache Tool') ?></legend>
                    <div class="form-group">
                        <?= $form->label('path', t('Check Path')) ?>
                        <?= $form->text('path', $path, ['placeholder' => '/about/blog']) ?>
                    </div>
                    <?php
                    if ($response) {
                        $cached = $response->cached ?? false;
                        $expiration = $response->expiration ?? 0;
                        ?>
                        <table class="table">
                            <tr>
                                <th><?= t('Cache Status') ?></th>
                                <td><?= $cached ? t('Cached') : t('Not Cached') ?></td>
                            </tr>
                            <tr>
                                <th><?= t('Expiration') ?></th>
                                <td><?= $expiration ? $dh->formatPrettyDateTime($expiration) : t('N/A') ?></td>
                            </tr>
                        </table>
                        <?php
                    }
                    ?>
                    <div class="btn-group">
                        <?= $form->submit('check', t('Check'), ['class' => 'btn-secondary']) ?>
                        <?= $form->submit('clear', t('Clear Cache'), ['class' => 'btn-warning']) ?>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<script>
    $('#showkey').on('click', function (e) {
        e.preventDefault();
        let keyField = $('#key');
        if (keyField.attr('type') === 'password') {
            keyField.attr('type', 'text');
            $('#showkey')
                .attr('title', <?= json_encode(t('Hide secret key')) ?>)
                .html('<i class="fas fa-eye-slash"></i>')
            ;
        } else {
            keyField.attr('type', 'password');
            $('#showkey')
                .attr('title', <?= json_encode(t('Show secret key')) ?>)
                .html('<i class="fas fa-eye"></i>')
            ;
        }
    });
    $('#showsecret').on('click', function (e) {
        e.preventDefault();
        let keyField = $('#cloudfront_secret');
        if (keyField.attr('type') === 'password') {
            keyField.attr('type', 'text');
            $('#showsecret')
                .attr('title', <?= json_encode(t('Hide secret key')) ?>)
                .html('<i class="fas fa-eye-slash"></i>')
            ;
        } else {
            keyField.attr('type', 'password');
            $('#showsecret')
                .attr('title', <?= json_encode(t('Show secret key')) ?>)
                .html('<i class="fas fa-eye"></i>')
            ;
        }
    });
</script>