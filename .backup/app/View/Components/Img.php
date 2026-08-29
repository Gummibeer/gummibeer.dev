<?php

namespace App\View\Components;

use Astrotomic\Imgix\Facades\Imgix;
use Astrotomic\LaravelMime\Facades\MimeTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class Img extends Component
{
    public ?int $width;

    public ?int $height;

    private array $params = [];

    private string $src;

    private ?string $ratio;

    private bool $crop;

    public function __construct(
        string $src,
        ?int $width = null,
        ?int $height = null,
        ?string $ratio = null,
        bool $crop = false
    ) {
        $this->ratio = $ratio;
        $this->crop = $crop;
        $this->setWidth($width);
        $this->setHeight($height);

        if (Str::startsWith($src, ['http://', 'https://'])) {
            $filename = hash('md5', $src);
            $tmppath = public_path('vendor/images/'.$filename);
            $filepath = Arr::first(glob($tmppath.'.*'));

            if (empty($filepath)) {
                @mkdir(dirname($tmppath), 0755, true);
                file_put_contents($tmppath, file_get_contents($src));
                $mimetype = MimeTypes::guessMimeType($tmppath);
                $extension = Arr::first(MimeTypes::getExtensions($mimetype));
                $filepath = $tmppath.'.'.$extension;
                rename($tmppath, $filepath);
            }

            $this->src = trim(str_replace(public_path(), '', $filepath), '/');
        } else {
            $this->src = parse_url($src, PHP_URL_PATH);
        }

        if (! app()->environment('local')) {
            $this->setDefaultParams();
        }
    }

    public function render(): View
    {
        return view('components.img');
    }

    public function src(?string $format = null): string
    {
        if (app()->environment('local')) {
            return asset($this->src);
        }

        return Imgix::createURL(
            $this->src,
            $this->getParams($format),
        );
    }

    public function srcSet(?string $format = null, array $options = []): string
    {
        if (app()->environment('local')) {
            return asset($this->src).' 1x';
        }

        return Imgix::createSrcSet(
            $this->src,
            $this->getParams($format),
            $options
        );
    }

    protected function setHeight(?int $height): self
    {
        $this->height = $height;

        if ($height) {
            $this->params['h'] = $height;
        } else {
            unset($this->params['h']);
        }

        return $this;
    }

    protected function setWidth(?int $width): self
    {
        $this->width = $width;

        if ($width) {
            $this->params['w'] = $width;
        } else {
            unset($this->params['w']);
        }

        return $this;
    }

    protected function setDefaultParams(): void
    {
        $this->params['auto'] = 'compress';
        $this->params['fit'] = 'max';

        if ($this->ratio) {
            $this->crop = true;
            $this->params['ar'] = $this->ratio;

            if ($this->width !== null && $this->height === null) {
                $this->setHeight($this->width / explode(':', $this->ratio)[0] * explode(':', $this->ratio)[1]);
            }

            if ($this->width === null && $this->height !== null) {
                $this->setWidth($this->height / explode(':', $this->ratio)[1] * explode(':', $this->ratio)[0]);
            }
        }

        if ($this->crop) {
            $this->params['fit'] = 'crop';
            $this->params['crop'] = 'edges';
        }
    }

    protected function getParams(?string $format = null): array
    {
        $params = array_merge(
            $this->params,
            array_filter([
                'fm' => $format,
            ])
        );

        return $params;
    }
}
