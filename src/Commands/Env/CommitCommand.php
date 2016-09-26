<?php

namespace Pantheon\Terminus\Commands\Env;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

class CommitCommand extends TerminusCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Commit code on an environment that is in SFTP mode.
     *
     * @command env:commit
     *
     * @param string $site_env Site & environment to commit code on.
     *
     * @option string $message Commit message
     *
     * @usage terminus env:commit my-site.dev --message="My code changes"
     *   Commit changes to the `dev` environment for site `my-site`.
     */
    public function commit($site_env, $options = ['message' => 'Terminus commit.'])
    {
        list(, $env) = $this->getSiteEnv($site_env, 'dev');

        // TODO: The 0.x command would prompt if there were no changed files, asking
        //   if the user wanted to commit anyway.
        // $change_count = count((array)$env->diffstat());
        // if ($change_count === 0) {...}

        $workflow = $env->commitChanges($options['message']);
        $workflow->wait();
        $this->workflowOutput($workflow, ['failure' => 'The commit workflow failed.',]);
    }
}
