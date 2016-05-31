<?php

namespace Kanboard\Plugin\GoogleAuth\User\Avatar;

use Kanboard\Core\Base;
use Kanboard\Core\User\Avatar\AvatarProviderInterface;

/**
 * Google Avatar Provider
 *
 * @package  avatar
 * @author   Frederic Guillot
 */
class GoogleAvatarProvider extends Base implements AvatarProviderInterface
{
    private $googleAvatarCache = array();

    /**
     * Render avatar html
     *
     * @access public
     * @param  array $user
     * @param  int   $size
     * @return string
     */
    public function render(array $user, $size)
    {
        $url = $this->googleAvatarCache[$user['id']].'?sz='.$size;
        $title = $this->helper->text->e($user['name'] ?: $user['username']);
        return '<img src="'.$url.'" alt="'.$title.'" title="'.$title.'">';
    }

    /**
     * Determine if the provider is active
     *
     * @access public
     * @param  array $user
     * @return boolean
     */
    public function isActive(array $user)
    {
        if (!isset($this->googleAvatarCache[$user['id']])) {
            $metadata = $this->userMetadataModel->getAll($user['id']);

            if (isset($metadata['google_show_avatar']) && $metadata['google_show_avatar'] == 1) {
                $this->googleAvatarCache[$user['id']] = $this->userMetadataModel->get($user['id'], 'google_avatar_url');
            } else {
                $this->googleAvatarCache[$user['id']] = '';
            }
        }

        return $this->googleAvatarCache[$user['id']] !== '';
    }
}
