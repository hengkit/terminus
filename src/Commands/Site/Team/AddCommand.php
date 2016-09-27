<?php

namespace Pantheon\Terminus\Commands\Site\Team;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

class AddCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Add a team member to a site.
     *
     * @command site:team:add
     *
     * @param string $site_id Site name to add team members to.
     *
     * @option string $member Email of the user to add, they will receive an invite.
     * @option string $role Role to designate the member as.
     *
     * @usage terminus site:team:add my-site --member=admin@agency.com --role=team_member
     *   Add `admin@agency.com` as a `team_member` to the site `my-site`.
     */
    public function add($site_id, $options = ['member' => '', 'role' => ''])
    {
        $site = $this->getSite($site_id);
        $team = $site->user_memberships;

        if ((boolean)$site->getFeature('change_management')) {
            $role = $options['role'];
        } else {
            $role = 'team_member';
        }
        $workflow = $team->create($options['member'], $role);
        $this->workflowOutput($workflow);
    }
}
