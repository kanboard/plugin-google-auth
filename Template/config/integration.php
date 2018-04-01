<h3><i class="fa fa-google fa-fw"></i><?= t('Google Authentication') ?></h3>
<div class="panel">
    <?= $this->form->label(t('Google OAuth callback URL'), 'google_oauth_url') ?>
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('OAuthController', 'handler', array('plugin' => 'GoogleAuth'), false, '', true) ?>"/>

    <?= $this->form->label(t('Google Client Id'), 'google_client_id') ?>
    <?= $this->form->text('google_client_id', $values) ?>

    <?= $this->form->label(t('Google Client Secret'), 'google_client_secret') ?>
    <?= $this->form->password('google_client_secret', $values) ?>

    <?= $this->form->hidden('google_account_creation', array('google_account_creation' => 0)) ?>
    <?= $this->form->checkbox('google_account_creation', t('Allow Account Creation'), 1, isset($values['google_account_creation']) && $values['google_account_creation'] == 1) ?>

    <?= $this->form->label(t('Allow account creation only for those domains'), 'google_email_domains') ?>
    <?= $this->form->text('google_email_domains', $values) ?>
    <p class="form-help"><?= t('Use a comma to enter multiple domains: domain1.tld, domain2.tld') ?></p>

    <p class="form-help"><a href="https://github.com/kanboard/plugin-google-auth#documentation"><?= t('Help on Google authentication') ?></a></p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue">
    </div>
</div>
