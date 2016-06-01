<?php

namespace Kanboard\Plugin\GoogleAuth\User;

use Kanboard\User\OAuthUserProvider;

/**
 * Google OAuth User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class GoogleUserProvider extends OAuthUserProvider
{
    /**
     * @var bool
     */
    protected $allowUserCreation;

    /**
     * Constructor
     *
     * @access public
     * @param  array $user
     * @param  bool   $allowUserCreation
     */
    public function __construct(array $user, $allowUserCreation = false)
    {
        $this->user = $user;
        $this->allowUserCreation = $allowUserCreation;
    }

    /**
     * Return true to allow automatic user creation
     *
     * @access public
     * @return boolean
     */
    public function isUserCreationAllowed()
    {
        return $this->allowUserCreation;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        if ($this->allowUserCreation) {
            list($username,) = explode('@', $this->user['email']);
            return $username;
        }

        return '';
    }

    /**
     * Get external id column name
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn()
    {
        return 'google_id';
    }

    /**
     * Get Avatar image url from Google profile
     *
     * @access public
     * @return string
     */
    public function getAvatarUrl()
    {
        return !empty($this->user['picture']) ? $this->user['picture'] : '';
    }

    /**
     * Get extra user attributes
     *
     * @access public
     * @return array
     */
    public function getExtraAttributes()
    {
        if ($this->allowUserCreation) {
            return array(
                'is_ldap_user' => 1,
                'disable_login_form' => 1,
            );
        }

        return array();
    }
}
