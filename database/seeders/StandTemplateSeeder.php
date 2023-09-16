<?php

namespace Database\Seeders;

use App\Models\Congregation;
use App\Models\Stand;
use App\Models\StandPublishers;
use App\Models\StandTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandTemplateSeeder extends Seeder
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

        $week_schedule = [
            1 => [8, 9, 10],
            2 => [8, 10, 11, 12],
            4 => [15, 16],
            7 => [8, 9, 10],
        ];

        $stand_templates = [
            [
                'type' => 'current',
                'week_schedule' => json_encode($week_schedule),
                'stand_id' => Stand::whereName('Moldtelecom')->first()->id,
                'congregation_id' => $congregation_id,
            ],
            [
                'type' => 'next',
                'week_schedule' => json_encode($week_schedule),
                'stand_id' => Stand::whereName('Moldtelecom')->first()->id,
                'congregation_id' => $congregation_id,
            ],
        ];

        foreach($stand_templates as $stand_template) {
            $standTemplateId = DB::table(StandTemplate::TABLE)->insertGetId($stand_template);
            DB::table(StandPublishers::TABLE)->insert([
                'stand_template_id' => $standTemplateId,
                'day' => 1,
                'time' => 8,
                'user_1' => User::orderByRaw("RAND()")->first()->id,
                'user_2' => User::orderByRaw("RAND()")->first()->id,
                'date' => now()
            ]);
            DB::table(StandPublishers::TABLE)->insert([
                'stand_template_id' => $standTemplateId,
                'day' => 1,
                'time' => 9,
                'user_1' => User::orderByRaw("RAND()")->first()->id,
                'user_2' => null,
                'date' => now()
            ]);
            DB::table(StandPublishers::TABLE)->insert([
                'stand_template_id' => $standTemplateId,
                'day' => 1,
                'time' => 10,
                'user_1' => User::orderByRaw("RAND()")->first()->id,
                'user_2' => null,
                'date' => now()
            ]);
            
            DB::table(StandPublishers::TABLE)->insert([
                'stand_template_id' => $standTemplateId,
                'day' => 2,
                'time' => 8,
                'user_1' => User::orderByRaw("RAND()")->first()->id,
                'user_2' => User::orderByRaw("RAND()")->first()->id,
                'date' => now()
            ]);
        }
    }
}
