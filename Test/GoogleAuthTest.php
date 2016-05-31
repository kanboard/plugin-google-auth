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
