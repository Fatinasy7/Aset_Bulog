<?php

namespace Tests\Feature;

use Tests\TestCase;

class PicFrontendTest extends TestCase
{
    public function test_pic_page_contains_form_for_api_integration(): void
    {
        $response = $this->get('/pics');

        $response->assertOk();
        $response->assertSee('id="modalPic"', false);
        $response->assertSee('name="nama"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="jabatan"', false);
    }
}
