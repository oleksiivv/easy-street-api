<?php

namespace App\Console\Commands;

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Role;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    public function handle(RoleRepository $roleRepository, AdministratorRepository $administratorRepository, UserRepository $userRepository)
    {
        DB::table('administrators_to_moderators_pivot')->truncate();

        $user = $userRepository->findBy([
            'email' => env('ADMIN_EMAIL'),
        ]);

        $user->role_id = $roleRepository->findByName(Role::ROLE_ADMIN);

        $administratorRepository->createOrUpdate($user->email, $user->email, $user->id);

        return Command::SUCCESS;
    }
}
