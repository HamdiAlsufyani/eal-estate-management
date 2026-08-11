<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Apartment' => 'شقة',
            'Villa' => 'فيلا',
            'House' => 'منزل',
            'Land' => 'أرض',
            'Office' => 'مكتب',
            'Shop' => 'محل تجاري',
            'Warehouse' => 'مستودع',
            'Building' => 'مبنى',
            'Farm' => 'مزرعة',
            'Other' => 'أخرى',
        ];

        foreach ($types as $nameEn => $nameAr) {
            $propertyType = PropertyType::firstOrCreate(
                ['slug' => Str::slug($nameEn)],
                ['name' => $nameEn, 'name_en' => $nameEn, 'name_ar' => $nameAr, 'is_active' => true]
            );

            if (! $propertyType->name_ar) {
                $propertyType->update(['name_en' => $nameEn, 'name_ar' => $nameAr]);
            }
        }
    }
}
