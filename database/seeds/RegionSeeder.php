<?php

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
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
        		'name' => 'Miền Bắc',
                'slug' => 'mien-bac'
        	),
        	array(
        		'name' => 'Miền Trung',
                'slug' => 'mien-trung'
        	),
        	array(
        		'name' => 'Miền Nam',
                'slug' => 'mien-nam'
        	),
        );
        Region::insert($data);
    }
}
