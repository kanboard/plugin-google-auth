<?php

namespace Kanboard\Plugin\GoogleAuth;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Core\Security\Role;
use Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider;

class Plugin extends Base
{
    public function initialize()
    {
        $this->on('app.bootstrap', function ($container) {
            Translator::load($container['config']->getCurrentLanguage(), __DIR__.'/Locale');
        });

        $this->authenticationManager->register(new GoogleAuthProvider($this->container));
        $this->applicationAccessMap->add('OAuth', 'handler', Role::APP_PUBLIC);

        $this->route->addRoute('/oauth/google', 'OAuth', 'handler', 'GoogleAuth');

        $this->template->hook->attach('template:auth:login-form:after', 'GoogleAuth:auth/login');
        $this->template->hook->attach('template:config:integrations', 'GoogleAuth:config/integration');
        $this->template->hook->attach('template:user:external', 'GoogleAuth:user/external');
        $this->template->hook->attach('template:user:authentication:form', 'GoogleAuth:user/authentication');
    }

    public function getPluginName()
    {
        return 'Google Authentication';
    }

    public function getPluginDescription()
    {
        return t('Use Google as authentication provider');
    }

    public function getPluginAuthor()
    {
        return 'Frédéric Guillot';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-google-auth';
    }
}
