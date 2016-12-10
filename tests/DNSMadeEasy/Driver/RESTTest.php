<?php

namespace tests\DNSMadeEasy\Driver;

use DNSMadeEasy\Driver\Configuration;
use DNSMadeEasy\Driver\REST;
use tests\Base;

/**
 * Tests for the REST driver.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class RESTTest extends Base
{
    /**
     * An instance of the REST driver.
     * @var REST
     */
    protected $rest;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $configuration = new Configuration($this->getApiKey(), $this->getSecretKey(), true);

        $this->rest = new REST($configuration);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');

        $configuration = $reflectionClass->getProperty('_config');
        $configuration->setAccessible(true);

        $debugger = $reflectionClass->getProperty('_debugger');
        $debugger->setAccessible(true);

        $uriTemplate = $reflectionClass->getProperty('_uriTemplate');
        $uriTemplate->setAccessible(true);

        $rest = new REST(new Configuration($this->getApiKey(), $this->getSecretKey(), true));

        $this->assertInstanceOf('DNSMadeEasy\Driver\Configuration', $configuration->getValue($rest),
            'The configuration object should be of the type DNSMadeEasy\Driver\Configuration');
        $this->assertInstanceOf('DNSMadeEasy\Debug\Debugger', $debugger->getValue($rest),
            'The debugger should be of the type DNSMadeEasy\Debug\Debugger');
        $this->assertInstanceOf('DNSMadeEasy\Driver\URITemplate', $uriTemplate->getValue($rest),
            'The URITemplate object should be of the type DNSMadeEasy\Driver\URITemplate');
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::get
     */
    public function testGet()
    {
        $result = $this->rest->get('/dns/managed');
        $this->assertInstanceOf('DNSMadeEasy\Result', $result, 'The result should be of the type DNSMadeEasy\Result');
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::post
     * @covers DNSMadeEasy\Driver\REST::put
     * @covers DNSMadeEasy\Driver\REST::delete
     */
    public function testPostPutAndDelete()
    {
        $config = array(
            'name' => 'PHPLibraryTest',
            'email' => 'php.library.com',
            'ttl' => 86400,
            'comp' => 'ns.phplibrarytest.com',
            'serial' => 2012020203,
            'refresh' => 14400,
            'retry' => 1800,
            'expire' => 86400,
            'negativeCache' => 1800
        );

        //POST
        $result = $this->rest->post('/dns/soa', $config);
        $this->assertTrue($result->success, "Creating the SoA was unsuccessful");
        $this->assertInstanceOf('DNSMadeEasy\Result', $result, 'The result should be of the type DNSMadeEasy\Result');
        $this->assertEquals('PHPLibraryTest', $result->body->name, "The created SoA does not match");

        $id = $result->body->id;

        //PUT
        $config['email'] = 'php2.library.com';
        $result = $this->rest->put("/dns/soa/$id", $config);
        $this->assertTrue($result->success, "Updating the SoA was unsuccessful");

        //DELETE
        $result = $this->rest->delete("/dns/soa/$id");
        $this->assertTrue($result->success, "Deleting the SoA was unsuccessful");
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::getAuthenticationHeaders
     */
    public function testGetAuthenticationHeaders()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');

        $getAuthenticationHeaders = $reflectionClass->getMethod('getAuthenticationHeaders');
        $getAuthenticationHeaders->setAccessible(true);

        $result = $getAuthenticationHeaders->invoke($this->rest);

        $this->assertInternalType('array', $result, "The result should be an array");
        $this->assertCount(3, $result, "The array should only contain 3 elements");
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::getAuthenticationHeaders
     */
    public function testGetAuthenticationHeadersWithoutAPIKeys()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');

        $getAuthenticationHeaders = $reflectionClass->getMethod('getAuthenticationHeaders');
        $getAuthenticationHeaders->setAccessible(true);

        $configuration = new Configuration(null, null, true);
        $rest = new REST($configuration);

        try {
            $result = $getAuthenticationHeaders->invoke($rest);
        } catch (\Exception $e) {
            $this->assertInstanceOf('DNSMadeEasy\Exception\RESTException', $e,
                'Exception thrown was not a DNSMadeEasy\Exception\RESTException');

            return;
        }

        $this->fail('Tried to generate authentication headers without an api key or secret key, but no exception was thrown');
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::send
     */
    public function testSend()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');

        $send = $reflectionClass->getMethod('send');
        $send->setAccessible(true);

        $result = $send->invoke($this->rest, '/dns/soa{?rows,page}', array('rows' => 1, 'page' => 1), 'GET');
        $this->assertTrue($result->success, "Getting the list of SoAs was unsuccessful");
        $this->assertInstanceOf('DNSMadeEasy\Result', $result, 'The result should be of the type DNSMadeEasy\Result');

        $config = array(
            'name' => 'PHPLibraryTest',
            'email' => 'php.library.com',
            'ttl' => 86400,
            'comp' => 'ns.phplibrarytest.com',
            'serial' => 2012020203,
            'refresh' => 14400,
            'retry' => 1800,
            'expire' => 86400,
            'negativeCache' => 1800
        );

        $result = $send->invoke($this->rest, '/dns/soa', array(), 'POST', $config);
        $this->assertTrue($result->success, "Creating the SoA was unsuccessful");
        $this->assertInstanceOf('DNSMadeEasy\Result', $result, 'The result should be of the type DNSMadeEasy\Result');
        $this->assertEquals('PHPLibraryTest', $result->body->name, "The created SoA does not match");

        $id = $result->body->id;

        //PUT
        $config['email'] = 'php2.library.com';
        $result = $send->invoke($this->rest, "/dns/soa/$id", array(), 'PUT', $config);
        $this->assertTrue($result->success, "Updating the SoA was unsuccessful");

        //DELETE
        $result = $send->invoke($this->rest, "/dns/soa/$id", array(), 'DELETE');
        $this->assertTrue($result->success, "Deleting the SoA was unsuccessful");
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::send
     */
    public function testSendInDebugMode()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');

        $send = $reflectionClass->getMethod('send');
        $send->setAccessible(true);

        $configuration = new Configuration($this->getApiKey(), $this->getSecretKey(), true);
        $configuration->debug(true);
        $rest = new REST($configuration);

        $this->setOutputCallback(function ($output) {
            $this->assertInternalType('string', $output);
        });

        $send->invoke($rest, '/dns/soa{?rows,page}', array('rows' => 1, 'page' => 1), 'GET');
    }

    /**
     * @covers DNSMadeEasy\Driver\REST::send
     */
    public function testSendWithInvalidAPIEndpoint()
    {
        $restClass = new \ReflectionClass('DNSMadeEasy\Driver\REST');
        $configurationClass = new \ReflectionClass('DNSMadeEasy\Driver\Configuration');

        $send = $restClass->getMethod('send');
        $send->setAccessible(true);

        $sandboxURL = $configurationClass->getProperty('_sandboxURL');
        $sandboxURL->setAccessible(true);

        $configuration = new Configuration('123456', 'abcdef', true);
        $sandboxURL->setValue($configuration, 'https://sitethatdoesnotexist' . time() . '.com');

        $rest = new REST($configuration);

        try {
            $send->invoke($rest, '/dns/soa', array(), 'GET');
        } catch (\Exception $e) {
            $this->assertInstanceOf('DNSMadeEasy\Exception\RESTException', $e,
                'Exception thrown was not a DNSMadeEasy\Exception\RESTException');

            return;
        }

        $this->fail('Tried to send request to an invalid endpoint, but no exception was thrown');

    }
}
