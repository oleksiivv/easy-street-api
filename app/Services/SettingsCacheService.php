<?php

namespace App\Services;

use App\Repositories\AdminSettingsRepository;
use Illuminate\Support\Facades\Cache;

class SettingsCacheService
{
    public function __construct(private AdminSettingsRepository $adminSettingsRepository)
    {
    }

    public function clear(int $adminId=1): void
    {
        Cache::forget("settings{$adminId}");
    }

    public function general(int $adminId=1): array
    {
        $settings = Cache::get("settings{$adminId}");

        if (!isset($settings)) {
            $settings = $this->adminSettingsRepository->getByAdminId($adminId)->settings;
            Cache::put("settings{$adminId}", $settings, now()->addHours(24));
        }

        return $settings ?? [];
    }

}
