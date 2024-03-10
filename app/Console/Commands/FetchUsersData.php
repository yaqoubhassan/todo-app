<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class FetchUsersData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-users-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $response = Http::get(config('json_faker.base_url') . 'users');

            if ($response->successful()) {
                $users = $response->json();

                foreach ($users as $user) {
                    User::firstOrCreate(
                        ['email' => $user['email']],
                        [
                            'name' => $user['name'],
                            'password' => Hash::make('password'),
                            'email_verified_at' => now()
                        ]
                    );
                }
                $this->info('Users data fecthed and stored successfully');
            } else {
                $this->error('Failed to fetch users data from JSON Faker API.');
            }
        } catch (\Exception $e) {
            $this->error('An error occurred while fetching todo data: ' . $e->getMessage());
        }
    }
}
