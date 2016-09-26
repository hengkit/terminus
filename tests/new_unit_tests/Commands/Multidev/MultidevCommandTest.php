<?php

namespace Pantheon\Terminus\UnitTests\Commands\Multidev;

use Pantheon\Terminus\UnitTests\Commands\CommandTestCase;
use Psr\Log\NullLogger;

abstract class MultidevCommandTest extends CommandTestCase
{
    protected $logger;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->logger = $this->getMockBuilder(NullLogger::class)
          ->setMethods(['log',])
          ->getMock();

        $this->environments = $this->getMockBuilder(Environments::class)
          ->disableOriginalConstructor()
          ->getMock();
    }
}
