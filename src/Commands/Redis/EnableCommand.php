<?php

namespace Pantheon\Terminus\Commands\Redis;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class EnableCommand
 * @package Pantheon\Terminus\Commands\Redis
 */
class EnableCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Enable Redis caching on a site
     *
     * @authorize
     *
     * @command redis:enable
     *
     * @param string $site_id Name of the site to enable Redis on
     *
     * @usage terminus redis:enable <site>
     *   Enables Redis caching for <site>
     */
    public function enable($site_id)
    {
        $site = $this->getSite($site_id);
        $site->getRedis()->enable();
        $this->log()->notice('Redis enabled. Converging bindings.');
        $workflow = $site->converge();
        // Wait for the workflow to complete.
        while (!$workflow->checkProgress()) {
            // @TODO: Add Symfony progress bar to indicate that something is happening.
        }
        $this->log()->notice($workflow->getMessage());
    }
}
