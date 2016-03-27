<?php

namespace Kanboard\Plugin\GoogleAuth;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\AuthenticationManager;
use Kanboard\Core\Translator;
use Kanboard\Core\Security\Role;
use Kanboard\Event\AuthSuccessEvent;
use Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider;
use Kanboard\Plugin\GoogleAuth\User\Avatar\GoogleAvatarProvider;

class Plugin extends Base
{
    public function initialize()
    {
        $this->dispatcher->addListener('app.bootstrap', array($this, 'onBootstrap'));
        $this->dispatcher->addListener(AuthenticationManager::EVENT_SUCCESS, array($this, 'onLoginSuccess'));

        $this->authenticationManager->register(new GoogleAuthProvider($this->container));
        $this->applicationAccessMap->add('OAuth', 'handler', Role::APP_PUBLIC);
        $this->avatarManager->register(new GoogleAvatarProvider($this->container));

        $this->route->addRoute('/oauth/google', 'OAuth', 'handler', 'GoogleAuth');

        $this->template->hook->attach('template:auth:login-form:after', 'GoogleAuth:auth/login');
        $this->template->hook->attach('template:config:integrations', 'GoogleAuth:config/integration');
        $this->template->hook->attach('template:user:external', 'GoogleAuth:user/external');
        $this->template->hook->attach('template:user:integrations', 'GoogleAuth:user/integrations');
        $this->template->hook->attach('template:user:authentication:form', 'GoogleAuth:user/authentication');
        $this->template->hook->attach('template:user:create-remote:form', 'GoogleAuth:user/create_remote');
    }

    public function onBootstrap()
    {
        Translator::load($this->config->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function onLoginSuccess(AuthSuccessEvent $event)
    {
        if ($event->getAuthType() === 'Google') {
            $provider = $this->authenticationManager->getProvider($event->getAuthType());
            $avatar_url = $provider->getUser()->getAvatarUrl();
            $user_id = $this->userSession->getId();

            if (! empty($avatar_url)) {
                $options = array('google_avatar_url' => $avatar_url);

                if (! $this->userMetadata->exists($user_id, 'google_show_avatar')) {
                    $options['google_show_avatar'] = 1;
                }

                $this->userMetadata->save($user_id, $options);
            }
        }
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
        return '1.0.1';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-google-auth';
    }
}
