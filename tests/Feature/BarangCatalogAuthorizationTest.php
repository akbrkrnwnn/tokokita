<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Tests\TestCase;

class BarangCatalogAuthorizationTest extends TestCase
{
    public function test_guest_does_not_see_edit_or_delete_actions(): void
    {
        $html = $this->renderCatalog();

        $this->assertStringNotContainsString('>Edit</a>', $html);
        $this->assertStringNotContainsString('>Hapus</button>', $html);
    }

    public function test_seller_sees_actions_for_their_own_product(): void
    {
        $seller = new User(['name' => 'Seller', 'role' => 'seller']);
        $seller->id = 7;
        $this->actingAs($seller);

        $html = $this->renderCatalog();

        $this->assertStringContainsString('>Edit</a>', $html);
        $this->assertStringContainsString('>Hapus</button>', $html);
    }

    private function renderCatalog(): string
    {
        $barang = new Barang([
            'nama' => 'Produk',
            'harga' => 10000,
            'stok' => 1,
            'seller_id' => 7,
        ]);
        $barang->setAttribute('id', 1);
        $barang->setRelation('kategori', null);

        return view('barang.index', ['barangs' => collect([$barang])])->render();
    }
}
