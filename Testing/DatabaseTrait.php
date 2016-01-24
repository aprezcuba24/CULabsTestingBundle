<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Inflector\Inflector;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Component\Process\Process;

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
		try {
			$purger = new ORMPurger($this->em);
			$purger->purge();
		} catch (\Exception $e) {
			$this->recreateDb();
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function recreateDb()
	{
		$process = new Process('php app/console doctrine:database:drop --force -e=test');
		$process->run();
		$process = new Process('php app/console doctrine:database:create -e=test');
		$process->run();
		$process = new Process('php app/console doctrine:schema:create -e=test');
		$process->run();

		return $this;
	}

	/**
	 * @param $class
	 * @param \Closure $callback
	 * @return $this
	 */
	public function factoryEntity($class, \Closure $callback)
	{
		$this->entityFactories[$class] = $callback;

		return $this;
	}

	/**
	 * @param $class
	 * @param array $data
	 * @param bool|TRUE $flush
	 * @return mixed
	 */
	public function makeEntity($class, array $data, $flush = true)
	{
		if (isset($this->entityFactories[$class])) {
			$data = $this->entityFactories[$class]($data);
		}
		$entity = new $class;
		$entity = $this->setEntityValues($entity, $data);
		if ($flush) {
			$this->em->persist($entity);
			$this->em->flush();
		}

		return $entity;
	}

	private function setEntityValues($entity, array $data)
	{
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

		return $entity;
	}

	/**
	 * @param $class
	 * @param array $find
	 * @param array $data
	 * @param bool|TRUE $flush
	 * @return $this
	 */
	public function updateEntity($class, array $find, array $data, $flush = true)
	{
		$entity = $this->findEntity($class, $find);
		if (!$entity) {
			Assert::assertTrue(false, 'Entity not found.');
		}
		$entity = $this->setEntityValues($entity, $data);
		if ($flush) {
			$this->em->persist($entity);
			$this->em->flush();
		}

		return $this;
	}

	/**
	 * @param $class
	 * @param array $criteria
	 * @return mixed
	 */
	public function findEntity($class, array $criteria)
	{
		return $this->em->getRepository($class)->findOneBy($criteria);
	}

	/**
	 * @param $class
	 * @param array $criteria
	 * @return $this
	 */
	public function hasEntity($class, array $criteria, $cant = 1)
	{
		Assert::assertEquals($cant, count($this->findEntities($class, $criteria)));

		return $this;
	}

	/**
	 * @param $class
	 * @param array $criteria
	 * @return mixed
	 */
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