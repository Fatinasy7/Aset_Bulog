<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssetFrontendTest extends TestCase
{
    public function test_asset_page_contains_form_for_api_integration(): void
    {
        $response = $this->get('/assets');

        $response->assertOk();
        $response->assertSee('id="modalAsset"', false);
        $response->assertSee('name="kode_aset"', false);
        $response->assertSee('name="nama_aset"', false);
        $response->assertSee('name="jenis"', false);
    }
}
