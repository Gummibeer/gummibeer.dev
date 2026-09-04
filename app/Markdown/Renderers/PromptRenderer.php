<?php

namespace App\Markdown\Renderers;

use App\Markdown\Nodes\Prompt;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use RuntimeException;

final class PromptRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable|string|null
    {
        if (! $node instanceof Prompt) {
            throw new InvalidArgumentException('Incompatible node type: '.get_class($node));
        }

        $path = $this->resolvePath($node->path());

        return view('components.prompt', [
            'id' => 'prompt-'.substr(hash('sha256', $node->path()), 0, 12),
            'path' => '/'.ltrim($node->path(), '/'),
            'prompt' => File::get($path),
            'title' => $node->title(),
        ])->render();
    }

    private function resolvePath(string $path): string
    {
        $root = realpath(public_path('assets'));
        $resolved = realpath(public_path($path));

        if (
            $root === false
            || $resolved === false
            || ! str_starts_with($resolved, $root.DIRECTORY_SEPARATOR)
            || ! is_file($resolved)
        ) {
            throw new RuntimeException("Prompt asset [{$path}] does not exist inside public/assets.");
        }

        return $resolved;
    }
}
