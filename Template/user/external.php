<h3><i class="fa fa-google fa-fw"></i> <?= t('Google Account') ?></h3>

<div class="panel">
    <?php if ($this->user->isCurrentUser($user['id'])): ?>
        <?php if (empty($user['google_id'])): ?>
            <?= $this->url->link(t('Link my Google Account'), 'OAuthController', 'handler', array('plugin' => 'GoogleAuth'), true) ?>
        <?php else: ?>
            <?= $this->url->link(t('Unlink my Google Account'), 'OAuthController', 'unlink', array('backend' => 'Google'), true) ?>
        <?php endif ?>
    <?php else: ?>
        <?= empty($user['google_id']) ? t('No account linked.') : t('Account linked.') ?>
    <?php endif ?>
</div>
