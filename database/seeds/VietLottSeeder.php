<?php

use Illuminate\Database\Seeder;
use App\Models\Vietlott;

class VietLottSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
        	array(
        		'name' => 'Power 655',
                'slug' => 'power655'
        	),
        	array(
        		'name' => 'Mega 645',
                'slug' => 'mega645'
            ),
            array(
        		'name' => 'Max 4D',
                'slug' => 'max4d'
            ),
        	array(
        		'name' => 'Max 3D',
                'slug' => 'max3d'
            ),
            array(
        		'name' => 'Keno',
                'slug' => 'keno'
        	),
        );
        Vietlott::insert($data);
    }
}
