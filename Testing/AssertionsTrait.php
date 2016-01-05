<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use PHPUnit_Framework_Assert as Assert;

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
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function isRedirectTo($url)
	{
		$location = $this->getRedirectLocation();
		Assert::assertEquals($url, $location);

		return $this;
	}

	/**
	 * @return array|string
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	private function getRedirectLocation()
	{
		$location = $this->getClient()->getResponse()->headers->get('location');
		if (!$location) {
			throw new \PHPUnit_Framework_AssertionFailedError('The page is not redirect');
		}

		return $location;
	}

	/**
	 * @param $pattern
	 * @return $this
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	public function isRedirectToRegExp($pattern)
	{
		$location = $this->getRedirectLocation();
		Assert::assertRegExp($pattern, $location);

		return $this;
	}
}