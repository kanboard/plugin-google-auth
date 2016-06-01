<ul class="no-bullet">
    <li>
        <i class="fa fa-google fa-fw"></i>
        <?= $this->url->link(t('Login with my Google Account'), 'OAuthController', 'handler', array('plugin' => 'GoogleAuth')) ?>
    </li>
</ul>
