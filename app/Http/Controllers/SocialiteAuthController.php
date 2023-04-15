<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\UseCases\LoginUseCase;
use App\UseCases\RegisterUseCase;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteAuthController extends Controller
{
    public function __construct(
        private LoginUseCase $loginUseCase,
        private RoleRepository $roleRepository,
    ) {
    }

    public function githubRedirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback()
    {
        try {
            $user = Socialite::driver('github')->user();

            $searchUser = User::where('github_id', $user->id)->first();
            if ($searchUser) {
                $this->loginUseCase->handle($searchUser->toArray(), false);

                return redirect('http://127.0.0.1:3000/account');
            } else {
                $password = Str::uuid()->toString();

                $user = User::create([
                    'first_name' => $user->getName(),
                    'last_name' => $user->getName(),
                    'email' => $user->email,
                    'github_id'=> $user->id,
                    'auth_type'=> 'github',
                    'password_sha' => sha1($password),
                    'email_is_confirmed' => true,
                    'role_id' => $this->getRole($user->email),
                    'icon' => $user->getAvatar(),
                ]);

                $this->loginUseCase->handle($user->toArray(), false);

                return redirect('http://127.0.0.1:3000/account');
            }

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $searchUser = User::where('google_id', $user->id)->first();
            if ($searchUser) {
                $this->loginUseCase->handle($searchUser->toArray(), false);

                return redirect('http://127.0.0.1:3000/account');
            } else {
                $password = Str::uuid()->toString();

                $user = User::create([
                    'first_name' => $user->getName(),
                    'last_name' => $user->getName(),
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'auth_type'=> 'google',
                    'password_sha' => sha1($password),
                    'email_is_confirmed' => true,
                    'role_id' => $this->getRole($user->email),
                    'icon' => $user->getAvatar(),
                ]);

                $this->loginUseCase->handle($user->toArray(), false);

                return redirect('http://127.0.0.1:3000/account');
            }

        } catch (Exception $e) {
            dd($e);
        }
    }

    private function getRole(string $email): int
    {
        $roleName = Role::ROLE_CUSTOMER;
        if (Administrator::where('moderators', 'like', "%\"{$email}\"%")->exists()) {
            $roleName = Role::ROLE_MODERATOR;
        }

        if (Company::where('team_members', 'like', "%\"{$email}\"%")->exists()) {
            $roleName = Role::ROLE_PUBLISHER_TEAM_MEMBER;
        }

        return $this->roleRepository->findByName($roleName)->id;
    }
}
