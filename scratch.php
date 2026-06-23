<?php
$hotels = [
    ["name" => "Dānā", "loc" => "Dānā", "flags" => "es,it,de,fr"],
    ["name" => "Commodore hotel jerusalem", "loc" => "Jerusalem", "flags" => "it,fr"],
    ["name" => "Grand East Dead sea", "loc" => "Dead Sea", "flags" => "fr,it"],
    ["name" => "Wadi Rum Night Luxury Camp", "loc" => "Wadi Rum", "flags" => "fr,es,it,de"],
    ["name" => "Ra 'ad", "loc" => "Aqaba", "flags" => "es,de"],
    ["name" => "Retaj Hotel", "loc" => "Amman Governorate", "flags" => "fr,es,it,de"],
    ["name" => "Tetra Tree", "loc" => "Petra", "flags" => "fr,es,it,de"],
    ["name" => "Weekend", "loc" => "Muscat", "flags" => "es,de"],
    ["name" => "Ramada Dead Sea Hôtel", "loc" => "Dead Sea", "flags" => "fr,es,it,de"],
    ["name" => "My Hotel", "loc" => "Aqaba", "flags" => "fr,es,it,de"],
    ["name" => "7th Star Hotel Suites", "loc" => "Amman", "flags" => "fr"],
    ["name" => "Memories Aicha Luxury Camp", "loc" => "Wadi Rum", "flags" => "it,fr"],
    ["name" => "In the inhabitants", "loc" => "Petra", "flags" => "fr,it"],
    ["name" => "Mövenpick Resort & Spa Dead Sea", "loc" => "Dead Sea", "flags" => "fr,it"],
    ["name" => "Falcon camp camp", "loc" => "Wadi Ibn Hammad", "flags" => "fr"],
    ["name" => "Mövenpick Resort & Residences Aqaba", "loc" => "Aqaba", "flags" => "fr,it"],
    ["name" => "Amman Rotana Hotel", "loc" => "Amman", "flags" => "it,fr"],
    ["name" => "homestay", "loc" => "Wadi Ibn Hammad", "flags" => "fr"],
    ["name" => "at the inhabitant", "loc" => "Petra", "flags" => "fr"],
    ["name" => "Dana Tower Hotel", "loc" => "Dānā", "flags" => "es,de,fr,it"],
    ["name" => "Amman Sadeen Hotel", "loc" => "Amman", "flags" => "es,de,fr"],
    ["name" => "Homestay in Petra", "loc" => "Petra", "flags" => "fr,it"],
    ["name" => "Al Anbat", "loc" => "Petra", "flags" => "es,de,fr"],
    ["name" => "Beyond Wadi Rum Camp", "loc" => "Wadi Rum", "flags" => "it,fr"],
    ["name" => "Gallery Guest House", "loc" => "Amman", "flags" => "fr,it"],
    ["name" => "Bivouac", "loc" => "Wadi Rum", "flags" => "fr,it"],
    ["name" => "Al Raad Hotel", "loc" => "Aqaba", "flags" => "fr,it"],
    ["name" => "Oh Beach!", "loc" => "Dead Sea", "flags" => "fr,it"],
    ["name" => "The RN Hotel", "loc" => "Petra", "desc" => "This hotel located in Petra,", "flags" => ""],
    ["name" => "Bedouin Garden Village", "loc" => "Aqaba", "flags" => "it,fr"],
    ["name" => "Petra Bubble Luxotel", "loc" => "Petra", "desc" => "Situato a Wadi Musa, nel cuore del Piccolo Petra Triclinium, il Petra Bubble Luxotel offre sistemazio...", "flags" => "it"],
    ["name" => "Ma'in Hot Springs Resort & Spa", "loc" => "Madaba", "desc" => "This Oasis-style resort features an outdoor pool and a spa located directly under a hot spring waterf...", "flags" => "fr,it"],
    ["name" => "Hassan Zawaideh Camp", "loc" => "Wadi Rum", "flags" => "fr,it"],
    ["name" => "La Maison Hotel", "loc" => "Petra", "flags" => "es,de,fr"],
    ["name" => "Town Season Hotel", "loc" => "Petra", "flags" => "fr,it"],
    ["name" => "Dana Hotel", "loc" => "Dānā", "flags" => "es,de,fr"],
    ["name" => "Rafi Hotel", "loc" => "Amman", "flags" => "es,de,fr"]
];

$out = "<?php\n\n";
$out .= "namespace Database\Seeders;\n\n";
$out .= "use Illuminate\Database\Seeder;\n";
$out .= "use App\Models\Service;\n";
$out .= "use Illuminate\Support\Facades\DB;\n\n";
$out .= "class HotelSeeder extends Seeder\n{\n";
$out .= "    public function run()\n    {\n";
$out .= "        // Delete existing accommodations\n";
$out .= "        Service::where('category', 403)->delete();\n\n";
$out .= "        \$hotels = " . var_export($hotels, true) . ";\n\n";
$out .= "        foreach (\$hotels as \$h) {\n";
$out .= "            Service::create([\n";
$out .= "                'category' => 403,\n";
$out .= "                'description' => \$h['name'],\n";
$out .= "                'arrival' => \$h['loc'],\n";
$out .= "                'notes' => \$h['desc'] ?? '',\n";
$out .= "                'website' => \$h['flags'],\n"; // Using website to store flags hack
$out .= "                'cost' => 0,\n";
$out .= "            ]);\n";
$out .= "        }\n";
$out .= "    }\n}\n";
file_put_contents('database/seeders/HotelSeeder.php', $out);
echo "Seeder generated.\n";
