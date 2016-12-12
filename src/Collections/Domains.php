<?php

namespace Pantheon\Terminus\Collections;

use Pantheon\Terminus\Exceptions\TerminusNotFoundException;

/**
 * Class Domains
 * @package Pantheon\Terminus\Collections
 */
class Domains extends EnvironmentOwnedCollection
{
    /**
     * @var string
     */
    protected $collected_class = 'Pantheon\Terminus\Models\Domain';

    /**
     * @var string
     */
    protected $url = 'sites/{site_id}/environments/{environment_id}/hostnames';

    /**
     * @var mixed Use to hydrate the data with additional information
     */
    protected $hydrate = false;

    /**
     * Adds a domain to the environment
     *
     * @param string $domain Domain to add to environment
     * @return array
     */
    public function create($domain)
    {
        $url = $this->replaceUrlTokens('sites/{site_id}/environments/{environment_id}/hostnames/');
        $url .= rawurlencode($domain);
        $this->request->request($url, ['method' => 'put',]);
    }

    /**
     * Changes the value of the hydration property
     *
     * @param mixed $value Value to set the hydration property to
     * @return Domains
     */
    public function setHydration($value)
    {
        $this->hydrate = $value;
        return $this;
    }

    public function getUrl()
    {
        return parent::getUrl() . '?hydrate=' . $this->hydrate;
    }

    /**
     * Does the Domains collection contain the given domain?
     *
     * @param $domain
     * @return bool True if the domain exists in the collection.
     */
    public function has($domain)
    {
        try {
            $this->get($domain);
            return true;
        } catch (TerminusNotFoundException $e) {
            return false;
        }
    }
}
