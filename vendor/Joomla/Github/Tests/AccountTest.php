<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Account;
use Joomla\Registry\Registry;

/**
 * Test class for Joomla\Github\Account.
 *
 * @since  1.0
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Joomla\Registry\Registry  Options for the GitHub object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    \Joomla\Github\Http  Mock client object.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  1.0
	 */
	protected $response;

	/**
	 * @var    Account  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    string  Sample JSON string.
	 * @since  1.0
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  1.0
	 */
	protected $errorString = '{"message": "Generic Error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->options = new Registry;
		$this->client = $this->getMock('\\Joomla\\Github\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		$this->object = new Account($this->options, $this->client);
	}

	/**
	 * Tests the createAuthorisation method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCreateAuthorisation()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$authorisation = new \stdClass;
		$authorisation->scopes = array('public_repo');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('post')
			->with('/authorizations', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->createAuthorisation(array('public_repo'), 'My test app', 'http://www.joomla.org'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the createAuthorisation method - simulated failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testCreateAuthorisationFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$authorisation = new \stdClass;
		$authorisation->scopes = array('public_repo');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('post')
			->with('/authorizations', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->object->createAuthorisation(array('public_repo'), 'My test app', 'http://www.joomla.org');
	}

	/**
	 * Tests the deleteAuthorisation method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDeleteAuthorisation()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('delete')
			->with('/authorizations/42')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->deleteAuthorisation(42),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the deleteAuthorisation method - simulated failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testDeleteAuthorisationFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('delete')
			->with('/authorizations/42')
			->will($this->returnValue($this->response));

		$this->object->deleteAuthorisation(42);
	}

	/**
	 * Tests the editAuthorisation method - Add scopes
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testEditAuthorisationAddScopes()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$authorisation = new \stdClass;
		$authorisation->add_scopes = array('public_repo', 'gist');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('patch')
			->with('/authorizations/42', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->editAuthorisation(42, array(), array('public_repo', 'gist'), array(), 'My test app', 'http://www.joomla.org'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the editAuthorisation method - Remove scopes
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testEditAuthorisationRemoveScopes()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$authorisation = new \stdClass;
		$authorisation->remove_scopes = array('public_repo', 'gist');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('patch')
			->with('/authorizations/42', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->editAuthorisation(42, array(), array(), array('public_repo', 'gist'), 'My test app', 'http://www.joomla.org'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the editAuthorisation method - Scopes param
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testEditAuthorisationScopes()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$authorisation = new \stdClass;
		$authorisation->scopes = array('public_repo', 'gist');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('patch')
			->with('/authorizations/42', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->editAuthorisation(42, array('public_repo', 'gist'), array(), array(), 'My test app', 'http://www.joomla.org'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the editAuthorisation method - simulated failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testEditAuthorisationFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$authorisation = new \stdClass;
		$authorisation->add_scopes = array('public_repo', 'gist');
		$authorisation->note = 'My test app';
		$authorisation->note_url = 'http://www.joomla.org';

		$this->client->expects($this->once())
			->method('patch')
			->with('/authorizations/42', json_encode($authorisation))
			->will($this->returnValue($this->response));

		$this->object->editAuthorisation(42, array(), array('public_repo', 'gist'), array(), 'My test app', 'http://www.joomla.org');
	}

	/**
	 * Tests the editAuthorisation method - too many scope params
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  RuntimeException
	 */
	public function testEditAuthorisationTooManyScopes()
	{
		$this->object->editAuthorisation(42, array(), array('public_repo', 'gist'), array('public_repo', 'gist'), 'My test app', 'http://www.joomla.org');
	}

	/**
	 * Tests the getAuthorisation method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetAuthorisation()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/authorizations/42')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getAuthorisation(42),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getAuthorisation method - failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testGetAuthorisationFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/authorizations/42')
			->will($this->returnValue($this->response));

		$this->object->getAuthorisation(42);
	}

	/**
	 * Tests the getAuthorisations method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetAuthorisations()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/authorizations')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getAuthorisations(),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getAuthorisations method - failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testGetAuthorisationsFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/authorizations')
			->will($this->returnValue($this->response));

		$this->object->getAuthorisations();
	}

	/**
	 * Tests the getRateLimit method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetRateLimit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/rate_limit')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRateLimit(),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getRateLimit method - failure
	 *
	 * @return  void
	 *
	 * @since              1.0
	 * @expectedException  DomainException
	 */
	public function testGetRateLimitFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/rate_limit')
			->will($this->returnValue($this->response));

		$this->object->getRateLimit();
	}
}
