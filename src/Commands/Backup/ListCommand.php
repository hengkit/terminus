<?php
/**
 * @file
 * Contains Pantheon\Terminus\Commands\SSHKey\ListCommand
 */


namespace Pantheon\Terminus\Commands\Backup;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;
use Terminus\Collections\Sites;
use Terminus\Models\Environment;

class ListCommand extends TerminusCommand
{

    /**
     * Contructor that supports injection for unit testing.
     */
    public function __construct($sites = null)
    {
        parent::__construct();
        $this->sites = $sites ? $sites : new Sites();
    }

    /**
     * Lists the Backups for a given Site and Environment
     *
     * @authorized
     *
     * @command backup:list
     * @aliases backups
     *
     * @param string $environment Name of the environment to retrieve
     * @param string $element Element filter code/files/database/db (optional)
     *
     * @param array $options [format=<table|csv|yaml|json>]
     *
     * @return RowsOfFields
     *
     * @field-labels
     *   file: Filename
     *   size: Size
     *   date: Date
     *   initiator: Initiator
     *
     * @example terminus backup:list awesome-site.dev database --format=json
     *
     */
    public function listBackups(
        $environment,
        $element = 'all',
        $options = ['format' => 'table']
    )
    {
        $backups = [[]];

        $site_env = explode('.', $environment);
        if (count($site_env) != 2) {
            $this->log()
                ->error('The environment argument must be given as <site_name>.<environment>');

            return new RowsOfFields($backups);
        }

        $site = $this->sites->get($site_env[0]);
        $env = $site->environments->get($site_env[1]);

        switch ($element) {
            case 'all':
                $backup_element = null;
                break;
            case 'db':
                $backup_element = 'database';
                break;
            default:
                $backup_element = $element;
        }

        $backups = $env->backups->getFinishedBackups($backup_element);
        foreach ($backups as $id => $backup) {
          $data[] = [
            'file'      => $backup->get('filename'),
            'size'      => $backup->getSizeInMb(),
            'date'      => $backup->getDate(),
            'initiator' => $backup->getInitiator(),
          ];
        }

        // Return the output data.
        return new RowsOfFields($data);
    }
}
