<?php
namespace tests\DNSMadeEasy;

use DNSMadeEasy\Client;
use tests\Base;

/**
 * Tests for the client.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class ClientTest extends Base
{
    /**
     * An instance of the client.
     * @var Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = $this->getClient();
    }

    /**
     * @covers \DNSMadeEasy\Client::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $reflectionClass->getProperty('_config');
        $configuration->setAccessible(true);

        $driver = $reflectionClass->getProperty('_driver');
        $driver->setAccessible(true);

        $client = new Client($this->getApiKey(), $this->getSecretKey(), true);

        $this->assertInstanceOf('DNSMadeEasy\Driver\Configuration', $configuration->getValue($client),
            'The configuration object should be of the type DNSMadeEasy\Driver\Configuration');
        $this->assertInstanceOf('DNSMadeEasy\Driver\REST', $driver->getValue($client),
            'The REST driver should be of the type DNSMadeEasy\Driver\REST');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Domains', $client->domains(),
            'The domains manager should be of the type DNSMadeEasy\Resource\Domains');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Records', $client->records(),
            'The records manager should be of the type DNSMadeEasy\Resource\Records');
        $this->assertInstanceOf('DNSMadeEasy\Resource\SoaRecords', $client->soaRecords(),
            'The SoA records manager should be of the type DNSMadeEasy\Resource\SoaRecords');
        $this->assertInstanceOf('DNSMadeEasy\Resource\VanityDNS', $client->vanityDNS(),
            'The vanity DNS manager should be of the type DNSMadeEasy\Resource\VanityDNS');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Templates', $client->templates(),
            'The templates manager should be of the type DNSMadeEasy\Resource\Templates');
        $this->assertInstanceOf('DNSMadeEasy\Resource\TemplateRecords', $client->templateRecords(),
            'The template recorsd manager should be of the type DNSMadeEasy\Resource\TemplateRecords');
        $this->assertInstanceOf('DNSMadeEasy\Resource\TransferACL', $client->transferACL(),
            'The transfer ACL manager should be of the type DNSMadeEasy\Resource\TransferACL');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Folders', $client->folders(),
            'The folders manager should be of the type DNSMadeEasy\Resource\Folders');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Usage', $client->usage(),
            'The usage manager should be of the type DNSMadeEasy\Resource\Usage');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Failover', $client->failover(),
            'The failover manager should be of the type DNSMadeEasy\Resource\Failover');
        $this->assertInstanceOf('DNSMadeEasy\Resource\Secondary', $client->secondary(),
            'The secondary manager should be of the type DNSMadeEasy\Resource\Secondary');
        $this->assertInstanceOf('DNSMadeEasy\Resource\SecondaryRecords', $client->secondaryRecords(),
            'The secondary records manager should be of the type DNSMadeEasy\Resource\SecondaryRecords');
    }

    /**
     * @covers \DNSMadeEasy\Client::useSandbox
     */
    public function testUseSandbox()
    {
        $clientClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $clientClass->getProperty('_config');
        $configuration->setAccessible(true);

        $configurationClass = new \ReflectionClass('DNSMadeEasy\Driver\Configuration');

        $useSandbox = $configurationClass->getProperty('_useSandbox');
        $useSandbox->setAccessible(true);

        $this->client->useSandbox(false);
        $this->assertFalse($useSandbox->getValue($configuration->getValue($this->client)),
            "useSandbox should be false");

        $this->client->useSandbox(true);
        $this->assertTrue($useSandbox->getValue($configuration->getValue($this->client)), "useSandbox should be true");
    }

    /**
     * @covers \DNSMadeEasy\Client::debug
     */
    public function testDebug()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $reflectionClass->getProperty('_config');
        $configuration->setAccessible(true);

        $this->client->debug(false);
        $this->assertFalse($configuration->getValue($this->client)->getDebug(), "Debug should be false");

        $this->client->debug(true);
        $this->assertTrue($configuration->getValue($this->client)->getDebug(), "Debug should be true");
    }
}
