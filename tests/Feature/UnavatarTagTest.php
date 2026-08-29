<?php

namespace Tests\Feature;

use Tests\TestCase;

class UnavatarTagTest extends TestCase
{
    public function test_article_avatar_is_generated_by_unavatar(): void
    {
        $this->get('/blog/how-i-structure-side-projects')
            ->assertSuccessful()
            ->assertSee('https://unavatar.io/gravatar/fdc12b019fcd6ce03bedefae77083c1e', false)
            ->assertDontSee('gmail.com', false);
    }
}
