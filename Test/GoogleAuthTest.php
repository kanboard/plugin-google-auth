<?php

require_once 'tests/units/Base.php';

use Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider;
use Kanboard\Model\UserModel;

class GoogleAuthTest extends Base
{
    public function testGetName()
    {
        $provider = new GoogleAuthProvider($this->container);
        $this->assertEquals('Google', $provider->getName());
    }

    public function testIsAccountCreationAllowed()
    {
        $provider = new GoogleAuthProvider($this->container);
        $this->assertFalse($provider->isAccountCreationAllowed(array()));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '0')));
        $this->container['memoryCache']->flush();
        $this->assertFalse($provider->isAccountCreationAllowed(array()));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '1')));
        $this->container['memoryCache']->flush();
        $this->assertTrue($provider->isAccountCreationAllowed(array()));
    }

    public function testEmailRestrictions()
    {
        $provider = new GoogleAuthProvider($this->container);

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '0', 'google_email_domains' => 'mydomain.tld')));
        $this->container['memoryCache']->flush();
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'me@mydomain.tld')));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '1', 'google_email_domains' => 'mydomain.tld')));
        $this->container['memoryCache']->flush();
        $this->assertTrue($provider->isAccountCreationAllowed(array('email' => 'me@mydomain.tld')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'me@my-other-domain.tld')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'test+mydomain.tld+@example.org')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'test@mydomain.tld.example.org')));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '1', 'google_email_domains' => 'example.org, example.com')));
        $this->container['memoryCache']->flush();
        $this->assertTrue($provider->isAccountCreationAllowed(array('email' => 'me@example.org')));
        $this->assertTrue($provider->isAccountCreationAllowed(array('email' => 'me@example.com')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'me@example.net')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'invalid email')));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '1', 'google_email_domains' => 'example')));
        $this->container['memoryCache']->flush();
        $this->assertTrue($provider->isAccountCreationAllowed(array('email' => 'me@example')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'example@localhost')));

        $this->assertTrue($this->container['configModel']->save(array('google_account_creation' => '1', 'google_email_domains' => 'example.org')));
        $this->container['memoryCache']->flush();
        $this->assertTrue($provider->isAccountCreationAllowed(array('email' => 'me@example.org')));
        $this->assertFalse($provider->isAccountCreationAllowed(array('email' => 'me@subdomain.example.org')));
    }

    public function testGetClientId()
    {
        $provider = new GoogleAuthProvider($this->container);
        $this->assertEmpty($provider->getGoogleClientId());

        $this->assertTrue($this->container['configModel']->save(array('google_client_id' => 'my_id')));
        $this->container['memoryCache']->flush();

        $this->assertEquals('my_id', $provider->getGoogleClientId());
    }

    public function testGetClientSecret()
    {
        $provider = new GoogleAuthProvider($this->container);
        $this->assertEmpty($provider->getGoogleClientSecret());

        $this->assertTrue($this->container['configModel']->save(array('google_client_secret' => 'secret')));
        $this->container['memoryCache']->flush();

        $this->assertEquals('secret', $provider->getGoogleClientSecret());
    }

    public function testAuthenticationSuccessful()
    {
        $profile = array(
            'id' => 1234,
            'email' => 'test@localhost',
            'name' => 'Test',
        );

        $provider = $this
            ->getMockBuilder('\Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getProfile',
            ))
            ->getMock();

        $provider->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue($profile));

        $this->assertInstanceOf('Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider', $provider->setCode('1234'));

        $this->assertTrue($provider->authenticate());

        $user = $provider->getUser();
        $this->assertInstanceOf('Kanboard\Plugin\GoogleAuth\User\GoogleUserProvider', $user);
        $this->assertEquals('Test', $user->getName());
        $this->assertEquals('', $user->getInternalId());
        $this->assertEquals(1234, $user->getExternalId());
        $this->assertEquals('', $user->getRole());
        $this->assertEquals('', $user->getUsername());
        $this->assertEquals('test@localhost', $user->getEmail());
        $this->assertEquals('google_id', $user->getExternalIdColumn());
        $this->assertEquals(array(), $user->getExternalGroupIds());
        $this->assertEquals(array(), $user->getExtraAttributes());
        $this->assertFalse($user->isUserCreationAllowed());
    }

    public function testAuthenticationFailed()
    {
        $provider = $this
            ->getMockBuilder('\Kanboard\Plugin\GoogleAuth\Auth\GoogleAuthProvider')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getProfile',
            ))
            ->getMock();

        $provider->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue(array()));

        $this->assertFalse($provider->authenticate());
        $this->assertEquals(null, $provider->getUser());
    }

    public function testGetService()
    {
        $provider = new GoogleAuthProvider($this->container);
        $this->assertInstanceOf('Kanboard\Core\Http\OAuth2', $provider->getService());
    }

    public function testUnlink()
    {
        $userModel = new UserModel($this->container);
        $provider = new GoogleAuthProvider($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'test', 'google_id' => '1234')));
        $this->assertNotEmpty($userModel->getByExternalId('google_id', 1234));

        $this->assertTrue($provider->unlink(2));
        $this->assertEmpty($userModel->getByExternalId('google_id', 1234));
    }
}
