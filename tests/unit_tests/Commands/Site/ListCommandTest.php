<?php

namespace Pantheon\Terminus\UnitTests\Commands\Site;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\Site\ListCommand;
use Pantheon\Terminus\Session\Session;
use Pantheon\Terminus\UnitTests\Commands\CommandTestCase;

/**
 * Class ListCommandTest
 * Test suite class for Pantheon\Terminus\Commands\Site\ListCommand
 * @package Pantheon\Terminus\UnitTests\Commands\Site
 */
class ListCommandTest extends CommandTestCase
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @inheritdoc
     */
    protected function setup()
    {
        parent::setUp();
        $this->session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new ListCommand($this->getConfig());
        $this->command->setSites($this->sites);
        $this->command->setLogger($this->logger);
        $this->command->setSession($this->session);
    }

    /**
     * Tests the site:list command with no filters and all membership types
     */
    public function testListAllSites()
    {
        $dummy_info = [
            'name' => 'my-site',
            'id' => 'site_id',
            'service_level' => 'pro',
            'framework' => 'cms',
            'owner' => 'user_id',
            'created' => '1984-07-28 16:40',
            'memberships' => 'org_id: org_url',
        ];

        $this->site->memberships = ['org_id: org_url'];
        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->never())
            ->method('filterByOwner');
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['abc' => $dummy_info, 'def' => $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index();
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['abc' => $dummy_info, 'def' => $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Exercises site:list with no filters and team membership type
     */
    public function testListTeamSitesOnly()
    {
        $dummy_info = [
            'name' => 'my-site',
            'id' => 'site_id',
            'service_level' => 'pro',
            'framework' => 'cms',
            'owner' => 'user_id',
            'created' => '1984-07-28 16:40',
            'memberships' => 'user_id: Team',
        ];

        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => true,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->never())
            ->method('filterByOwner');
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['a' => $dummy_info, 'b' =>  $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index(['team' => true, 'owner' => null, 'org' => null, 'name' => null,]);
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['a' => $dummy_info, 'b' =>  $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Tests the site:list command using no filters and belonging to an org
     */
    public function testListOrgSitesOnly()
    {
        $dummy_info = [
            'name' => 'my-site',
            'id' => 'site_id',
            'service_level' => 'pro',
            'framework' => 'cms',
            'owner' => 'user_id',
            'created' => '1984-07-28 16:40',
            'memberships' => 'org_id: org_url',
        ];

        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => 'org_id', 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->never())
            ->method('filterByOwner');
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['a' => $dummy_info, 'b' =>  $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index(['team' => false, 'owner' => null, 'org' => 'org_id', 'name' => null,]);
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['a' => $dummy_info, 'b' =>  $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Tests the site:list command when filtering for either membership type
     */
    public function testListByNameRegex()
    {
        $dummy_info = [
            'name' => 'my-site',
            'id' => 'site_id',
            'service_level' => 'pro',
            'framework' => 'cms',
            'owner' => 'user_id',
            'created' => '1984-07-28 16:40',
            'memberships' => 'org_id: org_url',
        ];
        $regex = '(.*)';

        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->once())
            ->method('filterByName')
            ->with($this->equalTo($regex))
            ->willReturn($this->sites);
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->never())
            ->method('filterByOwner');
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['a' => $dummy_info, 'b' =>  $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index(['team' => false, 'owner' => null, 'org' => null, 'name' => $regex,]);
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['a' => $dummy_info, 'b' =>  $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Tests the site:list command when either membership type owned by a user of a given ID
     */
    public function testListByOwner()
    {
        $user_id = 'user_id';
        $dummy_info = [
          'name' => 'my-site',
          'id' => 'site_id',
          'service_level' => 'pro',
          'framework' => 'cms',
          'owner' => $user_id,
          'created' => '1984-07-28 16:40',
          'memberships' => 'org_id: org_url',
        ];

        $this->site->memberships = ['org_id: org_url'];
        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->once())
            ->method('filterByOwner')
            ->with($this->equalTo($user_id))
            ->willReturn($this->sites);
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['a' => $dummy_info, 'b' =>  $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index(['team' => false, 'owner' => $user_id, 'org' => null, 'name' => null,]);
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['a' => $dummy_info, 'b' =>  $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Tests the site:list command when either membership type owned by a user is the logged-in user
     */
    public function testListMyOwn()
    {
        $user_id = 'user_id';
        $dummy_info = [
            'name' => 'my-site',
            'id' => 'site_id',
            'service_level' => 'pro',
            'framework' => 'cms',
            'owner' => $user_id,
            'created' => '1984-07-28 16:40',
            'memberships' => 'org_id: org_url',
        ];
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->id = $user_id;

        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->once())
            ->method('getUser')
            ->with()
            ->willReturn($user);
        $this->sites->expects($this->once())
            ->method('filterByOwner')
            ->with($this->equalTo($user_id))
            ->willReturn($this->sites);
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn(['a' => $dummy_info, 'b' =>  $dummy_info,]);
        $this->logger->expects($this->never())
            ->method('log');

        $out = $this->command->index(['team' => false, 'owner' => 'me', 'org' => null, 'name' => null,]);
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals(['a' => $dummy_info, 'b' =>  $dummy_info,], $out->getArrayCopy());
    }

    /**
     * Tests the site:list command when the user has no sites
     */
    public function testListNoSites()
    {
        $this->sites->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(['org_id' => null, 'team_only' => false,]))
            ->willReturn($this->sites);
        $this->sites->expects($this->never())
            ->method('filterByName');
        $this->session->expects($this->never())
            ->method('getUser');
        $this->sites->expects($this->never())
            ->method('filterByOwner');
        $this->sites->expects($this->once())
            ->method('serialize')
            ->with()
            ->willReturn([]);
        $this->logger->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo('notice'),
                $this->equalTo('You have no sites.')
            );

        $out = $this->command->index();
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals([], $out->getArrayCopy());
    }
}
