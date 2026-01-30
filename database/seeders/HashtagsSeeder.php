<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HashtagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hashtags = [
            // Wedding
            ['name' => 'BangladeshiWedding', 'category' => 'Wedding', 'is_featured' => true],
            ['name' => 'WeddingPhotography', 'category' => 'Wedding', 'is_featured' => true],
            ['name' => 'BrideAndGroom', 'category' => 'Wedding', 'is_featured' => false],
            ['name' => 'WeddingInspiration', 'category' => 'Wedding', 'is_featured' => false],
            ['name' => 'DesiWedding', 'category' => 'Wedding', 'is_featured' => false],
            
            // Portrait
            ['name' => 'PortraitPhotography', 'category' => 'Portrait', 'is_featured' => true],
            ['name' => 'PortraitMode', 'category' => 'Portrait', 'is_featured' => false],
            ['name' => 'PeoplePhotography', 'category' => 'Portrait', 'is_featured' => false],
            ['name' => 'FacePortrait', 'category' => 'Portrait', 'is_featured' => false],
            
            // Event
            ['name' => 'EventPhotography', 'category' => 'Event', 'is_featured' => true],
            ['name' => 'CorporateEvent', 'category' => 'Event', 'is_featured' => false],
            ['name' => 'PartyPhotography', 'category' => 'Event', 'is_featured' => false],
            ['name' => 'BirthdayParty', 'category' => 'Event', 'is_featured' => false],
            
            // Fashion
            ['name' => 'FashionPhotography', 'category' => 'Fashion', 'is_featured' => true],
            ['name' => 'FashionShoot', 'category' => 'Fashion', 'is_featured' => false],
            ['name' => 'ModelPhotography', 'category' => 'Fashion', 'is_featured' => false],
            ['name' => 'BangladeshiFashion', 'category' => 'Fashion', 'is_featured' => false],
            
            // Product
            ['name' => 'ProductPhotography', 'category' => 'Product', 'is_featured' => true],
            ['name' => 'Ecommerce', 'category' => 'Product', 'is_featured' => false],
            ['name' => 'ProductShoot', 'category' => 'Product', 'is_featured' => false],
            ['name' => 'CommercialPhotography', 'category' => 'Product', 'is_featured' => false],
            
            // Food
            ['name' => 'FoodPhotography', 'category' => 'Food', 'is_featured' => true],
            ['name' => 'FoodPorn', 'category' => 'Food', 'is_featured' => false],
            ['name' => 'BangladeshiFood', 'category' => 'Food', 'is_featured' => false],
            ['name' => 'RestaurantPhotography', 'category' => 'Food', 'is_featured' => false],
            
            // Landscape
            ['name' => 'LandscapePhotography', 'category' => 'Landscape', 'is_featured' => true],
            ['name' => 'NaturePhotography', 'category' => 'Landscape', 'is_featured' => false],
            ['name' => 'BeautifulBangladesh', 'category' => 'Landscape', 'is_featured' => true],
            ['name' => 'Sunset', 'category' => 'Landscape', 'is_featured' => false],
            
            // Architecture
            ['name' => 'ArchitecturePhotography', 'category' => 'Architecture', 'is_featured' => true],
            ['name' => 'BuildingPhotography', 'category' => 'Architecture', 'is_featured' => false],
            ['name' => 'UrbanPhotography', 'category' => 'Architecture', 'is_featured' => false],
            ['name' => 'DhakaCity', 'category' => 'Architecture', 'is_featured' => false],
            
            // Nature
            ['name' => 'NatureLovers', 'category' => 'Nature', 'is_featured' => true],
            ['name' => 'Wildlife', 'category' => 'Nature', 'is_featured' => false],
            ['name' => 'NaturalBeauty', 'category' => 'Nature', 'is_featured' => false],
            ['name' => 'WildlifePhotography', 'category' => 'Nature', 'is_featured' => false],
            
            // Street
            ['name' => 'StreetPhotography', 'category' => 'Street', 'is_featured' => true],
            ['name' => 'StreetLife', 'category' => 'Street', 'is_featured' => false],
            ['name' => 'Documentary', 'category' => 'Street', 'is_featured' => false],
            ['name' => 'DhakaStreets', 'category' => 'Street', 'is_featured' => false],
            
            // Sports
            ['name' => 'SportsPhotography', 'category' => 'Sports', 'is_featured' => true],
            ['name' => 'ActionPhotography', 'category' => 'Sports', 'is_featured' => false],
            ['name' => 'Cricket', 'category' => 'Sports', 'is_featured' => false],
            ['name' => 'Football', 'category' => 'Sports', 'is_featured' => false],
            
            // Baby
            ['name' => 'NewbornPhotography', 'category' => 'Baby', 'is_featured' => true],
            ['name' => 'BabyPhotography', 'category' => 'Baby', 'is_featured' => true],
            ['name' => 'NewbornShoot', 'category' => 'Baby', 'is_featured' => false],
            ['name' => 'BabyPortrait', 'category' => 'Baby', 'is_featured' => false],
            
            // Maternity
            ['name' => 'MaternityPhotography', 'category' => 'Maternity', 'is_featured' => true],
            ['name' => 'PregnancyPhotography', 'category' => 'Maternity', 'is_featured' => false],
            ['name' => 'MaternityShoot', 'category' => 'Maternity', 'is_featured' => false],
            ['name' => 'MomToBe', 'category' => 'Maternity', 'is_featured' => false],
            
            // Commercial
            ['name' => 'CommercialPhotography', 'category' => 'Commercial', 'is_featured' => true],
            ['name' => 'AdvertisingPhotography', 'category' => 'Commercial', 'is_featured' => false],
            ['name' => 'BrandPhotography', 'category' => 'Commercial', 'is_featured' => false],
            ['name' => 'Marketing', 'category' => 'Commercial', 'is_featured' => false],
        ];

        foreach ($hashtags as $hashtagData) {
            $category = \App\Models\PhotoCategory::where('name', $hashtagData['category'])->first();
            
            \App\Models\Hashtag::updateOrCreate(
                ['name' => $hashtagData['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($hashtagData['name']),
                    'category_id' => $category ? $category->id : null,
                    'is_featured' => $hashtagData['is_featured'],
                ]
            );
        }
    }
}
