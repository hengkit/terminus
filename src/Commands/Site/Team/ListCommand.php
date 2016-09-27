<?php

namespace Pantheon\Terminus\Commands\Site\Team;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

class ListCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * List team members for a site.
     *
     * @command site:team:list
     *
     * @param string $site_id Site name to list team members for.
     *
     * @usage terminus site:team:list my-site
     *   List team members for the site named `my-site`.
     */
    public function list($site_id)
    {
        $site = $this->getSite($site_id);
        $team = $site->user_memberships;
        $user_memberships = $team->all();
        $data = [];
        foreach ($user_memberships as $user_membership) {
            $user = $user_membership->get('user');
            $data[] = array(
                'First' => $user->profile->firstname,
                'Last'  => $user->profile->lastname,
                'Email' => $user->email,
                'Role'  => $user_membership->get('role'),
                'UUID'  => $user->id,
            );
        }
        return new RowsOfFields($data);
    }
}
