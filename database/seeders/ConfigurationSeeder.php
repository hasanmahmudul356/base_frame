<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'key' => 'app_name',
                'type' => 'text',
                'display_name' => 'Application name',
                'value' => 'RBAC template',
            ],
            [
                'key' => 'logo',
                'type' => 'file',
                'display_name' => 'Logo',
                'value' => 'images/logo.jpg',
            ],
        ];

        foreach ($array as $each){
            $config = Configuration::where('key', $each['key'])->first();

            if (!$config){
                $configuration = new Configuration();
                $configuration->fill($each);
                $configuration->save();
            }
        }

    }
}
