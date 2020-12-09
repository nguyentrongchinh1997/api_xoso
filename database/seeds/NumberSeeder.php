<?php

use Illuminate\Database\Seeder;
use App\Models\Number;

class NumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 99; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }

            Number::updateOrCreate(
                [
                    'number' => $i
                ],
                [
                    'number' => $i
                ]
            );
        }
    }
}
