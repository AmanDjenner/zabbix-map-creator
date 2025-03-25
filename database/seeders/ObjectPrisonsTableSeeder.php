<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ObjectPrisonsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $institutions = range(1, 21); // ID-urile instituțiilor de la 1 la 21

        foreach ($institutions as $institutionId) {
            // Generăm data între 01.01.2025 și 13.03.2025
            $startDate = Carbon::create(2025, 1, 1);
            $endDate = Carbon::create(2025, 3, 13);
            $randomDate = $faker->dateTimeBetween($startDate, $endDate);

            // Ora curentă
            $currentTime = Carbon::now()->format('H:i');

            // Numele instituției
            $institutionName = "Instituția $institutionId";

            // Textul începe cu "Instituția X, la ora HH:MM," urmat de text random (100-500 caractere)
            $randomText = $faker->realTextBetween(100, 500);
            $objText = "$institutionName, la ora $currentTime, $randomText";

            // Alegem random între "Depistare" și "Contracarare"
            $evenimentOptions = ['Depistare', 'Contracarare'];
            $eveniment = $faker->randomElement($evenimentOptions);

            DB::table('object_prisons')->insert([
                'data' => $randomDate,
                'id_institution' => $institutionId,
                'eveniment' => $eveniment, // Random: "Depistare" sau "Contracarare"
                'obj_text' => $objText,
                'created_by' => $faker->numberBetween(1, 2), // Ajustat la 1-2
                'updated_by' => 1, // Fix 1
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}