<h3><i class="fa fa-google fa-fw"></i><?= t('Google Authentication') ?></h3>
<div class="listing">

    <?= $this->form->label(t('Google OAuth callback URL'), 'google_oauth_url') ?>
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('OAuth', 'handler', array('plugin' => 'GoogleAuth'), false, '', true) ?>"/>

    <?= $this->form->label(t('Google Client Id'), 'google_client_id') ?>
    <?= $this->form->text('google_client_id', $values) ?>

    <?= $this->form->label(t('Google Client Secret'), 'google_client_secret') ?>
    <?= $this->form->password('google_client_secret', $values) ?>
    <p class="form-help"><a href="https://github.com/kanboard/plugin-google-auth/blob/master/README.md"><?= t('Help on Google authentication') ?></a></p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</div>