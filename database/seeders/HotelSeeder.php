<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class HotelSeeder extends Seeder
{
    public function run()
    {
        // Delete existing accommodations
        Service::where('category', 403)->delete();

        $hotels = array (
          0 => 
          array (
            'name' => 'Dānā',
            'loc' => 'Dānā',
            'flags' => 'es,it,de,fr',
          ),
          1 => 
          array (
            'name' => 'Commodore hotel jerusalem',
            'loc' => 'Jerusalem',
            'flags' => 'it,fr',
          ),
          2 => 
          array (
            'name' => 'Grand East Dead sea',
            'loc' => 'Dead Sea',
            'flags' => 'fr,it',
          ),
          3 => 
          array (
            'name' => 'Wadi Rum Night Luxury Camp',
            'loc' => 'Wadi Rum',
            'flags' => 'fr,es,it,de',
          ),
          4 => 
          array (
            'name' => 'Ra \'ad',
            'loc' => 'Aqaba',
            'flags' => 'es,de',
          ),
          5 => 
          array (
            'name' => 'Retaj Hotel',
            'loc' => 'Amman Governorate',
            'flags' => 'fr,es,it,de',
          ),
          6 => 
          array (
            'name' => 'Tetra Tree',
            'loc' => 'Petra',
            'flags' => 'fr,es,it,de',
          ),
          7 => 
          array (
            'name' => 'Weekend',
            'loc' => 'Muscat',
            'flags' => 'es,de',
          ),
          8 => 
          array (
            'name' => 'Ramada Dead Sea Hôtel',
            'loc' => 'Dead Sea',
            'flags' => 'fr,es,it,de',
          ),
          9 => 
          array (
            'name' => 'My Hotel',
            'loc' => 'Aqaba',
            'flags' => 'fr,es,it,de',
          ),
          10 => 
          array (
            'name' => '7th Star Hotel Suites',
            'loc' => 'Amman',
            'flags' => 'fr',
          ),
          11 => 
          array (
            'name' => 'Memories Aicha Luxury Camp',
            'loc' => 'Wadi Rum',
            'flags' => 'it,fr',
          ),
          12 => 
          array (
            'name' => 'In the inhabitants',
            'loc' => 'Petra',
            'flags' => 'fr,it',
          ),
          13 => 
          array (
            'name' => 'Mövenpick Resort & Spa Dead Sea',
            'loc' => 'Dead Sea',
            'flags' => 'fr,it',
          ),
          14 => 
          array (
            'name' => 'Falcon camp camp',
            'loc' => 'Wadi Ibn Hammad',
            'flags' => 'fr',
          ),
          15 => 
          array (
            'name' => 'Mövenpick Resort & Residences Aqaba',
            'loc' => 'Aqaba',
            'flags' => 'fr,it',
          ),
          16 => 
          array (
            'name' => 'Amman Rotana Hotel',
            'loc' => 'Amman',
            'flags' => 'it,fr',
          ),
          17 => 
          array (
            'name' => 'homestay',
            'loc' => 'Wadi Ibn Hammad',
            'flags' => 'fr',
          ),
          18 => 
          array (
            'name' => 'at the inhabitant',
            'loc' => 'Petra',
            'flags' => 'fr',
          ),
          19 => 
          array (
            'name' => 'Dana Tower Hotel',
            'loc' => 'Dānā',
            'flags' => 'es,de,fr,it',
          ),
          20 => 
          array (
            'name' => 'Amman Sadeen Hotel',
            'loc' => 'Amman',
            'flags' => 'es,de,fr',
          ),
          21 => 
          array (
            'name' => 'Homestay in Petra',
            'loc' => 'Petra',
            'flags' => 'fr,it',
          ),
          22 => 
          array (
            'name' => 'Al Anbat',
            'loc' => 'Petra',
            'flags' => 'es,de,fr',
          ),
          23 => 
          array (
            'name' => 'Beyond Wadi Rum Camp',
            'loc' => 'Wadi Rum',
            'flags' => 'it,fr',
          ),
          24 => 
          array (
            'name' => 'Gallery Guest House',
            'loc' => 'Amman',
            'flags' => 'fr,it',
          ),
          25 => 
          array (
            'name' => 'Bivouac',
            'loc' => 'Wadi Rum',
            'flags' => 'fr,it',
          ),
          26 => 
          array (
            'name' => 'Al Raad Hotel',
            'loc' => 'Aqaba',
            'flags' => 'fr,it',
          ),
          27 => 
          array (
            'name' => 'Oh Beach!',
            'loc' => 'Dead Sea',
            'flags' => 'fr,it',
          ),
          28 => 
          array (
            'name' => 'The RN Hotel',
            'loc' => 'Petra',
            'desc' => 'This hotel located in Petra,',
            'flags' => '',
          ),
          29 => 
          array (
            'name' => 'Bedouin Garden Village',
            'loc' => 'Aqaba',
            'flags' => 'it,fr',
          ),
          30 => 
          array (
            'name' => 'Petra Bubble Luxotel',
            'loc' => 'Petra',
            'desc' => 'Situato a Wadi Musa, nel cuore del Piccolo Petra Triclinium, il Petra Bubble Luxotel offre sistemazio...',
            'flags' => 'it',
          ),
          31 => 
          array (
            'name' => 'Ma\'in Hot Springs Resort & Spa',
            'loc' => 'Madaba',
            'desc' => 'This Oasis-style resort features an outdoor pool and a spa located directly under a hot spring waterf...',
            'flags' => 'fr,it',
          ),
          32 => 
          array (
            'name' => 'Hassan Zawaideh Camp',
            'loc' => 'Wadi Rum',
            'flags' => 'fr,it',
          ),
          33 => 
          array (
            'name' => 'La Maison Hotel',
            'loc' => 'Petra',
            'flags' => 'es,de,fr',
          ),
          34 => 
          array (
            'name' => 'Town Season Hotel',
            'loc' => 'Petra',
            'flags' => 'fr,it',
          ),
          35 => 
          array (
            'name' => 'Dana Hotel',
            'loc' => 'Dānā',
            'flags' => 'es,de,fr',
          ),
          36 => 
          array (
            'name' => 'Rafi Hotel',
            'loc' => 'Amman',
            'flags' => 'es,de,fr',
          ),
        );

        foreach ($hotels as $h) {
            Service::create([
                'category' => 403,
                'description' => $h['name'],
                'arrival' => $h['loc'],
                'notes' => $h['desc'] ?? '',
                'website' => $h['flags'],
                'cost' => 0,
                'vender' => 0,
                'country' => 123
            ]);
        }
    }
}
