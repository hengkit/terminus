<?php
namespace Pantheon\Terminus\UnitTests\Commands\Backup;

use Pantheon\Terminus\UnitTests\Commands\CommandTestCase;

use Pantheon\Terminus\Session\Session;
use Psr\Log\NullLogger;
use Terminus\Collections\Sites;
use Terminus\Models\Site;
use Terminus\Collections\Environments;
use Terminus\Models\Environment;
use Terminus\Collections\Backups;
use Terminus\Models\Backup;

/**
 * @property \PHPUnit_Framework_MockObject_MockObject sites
 */
abstract class BackupCommandTest extends CommandTestCase
{
    protected $session;
    protected $sites;
    protected $user;
    protected $logger;
    protected $command;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->sites = $this->getMockBuilder(Sites::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->site = $this->getMockBuilder(Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sites->method('get')
            ->willReturn($this->site);

        $this->site->environments = $this->getMockBuilder(Environments::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->env = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->site->environments->method('get')
            ->willReturn($this->env);

        $this->backups = $this->getMockBuilder(Backups::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->backup = $this->getMockBuilder(Backup::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->env->backups = $this->backups;

        $this->logger = $this->getMockBuilder(NullLogger::class)
            ->setMethods(array('log'))
            ->getMock();
    }
}
