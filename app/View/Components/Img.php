<?php

namespace App\View\Components;

use Astrotomic\LaravelMime\Facades\MimeTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Statamic\Contracts\Imaging\UrlBuilder;

class Img extends Component
{
    public ?int $width;

    public ?int $height;

    private array $params = [];

    private string $src;

    private ?string $ratio;

    private bool $crop;

    private UrlBuilder $urlBuilder;

    public function __construct(
        string $src,
        ?int $width = null,
        ?int $height = null,
        ?string $ratio = null,
        bool $crop = false
    ) {
        $this->urlBuilder = app(UrlBuilder::class);
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
            $this->src = trim((string) parse_url($src, PHP_URL_PATH), '/');
        }

        $this->setDefaultParams();
    }

    public function render(): View
    {
        return view('components.img');
    }

    public function src(?string $format = null): string
    {
        return $this->urlBuilder->build(
            $this->src,
            $this->getParams($format),
        );
    }

    public function srcSet(?string $format = null, array $options = []): string
    {
        $params = $this->getParams($format);
        $srcset = [$this->urlBuilder->build($this->src, $params).' 1x'];

        if ($this->width !== null || $this->height !== null) {
            $retina = $params;

            if (isset($retina['w'])) {
                $retina['w'] *= 2;
            }

            if (isset($retina['h'])) {
                $retina['h'] *= 2;
            }

            $srcset[] = $this->urlBuilder->build($this->src, $retina).' 2x';
        }

        return implode(', ', $srcset);
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
        $this->params['fit'] = 'max';

        if ($this->ratio) {
            $this->crop = true;
            [$ratioWidth, $ratioHeight] = array_map('intval', explode(':', $this->ratio, 2));

            if ($this->width !== null && $this->height === null) {
                $this->setHeight((int) round($this->width / $ratioWidth * $ratioHeight));
            }

            if ($this->width === null && $this->height !== null) {
                $this->setWidth((int) round($this->height / $ratioHeight * $ratioWidth));
            }
        }

        if ($this->crop) {
            $this->params['fit'] = 'smartcrop';
        }
    }

    protected function getParams(?string $format = null): array
    {
        return array_merge(
            $this->params,
            array_filter([
                'fm' => $format,
            ])
        );
    }
}
