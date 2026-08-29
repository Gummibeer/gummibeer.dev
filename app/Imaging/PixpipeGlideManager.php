<?php

namespace App\Imaging;

use League\Glide\Api\Api;
use League\Glide\Server;
use Statamic\Imaging\GlideManager;

class PixpipeGlideManager extends GlideManager
{
    public function __construct(
        private readonly Api $pixpipe,
    ) {}

    public function server(array $config = []): Server
    {
        $server = parent::server($config);

        // Retain Statamic's configured GD/Imagick driver while using
        // Pixpipe's manipulator pipeline (including fit=smartcrop).
        $this->pixpipe->setImageManager($server->getApi()->getImageManager());
        $server->setApi($this->pixpipe);

        return $server;
    }
}
