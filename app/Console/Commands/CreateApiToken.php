<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildstatus:create-api-token {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an API token for the user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('username', $this->argument('username'))->first();
        if (!$user) {
            $this->error('User not found');
            return Command::FAILURE;
        }
        $token = $user->createToken('api-token')->plainTextToken;
        $this->info('API token created: ' . $token);
        return Command::SUCCESS;
    }
}
