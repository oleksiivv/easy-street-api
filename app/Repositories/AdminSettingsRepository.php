<?php

namespace App\Repositories;

use App\Models\AdminSettings;
use App\Models\FinancialEvent;
use App\Models\Payout;
use Illuminate\Support\Collection;

class AdminSettingsRepository
{
    public function createOrUpdate(int $adminId, array $data): AdminSettings
    {
        try {
            return $this->updateByAdminId($adminId, $data);
        } catch (\Throwable) {
            return $this->create(array_merge($data, [
                'admin_id' => $adminId,
            ]));
        }
    }

    public function create(array $data): AdminSettings
    {
        return AdminSettings::create($data);
    }

    public function update(int $id, array $settings): AdminSettings
    {
        $adminSettings = AdminSettings::firstOrFail($id);

        $adminSettings->update([
            'settings' => $settings,
        ]);

        return $adminSettings->refresh();
    }

    public function updateByAdminId(int $adminId, array $data): AdminSettings
    {
        $adminSettings = $this->getByAdminId($adminId);

        $adminSettings->update([
            'settings' => $data['settings'],
        ]);

        return $adminSettings->refresh();
    }

    public function getByAdminId(int $adminId): AdminSettings
    {
        return AdminSettings::where(['admin_id' => $adminId])->firstOrFail();
    }

    public function getById(int $id): AdminSettings
    {
        return AdminSettings::firstOrFail($id);
    }
}
