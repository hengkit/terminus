<?php

namespace Pantheon\Terminus\UnitTests\Commands\Multidev;

use Pantheon\Terminus\Commands\Multidev\ListCommand;
use Terminus\Models\Environment;
use Terminus\Models\Site;

/**
 * Testing class for Pantheon\Terminus\Commands\Auth\LoginCommand
 */
class ListCommandTest extends MultidevCommandTest
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new ListCommand($this->getConfig());
        $this->command->setLogger($this->logger);
    }

    /**
     * Tests the multidev:list command when there are no multidev environments
     *
     * @return void
     */
    public function testMultidevListEmpty()
    {
        $site_name = 'awesome-site';
        $this->environments->method('multidev')->willReturn([]);

        $this->logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo('warning'), $this->equalTo('You have no multidev environments.'));

        $out = $this->command->listMultidevs($site_name);
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);
        $this->assertEquals([], $out->getArrayCopy());
    }

    /**
     * Tests the multidev:list command when there are multidev environments
     *
     * @return void
     */
    public function testMultidevListNotEmpty()
    {
        $environment_data = (object)[
          'id' => 'testing',
          'created' => strtotime('1984/07/28 16:40'),
          'domain' => 'domain',
          'on_server_development' => true,
          'php_version' => '70',
          'lock' => (object)['locked' => true,],
        ];

        $this->logger->expects($this->never())
            ->method($this->anything());

        $out = $this->command->listMultidevs($site_name);
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);
        $data = [
          'id' => 'testing',
          'created' => '1984/07/28 16:40',
          'domain' => 'domain',
          'onserverdev' => 'true',
          'locked' => 'false',
          'initialized' => 'true',
        ];

        $this->assertEquals($data, $out->getArrayCopy());
    }
}
