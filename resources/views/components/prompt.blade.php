<?php /** @var string $id */ ?>
<?php /** @var string $path */ ?>
<?php /** @var string $prompt */ ?>
<?php /** @var string $title */ ?>

<x-text-file
    :id="$id"
    :title="$title"
    :content="$prompt"
    :path="$path"
    copy-label="copy prompt to clipboard"
    download-label="download prompt"
/>
