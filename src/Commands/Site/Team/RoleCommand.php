<?php

namespace Pantheon\Terminus\Commands\Site\Team;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Terminus\Exceptions\TerminusException;

class RoleCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Change a team member's role.
     *
     * @command site:team:role
     *
     * @param string $site_id Site name to change roles on.
     *
     * @option string $member Email of the member to change.
     * @option string $role Role to designate the member as.
     *
     * @usage terminus site:team:role my-site --member=admin@agency.com --role=admin
     *   Change `admin@agency.com` to be role `admin` on the site `my-site`.
     */
    public function role($site_id, $options = ['member' => '', 'role' => ''])
    {
        $site = $this->getSite($site_id);
        if ((boolean)$site->getFeature('change_management')) {
            $workflow = $user->setRole($options['role']);
            $this->workflowOutput($workflow);
        } else {
            throw new TerminusException('This site does not have its change-management option enabled.');
        }
    }
}
