<?php

/**
 * @author: Renier Ricardo Figueredo
 * @mail: aprezcuba24@gmail.com
 */
namespace CULabs\TestingBundle\Testing;

use Doctrine\Common\Util\Inflector;
use PHPUnit\Framework\Assert;

trait MailTrait
{
	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Client
	 */
	abstract public function getClient();

	private function getMailCollector()
	{
		return $this->getClient()->getProfile()->getCollector('swiftmailer');
	}

	/**
	 * @param $count
	 * @return $this
	 */
	public function countMail($count)
	{
		Assert::assertEquals($count, $this->getMailCollector()->getMessageCount());

		return $this;
	}

	/**
	 * @param $to
	 * @param array $data
	 * @return $this
	 */
	public function hasMailTo($to, array $data = array())
	{
		/**@var $message \Swift_Message*/
		foreach ($this->getMailCollector()->getMessages() as $message) {
			if (key($message->getTo()) == $to) {
				foreach ($data as $key => $value) {
					$methodValue = $message->{'get'.Inflector::camelize($key)}();
					if (strtolower($key) == 'body') {
						Assert::assertContains($value, $methodValue);
					} else {
						Assert::assertEquals($value, $methodValue);
					}
				}

				return $this;
			}
		}
		Assert::assertTrue(false, 'Not has mail to '.$to);

		return $this;
	}
}