<?php

namespace Pantheon\Terminus\Commands\Site\Team;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Terminus\Exceptions\TerminusException;

class RemoveCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Remove a team member from the site's team.
     *
     * @command site:team:remove
     *
     * @param string $site_id Site name to remove member from.
     *
     * @option string $member Email of the member to remove.
     *
     * @usage terminus site:team:remove my-site --member=admin@agency.com
     *   Remove `admin@agency.com` from the site `my-site`.
     */
    public function remove($site_id, $options = ['member' => ''])
    {
        $site = $this->getSite($site_id);
        $team = $site->user_memberships;
        $user = $team->get($options['member']);

        $workflow = $user->delete($options['member']);
        $this->workflowOutput($workflow);
    }
}
