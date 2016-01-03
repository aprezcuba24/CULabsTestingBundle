<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

trait NavigateTrait
{
	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	/**
	 * @param $uri
	 * @return $this
	 */
	public function go($uri)
	{
		return $this->get($uri);
	}

	/**
	 * @param $link
	 * @return $this
	 */
	public function click($link)
	{
		$link = $this->getClient()->getCrawler()->filter(sprintf('a:contains("%s")', $link))->link();
        $this->getClient()->click($link);

		return $this;
	}

	/**
	 * @param $uri
	 * @return $this
	 */
	public function get($uri)
	{
		return $this->request('GET', $uri);
	}

	/**
	 * @param $uri
	 * @param array $data
	 * @return $this
	 */
	public function post($uri, array $data)
	{
		return $this->request('POST', $uri, $data);
	}

	/**
	 * @param $method
	 * @param $uri
	 * @param array $parameters
	 * @param array $files
	 * @param array $server
	 * @param null $content
	 * @param bool|TRUE $changeHistory
	 * @return $this
	 */
	public function request($method, $uri, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$this->getClient()->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);

		return $this;
	}
}