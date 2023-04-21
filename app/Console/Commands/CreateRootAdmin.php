<?php

namespace App\Console\Commands;

use App\Repositories\AdministratorRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;

class CreateRootAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root_admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create root admin';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AdministratorRepository $administratorRepository, UserRepository $userRepository)
    {
        $user = $userRepository->findBy([
            'email' => env('ADMIN_EMAIL'),
        ]);

        $administratorRepository->createOrUpdate($user->email, $user->email, $user->id);

        return Command::SUCCESS;
    }
}
