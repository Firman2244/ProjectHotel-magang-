<?php

namespace Tests\Unit\Helpers;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit test getImageUrl() (App\Helpers\ImageHelper).
 * Test Double: Fake filesystem (Storage::fake) untuk disk "public".
 */
class ImageHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Fake: disk berisi direktori sementara
    }

    /** IMG-TC-01: path kosong (null dan string kosong) -> placeholder. */
    public function test_returns_placeholder_when_path_is_empty(): void
    {
        // Arrange
        $expected = asset('images/default-placeholder.png');

        // Act
        $fromNull = \App\Helpers\getImageUrl(null);
        $fromEmpty = \App\Helpers\getImageUrl('');

        // Assert
        $this->assertSame($expected, $fromNull);
        $this->assertSame($expected, $fromEmpty);
    }

    /** IMG-TC-02: path sudah berupa URL -> dikembalikan apa adanya. */
    public function test_returns_the_same_value_when_path_is_a_url(): void
    {
        // Arrange
        $url = 'https://example.com/foto/kamar.jpg';

        // Act
        $result = \App\Helpers\getImageUrl($url);

        // Assert
        $this->assertSame($url, $result);
    }

    /** IMG-TC-03: file ada di disk public -> URL storage. */
    public function test_returns_storage_url_when_file_exists_on_public_disk(): void
    {
        // Arrange
        Storage::disk('public')->put('avatars/budi.jpg', 'isi-gambar');

        // Act
        $result = \App\Helpers\getImageUrl('avatars/budi.jpg');

        // Assert
        $this->assertSame(asset('storage/avatars/budi.jpg'), $result);
    }

    /** IMG-TC-04: file tidak di disk public tetapi ada di folder public/. */
    public function test_returns_public_url_when_file_exists_in_public_folder(): void
    {
        // Arrange
        $name = '__unit_test_image_' . uniqid() . '.png';
        $fullPath = public_path($name);
        file_put_contents($fullPath, 'isi-gambar');

        // Act
        try {
            $result = \App\Helpers\getImageUrl('/' . $name); // garis miring depan harus dibersihkan
        } finally {
            @unlink($fullPath);
        }

        // Assert
        $this->assertSame(asset($name), $result);
    }

    /** IMG-TC-05: file tidak ditemukan di mana pun -> fallback URL storage. */
    public function test_falls_back_to_storage_url_when_file_is_not_found(): void
    {
        // Arrange
        $path = '/uploads/tidak-ada.jpg';

        // Act
        $result = \App\Helpers\getImageUrl($path);

        // Assert
        $this->assertSame(asset('storage/uploads/tidak-ada.jpg'), $result);
    }
}
