<?php

namespace Tests\Feature;

use App\Modifiers\Twemoji;
use Tests\TestCase;

class TwemojiModifierTest extends TestCase
{
    public function test_cms_emoji_are_rendered_as_twemoji_images(): void
    {
        $this->get('/')
            ->assertSuccessful()
            ->assertSee('src="https://cdn.jsdelivr.net/gh/twitter/twemoji@latest/assets/svg/2615.svg"', false)
            ->assertSee('alt="☕"', false)
            ->assertSee('class="twemoji"', false);
    }

    public function test_html_attributes_and_code_are_not_modified(): void
    {
        $html = '<p title="Party 🎉">Hello 🎉</p><code>🎉</code>';
        $rendered = (new Twemoji)->index($html);

        $this->assertStringContainsString('title="Party 🎉"', $rendered);
        $this->assertStringContainsString('Hello <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@latest/assets/svg/1f389.svg"', $rendered);
        $this->assertStringContainsString('<code>🎉</code>', $rendered);
    }
}
