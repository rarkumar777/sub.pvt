<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class UpdateHotelImagesSeeder extends Seeder
{
    public function run()
    {
        $hotels = Service::where('category', 403)->get();
        
        $images = [
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1551882547-ff40c0d5e9af?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1542314831-c6a4d1409b1e?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1498503182468-3b51cbb6cb24?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1549294413-26f195200c16?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1518602164578-cd0074062767?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=500&h=300&fit=crop',
            'https://images.unsplash.com/photo-1560662105-57f8ad6ae2d1?w=500&h=300&fit=crop',
        ];

        foreach($hotels as $index => $hotel) {
            // Assign a stable but varied image from the array based on ID
            $img = $images[$hotel->id % count($images)];
            
            // Format it correctly as a JSON array if required by the app,
            // but the app handles plain strings or json arrays for images.
            // Let's use a JSON array since that's what the app parses:
            $hotel->image = json_encode([$img]);
            $hotel->save();
        }
    }
}
