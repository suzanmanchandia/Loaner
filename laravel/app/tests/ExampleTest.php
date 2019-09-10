<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isRedirection());
	}

}
