<?php

namespace App\Tags;

use Astrotomic\Unavatar\Unavatar as UnavatarUrl;
use Statamic\Tags\Tags;

class Unavatar extends Tags
{
    /**
     * Build an Unavatar URL from CMS-managed provider and identifier fields.
     */
    public function index(): ?string
    {
        $identifier = trim((string) $this->params->get('identifier'));

        if ($identifier === '') {
            return null;
        }

        $provider = $this->params->get('provider');
        $unavatar = new UnavatarUrl($identifier, $provider ?: null);

        if ($fallback = $this->params->get('fallback')) {
            $unavatar->fallback($fallback);
        }

        return $unavatar->toUrl();
    }
}
