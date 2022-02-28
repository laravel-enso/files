<?php

namespace LaravelEnso\Files\Database\Seeders;

use Illuminate\Database\Seeder;
use LaravelEnso\Avatars\Models\Avatar;
use LaravelEnso\DataExport\Models\Export;
use LaravelEnso\DataImport\Models\Import;
use LaravelEnso\DataImport\Models\RejectedImport;
use LaravelEnso\Documents\Http\Resources\Document;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Files\Models\Upload;
use LaravelEnso\HowTo\Models\Poster;
use LaravelEnso\HowTo\Models\Video;
use LaravelEnso\Products\Models\Picture;
use LaravelEnso\Webshop\Models\Brand;
use LaravelEnso\Webshop\Models\CarouselSlide;

class TypeSeeder extends Seeder
{
    public function run()
    {
        $this->avatars()
            ->recent()
            ->favorites()
            ->uploads()
            ->exports()
            ->imports()
            ->rejectedImports()
            ->documents()
            ->productPictures()
            ->brands()
            ->carouselSlides()
            ->howToPosters()
            ->howToVideos();
    }

    private function avatars(): self
    {
        Type::factory()->model(Avatar::class);

        return $this;
    }

    private function recent(): self
    {
        Type::factory()->create([
            'name' => 'Recent',
            'folder' => null,
            'model' => null,
            'icon' => 'folder-plus',
            'endpoint' => 'recent',
            'description' => 'User Favorites',
            'is_browsable' => true,
            'is_system' => true,
        ]);

        return $this;
    }

    private function favorites(): self
    {
        Type::factory()->create([
            'name' => 'Favorites',
            'folder' => null,
            'model' => null,
            'icon' => 'star',
            'endpoint' => 'favorites',
            'description' => 'User Favorites',
            'is_browsable' => true,
            'is_system' => true,
        ]);

        return $this;
    }

    private function uploads(): self
    {
        Type::factory()->model(Upload::class)->create([
            'name' => 'Uploads',
            'icon' => 'folder-upload',
            'is_browsable' => true,
            'is_system' => false,
        ]);

        return $this;
    }

    private function exports(): self
    {
        Type::factory()->model(Export::class)->create([
            'icon' => 'file-export',
            'is_browsable' => true,
            'is_system' => false,
        ]);

        return $this;
    }

    private function imports(): self
    {
        if (class_exists(Import::class)) {
            Type::factory()->model(Import::class)->create([
                'icon' => 'file-import',
                'is_browsable' => true,
            ]);
        }

        return $this;
    }

    private function rejectedImports(): self
    {
        if (class_exists(RejectedImport::class)) {
            Type::factory()->model(RejectedImport::class)->create([
                'icon' => 'exclamation-triangle',
                'is_browsable' => true,
            ]);
        }

        return $this;
    }

    private function documents(): self
    {
        if (class_exists(Document::class)) {
            Type::factory()->model(Document::class)->create([
                'icon' => 'file-contract',
                'is_browsable' => true,
            ]);
        }

        return $this;
    }

    private function productPictures(): self
    {
        if (class_exists(Picture::class)) {
            Type::factory()->model(Picture::class)->create([
                'icon' => 'image',
                'is_browsable' => true,
                'folder' => 'productPictures',
            ]);
        }

        return $this;
    }

    private function brands(): self
    {
        if (class_exists(Brand::class)) {
            Type::factory()->model(Brand::class)->create([
                'is_browsable' => true,
                'icon' => 'copyright',
            ]);
        }

        return $this;
    }

    private function carouselSlides(): self
    {
        if (class_exists(CarouselSlide::class)) {
            Type::factory()->model(CarouselSlide::class)->create();
        }

        return $this;
    }

    private function howToPosters(): self
    {
        if (class_exists(Poster::class)) {
            Type::factory()->model(Poster::class)->create();
        }

        return $this;
    }

    private function howToVideos(): self
    {
        if (class_exists(Video::class)) {
            Type::factory()->model(Video::class)->create();
        }

        return $this;
    }
}
