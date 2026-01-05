<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateNewUser extends Command
{
    protected $signature = 'app:create-new-user {--name=} {--email=}';

    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');

        if ($name && $email) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            $token = $user->createToken('manual-token')->plainTextToken;

            $this->info("User created. API Token: $token");
        }
    }
}
