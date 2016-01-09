<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Inflector\Inflector;
use PHPUnit_Framework_Assert as Assert;

trait DatabaseTrait
{
	protected $em;
	protected $entityFactories;

	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	public function prepareDatabase()
	{
		$client = $this->getClient();
		$this->em = $client->getContainer()->get('doctrine')->getManager();
	}

	/**
	 * @return $this
	 */
	public function purgeDatabase()
	{
		$purger = new ORMPurger($this->em);
		$purger->purge();

		return $this;
	}

	public function factoryEntity($class, \Closure $callback)
	{
		$this->entityFactories[$class] = $callback;
	}

	public function makeEntity($class, array $data, $flush = true)
	{
		if (isset($this->entityFactories[$class])) {
			$data = $this->entityFactories[$class]($data);
		}
		$entity = new $class;
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$method = 'add'.Inflector::camelize($key);
				foreach ($value as $item) {
					$entity->$method($item);
				}
			} else {
				$method = 'set'.Inflector::camelize($key);
				$entity->$method($value);
			}
		}
		if ($flush) {
			$this->em->persist($entity);
			$this->em->flush();
		}

		return $entity;
	}

	public function findEntity($class, array $criteria)
	{
		return $this->em->getRepository($class)->findOneBy($criteria);
	}

	/**
	 * @param $class
	 * @param array $criteria
	 * @return $this
	 */
	public function hasEntity($class, array $criteria)
	{
		Assert::assertGreaterThan(0, count($this->findEntities($class, $criteria)));

		return $this;
	}

	public function findEntities($class, array $criteria)
	{
		return $this->em->getRepository($class)->findBy($criteria);
	}

	/**
	 * @param $entity
	 * @return $this
	 */
	public function refreshEntity($entity)
	{
		$this->em->refresh($entity);

		return $this;
	}
}