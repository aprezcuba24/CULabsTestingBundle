<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use PHPUnit\Framework\Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;

trait RESTTrait
{
	protected $jsonDataCatch;

	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	/**
	 * @param bool|FALSE $assoc
	 * @return $this
	 */
	public function catchJson($assoc = false)
	{
		$this->jsonDataCatch = json_decode($this->getClient()->getResponse()->getContent(), $assoc);

		return $this;
	}

	/**
	 * @param $field
	 * @return mixed
	 */
	public function json($field)
	{
		$accessor = PropertyAccess::createPropertyAccessor();

		return $accessor->getValue($this->jsonDataCatch, $field);
	}
}