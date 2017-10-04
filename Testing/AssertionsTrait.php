<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use PHPUnit\Framework\Assert as Assert;
use PHPUnit\Framework\AssertionFailedError;

trait AssertionsTrait
{
	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	/**
	 * @return $this
	 */
	public function isOk()
	{
		$actual = $this->getClient()->getResponse()->getStatusCode();
		Assert::assertTrue($this->getClient()->getResponse()->isOk(), "Expected status code 200, got {$actual}.");

		return $this;
	}

	/**
	 * @param $status
	 * @return $this
	 */
	public function statusIs($status)
	{
		Assert::assertEquals($status, $this->getClient()->getResponse()->getStatusCode());

		return $this;
	}

	/**
	 * @param bool|TRUE $followRedirect
	 * @return $this
	 */
	public function isRedirect($followRedirect = true)
	{
		$actual = $this->getClient()->getResponse()->getStatusCode();
		Assert::assertTrue($this->getClient()->getResponse()->isRedirect(), "Expected redirect, got {$actual}.");
		if ($followRedirect) {
			$this->getClient()->followRedirect();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function pageDenied()
	{
		Assert::assertEquals(403, $this->getClient()->getResponse()->getStatusCode(), 'Expected denied');

		return $this;
	}

	/**
	 * @param $text
	 * @return $this
	 */
	public function see($text)
	{
		$crawler = $this->getClient()->getCrawler();
		Assert::assertContains($text, $crawler->filter('body')->text());

		return $this;
	}

	/**
	 * @param $text
	 * @return $this
	 */
	public function notSee($text)
	{
		$crawler = $this->getClient()->getCrawler();
		Assert::assertNotContains($text, $crawler->filter('body')->text());

		return $this;
	}

	/**
	 * @param $uri
	 * @return $this
	 */
	public function itOn($uri)
	{
		Assert::assertEquals($uri, $this->getClient()->getRequest()->getPathInfo());

		return $this;
	}

	/**
	 * @param $url
	 * @return $this
	 * @throws AssertionFailedError
	 */
	public function isRedirectTo($url)
	{
		$location = $this->getRedirectLocation();
		Assert::assertEquals($url, $location);

		return $this;
	}

	/**
	 * @return array|string
	 * @throws AssertionFailedError
	 */
	private function getRedirectLocation()
	{
		$location = $this->getClient()->getResponse()->headers->get('location');
		if (!$location) {
			throw new AssertionFailedError('The page is not redirect');
		}

		return $location;
	}

	/**
	 * @param $pattern
	 * @return $this
	 * @throws AssertionFailedError
	 */
	public function isRedirectToRegExp($pattern)
	{
		$location = $this->getRedirectLocation();
		Assert::assertRegExp($pattern, $location);

		return $this;
	}

	/**
	 * @param $expected
	 * @param $actual
	 * @return $this
	 */
	public function equals($expected, $actual)
	{
		Assert::assertEquals($expected, $actual);

		return $this;
	}
}