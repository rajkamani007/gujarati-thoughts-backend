<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'admin@quotes.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ]
        );

        $categories = [
            ['name' => 'Gujarati Quotes', 'slug' => 'gujarati-quotes'],
            ['name' => 'Motivational Quotes', 'slug' => 'motivational-quotes'],
            ['name' => 'Instagram Captions', 'slug' => 'instagram-captions'],
            ['name' => 'YouTube Quotes', 'slug' => 'youtube-quotes'],
            ['name' => 'WhatsApp Status', 'slug' => 'whatsapp-status'],
            ['name' => 'Festival Wishes', 'slug' => 'festival-wishes'],
        ];

        $sampleQuotes = [
            'જીવનમાં સફળતા માટે મહેનત અને ધૈર્ય જરૂરી છે.',
            'Believe in yourself and all that you are.',
            'Living my best life ✨ #vibes #instagood',
            'Success is not final, failure is not fatal.',
            'આજનો દિવસ ખૂબ સુંદર છે! 🌸',
            'Wishing you joy and prosperity on this festival!',
        ];

        foreach ($categories as $index => $cat) {
            $category = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'status' => true,
                ]
            );

            Quote::updateOrCreate(
                ['slug' => Str::slug('sample-' . $cat['name'])],
                [
                    'category_id' => $category->id,
                    'title' => 'Sample ' . $cat['name'],
                    'quote_text' => $sampleQuotes[$index],
                    'hashtags' => '#quotes #motivation #gujarati #status',
                    'meta_title' => 'Sample ' . $cat['name'] . ' | Quotes Hub',
                    'meta_description' => 'Beautiful sample quote from ' . $cat['name'] . ' category on Quotes Hub.',
                    'status' => true,
                    'views' => rand(50, 1000),
                ]
            );
        }
    }
}
