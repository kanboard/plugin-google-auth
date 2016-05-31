<?php

namespace Kanboard\Plugin\GoogleAuth\Controller;

use Kanboard\Controller\OAuthController as BaseOAuthController;

/**
 * OAuth Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class OAuthController extends BaseOAuthController
{
    /**
     * Handle authentication
     *
     * @access public
     */
    public function handler()
    {
        $this->step1('Google');
    }
}
