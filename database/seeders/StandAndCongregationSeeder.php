<?php

namespace Database\Seeders;

use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandAndCongregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $congregation = [
            'name' => 'Chisinau',
        ];

        $congregation_id = DB::table(Congregation::TABLE)->insertGetId($congregation);

        $stands = [
            [
                'congregation_id' => $congregation_id,
                'name' => 'Moldtelecom',
                'location' => 'str Bulevardul Ștefan cel Mare și Sfînt',
            ],

            [
                'congregation_id' => $congregation_id,
                'name' => 'McDonalds',
                'location' => 'str Bulevardul Ștefan cel Mare și Sfînt',
            ],
        ];

        foreach($stands as $stand) {
            DB::table(Stand::TABLE)->insert($stand);
        }
    }
}
