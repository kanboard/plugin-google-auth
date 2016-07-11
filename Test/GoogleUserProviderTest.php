<?php

use Kanboard\Plugin\GoogleAuth\User\GoogleUserProvider;

require_once 'tests/units/Base.php';

class GoogleUserProviderTest extends Base
{
    public function testIsUserCreationAllowed()
    {
        $userProvider = new GoogleUserProvider(array());
        $this->assertFalse($userProvider->isUserCreationAllowed());

        $userProvider = new GoogleUserProvider(array(), true);
        $this->assertTrue($userProvider->isUserCreationAllowed());
    }

    public function testGetEmail()
    {
        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'));
        $this->assertEquals('test@gmail.com', $userProvider->getEmail());
    }

    public function testGetRole()
    {
        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'), true);
        $this->assertSame('', $userProvider->getRole());
    }

    public function testGetName()
    {
        $userProvider = new GoogleUserProvider(array());
        $this->assertEquals('', $userProvider->getName());

        $userProvider = new GoogleUserProvider(array('name' => 'test'));
        $this->assertEquals('test', $userProvider->getName());
    }

    public function testGetUserName()
    {
        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'));
        $this->assertEquals('', $userProvider->getUsername());

        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'), true);
        $this->assertEquals('test', $userProvider->getUsername());
    }

    public function testGetExternalIdColumn()
    {
        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'));
        $this->assertEquals('google_id', $userProvider->getExternalIdColumn());
    }

    public function testGetAvatarUrl()
    {
        $userProvider = new GoogleUserProvider(array());
        $this->assertEquals('', $userProvider->getAvatarUrl());

        $userProvider = new GoogleUserProvider(array('picture' => 'my picture'));
        $this->assertEquals('my picture', $userProvider->getAvatarUrl());
    }

    public function testGetExtraAttribute()
    {
        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'));
        $this->assertSame(array(), $userProvider->getExtraAttributes());

        $userProvider = new GoogleUserProvider(array('email' => 'test@gmail.com'), true);
        $this->assertSame(array('is_ldap_user' => 1, 'disable_login_form' => 1), $userProvider->getExtraAttributes());
    }
}
