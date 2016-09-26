<?php

namespace Pantheon\Terminus\UnitTests\Commands\Connection;

use Pantheon\Terminus\Commands\Site\ImportCommand;
use Pantheon\Terminus\Config;

use Prophecy\Prophet;
use Terminus\Models\Environment;
use Terminus\Models\Site;
use VCR\VCR;

/**
 * Test suite for class for Pantheon\Terminus\Commands\Connection\ImportCommand
 */
class ImportCommandTest extends ConnectionCommandTest
{
    private $prophet;
    /**
     * Test suite setup
     *
     * @return void
     */
    protected function setup()
    {
        parent::setUp();
        $this->setInput(['command' => 'site:import', 'site' => 'my-site', 'url' => 'https://pantheon-infrastructure.s3.amazonaws.com/testing/duplicator_export.zip']);
        
        $this->command = new ImportCommand($this->getConfig());
        $this->command->setLogger($this->logger);
        $this->prophet = new Prophet;
    }

    protected function tearDown()
    {
        parent::tearDown();
        
    }


    /**
     * Exercises site:import command with a valid site
     * 
     * @vcr auth_login
     * @return void
     *
     * 
     */
    public function testSiteImport()
    {
        $site_prophet = $this->prophet->prophesize(Site::class);
        $site_prophet->get('name')->willReturn('my_site');
        $site = $site_prophet->reveal();
        $this->assertEquals('Hello World!', $this->runCommand()->fetchTrimmedOutput());
    }
    
    /**
     * Exercises site:import command invalid site
     *
     * @return void
     */
    public function testSiteImportInvalidSite()
    {
    }
}