<?php

namespace App\Http\Controllers\Administration;

use App\Http\Requests\Administration\CreateModeratorRequest;
use App\Http\Requests\Administration\RemoveModeratorRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdministratorRepository;
use App\Repositories\AdminSettingsRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\SettingsCacheService;
use App\UseCases\CreateModeratorUseCase;
use App\UseCases\RemoveModeratorUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class AdministratorSettingsController
{
    public function __construct(
        private AdminSettingsRepository $adminSettingsRepository,
        private SettingsCacheService $settingsCacheService,
    ) {
    }

    public function getByAdmin(int $adminId): Response
    {
        return new Response($this->adminSettingsRepository->getByAdminId($adminId));
    }

    public function general(int $adminId=1): Response
    {
        return new Response($this->settingsCacheService->general($adminId));
    }

    public function get(int $id): Response
    {
        return new Response($this->adminSettingsRepository->getByAdminId($id));
    }

    public function createOrUpdate(int $adminId, Request $request): Response
    {
        $this->settingsCacheService->clear($adminId);
        return new Response($this->adminSettingsRepository->createOrUpdate($adminId, $request->all()));
    }
}
