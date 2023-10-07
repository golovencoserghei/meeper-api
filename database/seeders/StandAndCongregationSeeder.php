<?php

namespace Database\Seeders;

use App\Models\Congregation;
use App\Models\Stand;
use App\Models\StandRecords;
use App\Models\StandTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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

        $standTemplates = [
            [
                'congregation_id' => $congregation_id,
                'stand_id' => 1,
                'activation_at' => '4-80:00', // thursday (4 week day) at 8 o'clock
                'week_schedule' => json_encode([
                    '1' => [ // current week of template
                        '1' => [ // monday
                            '08', '09', 10, 11, 12 // hours when stand stays
                        ],
                        '2' => [ // tuesday
                            10, 11, 12, 16, 17, 18
                        ],
                        '4' => [ // thursday
                            '08', '09', 10
                        ],
                    ],
                    '2' => [ // next week of template
                        '1' => [ // monday
                            '08', '09', 10, 11, 12 // hours when stand stays
                        ],
                        '2' => [ // tuesday
                            10, 11, 12, 16, 17, 18
                        ],
                        '5' => [ // friday
                            '08', '09', 10
                        ],
                    ],
                ]),
            ],
            [
                'congregation_id' => $congregation_id,
                'stand_id' => 2,
                'activation_at' => '7-23:59', // sunday (7 week day) at the end of a day
                'week_schedule' => json_encode([
                    '1' => [ // current week of template
                        '2' => [ // monday
                            '08', '09', 10, 11, 12 // hours when stand stays
                        ],
                        '4' => [ // tuesday
                            10, 11, 12, 16, 17, 18
                        ],
                        '5' => [ // thursday
                            '08', '09', 10
                        ],
                    ],
                    '2' => [ // next week of template
                        '2' => [ // monday
                            '08', '09', 10, 11, 12 // hours when stand stays
                        ],
                        '4' => [ // tuesday
                            10, 11, 12, 16, 17, 18
                        ],
                        '5' => [ // friday
                            '08', '09', 10
                        ],
                    ],
                ]),
            ]
        ];

        foreach ($standTemplates as $standTemplate) {
            StandTemplate::query()->insert($standTemplate);
        }

        $standRecords = [
            // Monday full day scheduled for first template and first stand
            [
                'stand_template_id' => 1,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 08:00:00',
                'publishers' => [1]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 08:00:00',
                'publishers' => [2]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 9 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 09:00:00',
                'publishers' => [5, 6]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 10 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 10:00:00',
                'publishers' => [5, 6]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 11 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 11:00:00',
                'publishers' => [7, 8]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 12:00:00',
                'publishers' => [7, 8]
            ],

            // Monday 8 o'clock for second template and second stand
            [
                'stand_template_id' => 2,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 08:00:00',
                'publishers' => [3, 4]
            ],
            [
                'stand_template_id' => 2,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::MONDAY)->format('d-m-Y') . ' 09:00:00',
                'publishers' => [3, 4]
            ],


            // Tuesday 10 o'clock for first template
            [
                'stand_template_id' => 1,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::TUESDAY)->format('d-m-Y') . ' 10:00:00',
                'publishers' => [3]
            ],
            [
                'stand_template_id' => 1,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::TUESDAY)->format('d-m-Y') . ' 11:00:00',
                'publishers' => [4]
            ],


            // Thursday 10 o'clock for second template
            [
                'stand_template_id' => 2,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::THURSDAY)->format('d-m-Y') . ' 10:00:00',
                'publishers' => [3]
            ],
            [
                'stand_template_id' => 2,
                // register on Monday at 8 o'clock
                'date_time' => now()->startOfWeek(Carbon::THURSDAY)->format('d-m-Y') . ' 11:00:00',
                'publishers' => [4]
            ],
        ];

        foreach ($standRecords as $standRecord) {
            $standRecordCreated = StandRecords::query()->create([
                'stand_template_id' => $standRecord['stand_template_id'],
                'day' => Carbon::parse($standRecord['date_time'])->dayOfWeekIso,
                'date_time' => Carbon::parse($standRecord['date_time'])->format('Y-m-d H:i:s'),
            ]);

            $standRecordCreated->publishers()->attach($standRecord['publishers']);

        }
    }
}
