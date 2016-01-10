<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use Symfony\Component\DomCrawler\Form;

trait FormTrait
{
	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	public function getForm($button, array $values = array(), $method = null)
	{
		return $this->getClient()->getCrawler()->selectButton($button)->form($values, $method);
	}

	/**
	 * @param Form|string $form
	 * @param array $values
	 * @return $this
	 */
	public function submit($form, array $values = array())
	{
		if (is_string($form)) {
			$form = $this->getForm($form);
		}
		$this->getClient()->submit($form, $values);

		return $this;
	}
}