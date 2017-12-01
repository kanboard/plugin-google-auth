<?php

namespace Kanboard\Plugin\GoogleAuth\Auth;

use Kanboard\Core\Base;
use Kanboard\Core\Security\OAuthAuthenticationProviderInterface;
use Kanboard\Plugin\GoogleAuth\User\GoogleUserProvider;

/**
 * Google Authentication Provider
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class GoogleAuthProvider extends Base implements OAuthAuthenticationProviderInterface
{
    /**
     * User properties
     *
     * @access protected
     * @var \Kanboard\Plugin\GoogleAuth\User\GoogleUserProvider
     */
    protected $userInfo = null;

    /**
     * OAuth2 instance
     *
     * @access protected
     * @var \Kanboard\Core\Http\OAuth2
     */
    protected $service;

    /**
     * OAuth2 code
     *
     * @access protected
     * @var string
     */
    protected $code = '';

    /**
     * Get authentication provider name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'Google';
    }

    /**
     * Authenticate the user
     *
     * @access public
     * @return boolean
     */
    public function authenticate()
    {
        $profile = $this->getProfile();

        if (! empty($profile)) {
            $this->userInfo = new GoogleUserProvider($profile, $this->isAccountCreationAllowed($profile));
            return true;
        }

        return false;
    }

    /**
     * Set Code
     *
     * @access public
     * @param  string  $code
     * @return GoogleAuthProvider
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get user object
     *
     * @access public
     * @return GoogleUserProvider
     */
    public function getUser()
    {
        return $this->userInfo;
    }

    /**
     * Get configured OAuth2 service
     *
     * @access public
     * @return \Kanboard\Core\Http\OAuth2
     */
    public function getService()
    {
        if (empty($this->service)) {
            $this->service = $this->oauth->createService(
                $this->getGoogleClientId(),
                $this->getGoogleClientSecret(),
                $this->helper->url->to('OAuthController', 'handler', array('plugin' => 'GoogleAuth'), '', true),
                'https://accounts.google.com/o/oauth2/v2/auth',
                'https://www.googleapis.com/oauth2/v4/token',
                array(
                    'email',
                    'profile',
                )
            );
        }

        return $this->service;
    }

    /**
     * Get Google profile
     *
     * @access public
     * @return array
     */
    public function getProfile()
    {
        $this->getService()->getAccessToken($this->code);

        return $this->httpClient->getJson(
            'https://www.googleapis.com/oauth2/v1/userinfo',
            array($this->getService()->getAuthorizationHeader())
        );
    }

    /**
     * Unlink user
     *
     * @access public
     * @param  integer $userId
     * @return bool
     */
    public function unlink($userId)
    {
        return $this->userModel->update(array('id' => $userId, 'google_id' => ''));
    }

    /**
     * Get Google client id
     *
     * @access public
     * @return string
     */
    public function getGoogleClientId()
    {
        if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID) {
            return GOOGLE_CLIENT_ID;
        }

        return $this->configModel->get('google_client_id');
    }

    /**
     * Get Google client secret
     *
     * @access public
     * @return string
     */
    public function getGoogleClientSecret()
    {
        if (defined('GOOGLE_CLIENT_SECRET') && GOOGLE_CLIENT_SECRET) {
            return GOOGLE_CLIENT_SECRET;
        }

        return $this->configModel->get('google_client_secret');
    }

    /**
     * Get Google allowed email domains
     *
     * @access public
     * @return string
    */
    public function getGoogleEmailDomains()
    {
        if (defined('GOOGLE_EMAIL_DOMAINS') && GOOGLE_EMAIL_DOMAINS) {
            return GOOGLE_EMAIL_DOMAINS;
        }

        return $this->configModel->get('google_email_domains');
    }

    /**
     * Return true if the account creation is allowed according to the settings
     *
     * @access public
     * @param array $profile
     * @return bool
     */
    public function isAccountCreationAllowed(array $profile)
    {
        if ($this->configModel->get('google_account_creation', 0) == 1) {
            $domains = $this->getGoogleEmailDomains();

            if (! empty($domains)) {
                return $this->validateDomainRestriction($profile, $domains);
            }

            return true;
        }

        return false;
    }

    /**
     * Validate domain restriction
     *
     * @access private
     * @param  array  $profile
     * @param  string $domains
     * @return bool
     */
    public function validateDomainRestriction(array $profile, $domains)
    {
        if (strpos($profile['email'], '@') === false) {
            return false;
        }

        list(, $hostname) = explode('@', $profile['email']);
        $hostname = trim($hostname);

        foreach (explode(',', $domains) as $domain) {
            if ($hostname === trim($domain)) {
                return true;
            }
        }

        return false;
    }
}
