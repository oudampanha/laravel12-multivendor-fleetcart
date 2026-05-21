<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\MetaData;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandMetaDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_translated_attributes_are_available_via_plain_model_access(): void
    {
        $brand = Brand::create([
            'slug' => 'brand-translation-attribute-test',
            'is_active' => true,
        ]);

        $brand->setTranslation('name', 'Translated Brand Name');
        $brand->setTranslation('description', 'Translated Brand Description');

        $freshBrand = $brand->fresh();

        $this->assertSame('Translated Brand Name', $freshBrand->name);
        $this->assertSame('Translated Brand Description', $freshBrand->description);
        $this->assertSame('Translated Brand Name', $freshBrand->getTranslation('name'));
        $this->assertSame('Translated Brand Description', $freshBrand->getTranslation('description'));
    }

    public function test_brand_meta_fields_share_one_metadata_record(): void
    {
        $brand = Brand::create([
            'slug' => 'brand-meta-test',
            'is_active' => true,
        ]);

        $brand->setMeta('meta_title', 'Audit Meta Title');
        $brand->setMeta('meta_description', 'Audit Meta Description');

        $freshBrand = $brand->fresh();
        $metaData = MetaData::forEntity(Brand::class, $brand->id)->firstOrFail();

        $this->assertSame('Audit Meta Title', $freshBrand->getMeta('meta_title'));
        $this->assertSame('Audit Meta Description', $freshBrand->getMeta('meta_description'));
        $this->assertSame(1, MetaData::forEntity(Brand::class, $brand->id)->count());
        $this->assertEqualsCanonicalizing(
            ['description', 'title'],
            Translation::forModel(MetaData::class, $metaData->id)->pluck('field')->all()
        );
    }
}
