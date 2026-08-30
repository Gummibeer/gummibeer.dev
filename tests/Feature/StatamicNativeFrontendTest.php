<?php

namespace Tests\Feature;

use Tests\TestCase;

final class StatamicNativeFrontendTest extends TestCase
{
    public function test_blog_uses_native_statamic_query_string_pagination(): void
    {
        $this->get('/blog')
            ->assertOk()
            ->assertSee('?page=2', false);

        $this->get('/blog?page=2')->assertOk();
        // ToDo: $this->get('/blog/p:2')->assertNotFound();
    }

    public function test_category_archive_is_rendered_by_statamic_taxonomy_content(): void
    {
        $this->get('/blog/categories/laravel')
            ->assertOk()
            ->assertSee('Posts about Laravel');
    }
}
