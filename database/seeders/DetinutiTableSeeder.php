<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DetinutiTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Verificăm dacă tabela institutions este populată
      

        $institutions = range(1, 21); // ID-urile instituțiilor de la 1 la 21

        // Definim intervalul de date
        $startDate = Carbon::create(2024, 1, 1); // 01.01.2024
        $endDate = Carbon::create(2025, 3, 3);   // 03.03.2025

        // Iterăm prin fiecare zi din interval
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            foreach ($institutions as $institutionId) {
                // Total random între 500 și 1000
                $total = $faker->numberBetween(500, 1000);

                // Valorile fixe specificate
                $pretrial_detention = 5;
                $initial_conditions = 3;
                $life = 1;
                $female = 20;
                $minors = 5;
                $open_sector = 10;
                $no_escort = 15;
                $monitoring_bracelets = 2;
                $hunger_strike = 1;
                $disciplinary_insulator = 3;
                $admitted_to_hospitals = 4;
                $employed_ip_in_hospitals = 2;
                $employed_dds_in_hospitals = 1;
                $work_outside = 10;
                $employed_ip_work_outside = 5;

                // Valori random între 1 și 5 pentru câmpurile nespecificate
                $in_search = $faker->numberBetween(1, 5);

                // Calculăm suma tuturor câmpurilor (excluzând real_inmates)
                $sumOthers = $in_search + $pretrial_detention + $initial_conditions + $life +
                             $female + $minors + $open_sector + $no_escort + $monitoring_bracelets +
                             $hunger_strike + $disciplinary_insulator + $admitted_to_hospitals +
                             $employed_ip_in_hospitals + $employed_dds_in_hospitals + $work_outside +
                             $employed_ip_work_outside;

                // Real_inmates = total - suma celorlalte câmpuri
                $real_inmates = $total - $sumOthers;

                // Asigurăm că real_inmates nu devine negativ
                if ($real_inmates < 0) {
                    $real_inmates = 0;
                }

                // Inserăm datele
                DB::table('detinuti')->insert([
                    'data' => $currentDate->toDateString(), // Data curentă din iterație
                    'id_institution' => $institutionId,
                    'total' => $total,
                    'real_inmates' => $real_inmates,
                    'in_search' => $in_search,
                    'pretrial_detention' => $pretrial_detention,
                    'initial_conditions' => $initial_conditions,
                    'life' => $life,
                    'female' => $female,
                    'minors' => $minors,
                    'open_sector' => $open_sector,
                    'no_escort' => $no_escort,
                    'monitoring_bracelets' => $monitoring_bracelets,
                    'hunger_strike' => $hunger_strike,
                    'disciplinary_insulator' => $disciplinary_insulator,
                    'admitted_to_hospitals' => $admitted_to_hospitals,
                    'employed_ip_in_hospitals' => $employed_ip_in_hospitals,
                    'employed_dds_in_hospitals' => $employed_dds_in_hospitals,
                    'work_outside' => $work_outside,
                    'employed_ip_work_outside' => $employed_ip_work_outside,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $currentDate->addDay(); // Trecem la ziua următoare
        }
    }
}