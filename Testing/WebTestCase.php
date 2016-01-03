<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
	protected $client;

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();
		$this->newClient();
		$this->prepareDatabase();
		$this->prepareFactories();
		$this->prepareTest();
	}

	public function prepareFactories()
	{

	}

	public function prepareTest()
	{

	}

	/**
	 * @return $this
	 */
	public function enableProfiler()
	{
		$this->getClient()->enableProfiler();

		return $this;
	}

	/**
	 * @param array $options
	 * @param array $server
	 * @return $this
	 */
	public function newClient(array $options = array(), array $server = array())
	{
		$this->client = static::createClient($options, $server);

		return $this;
	}

	public function getClient()
	{
		return $this->client;
	}

	public function setClient($client)
	{
		$this->client = $client;
	}
}