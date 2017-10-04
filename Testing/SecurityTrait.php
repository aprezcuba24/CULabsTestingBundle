<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\BrowserKit\Cookie;
use PHPUnit\Framework\Assert as Assert;

trait SecurityTrait
{
	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();
	abstract public function newClient(array $options = array(), array $server = array());

	public function securityCollector()
	{
		return $this->getClient()->getProfile()->getCollector('security');
	}

	/**
	 * @param $user
	 * @param array $roles
	 * @param string $firewall
	 * @return $this
	 */
	protected function logIn($user, array $roles = [], $firewall = 'default')
	{
		if ($user instanceof UserInterface) {
			$roles = array_merge($user->getRoles(), $roles);
		}
		$this->newClient();
		$client = $this->getClient();
		$session = $client->getContainer()->get('session');
		$token = new UsernamePasswordToken($user, null, $firewall, $roles);
		$session->set('_security_'.$firewall, serialize($token));
		$session->save();
		$cookie = new Cookie($session->getName(), $session->getId());
		$client->getCookieJar()->set($cookie);

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function logOut()
	{
		$client = $this->getClient();
		$client->getCookieJar()->clear();
		$session = $client->getContainer()->get('session');
		$session->clear();

		return $this;
	}

	/**
	 * @param null $roles
	 * @return $this
	 * @throws AssertionFailedError
	 */
	public function isAuthenticated($roles = null)
	{
		/**@var $securityCollector SecurityDataCollector*/
		$securityCollector = $this->securityCollector();
		Assert::assertTrue($securityCollector->isAuthenticated());
		if (!$roles) {
			return $this;
		}
		if (is_string($roles)) {
			$roles = [$roles];
		}
		foreach ($roles as $role) {
			if (!in_array($role, array_merge($securityCollector->getRoles(), $securityCollector->getInheritedRoles()))) {
				throw new AssertionFailedError;
			}
		}

		return $this;
	}

	/**
	 * @param $user
	 * @return $this
	 */
	public function userAuthenticatedIs($user)
	{
		Assert::assertEquals($user, $this->securityCollector()->getUser());

		return $this;
	}
}