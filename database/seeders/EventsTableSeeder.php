<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class EventsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Verificăm dacă tabelele de referință sunt populate
        if (DB::table('institutions')->count() < 19 || DB::table('events_category')->count() < 18) {
            throw new \Exception('Tabela "institutions" trebuie să aibă cel puțin 19 înregistrări, iar "events_category" cel puțin 18.');
        }

        $institutions = range(1, 19); // ID-urile instituțiilor (limităm la 19)
        $categories = range(2, 19);   // ID-urile categoriilor (conform cu ce ai în events_category)

        // Definim intervalul de date
        $startDate = Carbon::create(2024, 1, 1); // 01.01.2024
        $endDate = Carbon::create(2025, 3, 3);   // 03.03.2025

        // Iterăm prin fiecare zi din interval
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            foreach ($institutions as $institutionId) {
                $institutionName = "Instituția $institutionId";

                // Alegem o categorie random din range(2, 19)
                $categoryId = $faker->randomElement($categories);

                // Ora curentă
                $currentTime = Carbon::now()->format('H:i');

                // Generăm textul evenimentului
                $randomText = $faker->realTextBetween(100, 500);
                $eventText = "$institutionName, la ora $currentTime, $randomText";

                // Inserăm evenimentul
                DB::table('events')->insert([
                    'data' => $currentDate->toDateString(),
                    'id_institution' => $institutionId,
                    'id_events_category' => $categoryId,
                    'persons_involved' => $faker->numberBetween(1, 10),
                    'events_text' => $eventText,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $currentDate->addDay();
        }
    }
}