<?php

namespace App\Repositories;

use App\Models\Administrator;
use App\Models\GameCategory;
use Throwable;
use Webmozart\Assert\Assert;

class AdministratorRepository
{
    public function createOrUpdate(string $administratorEmail, string $moderatorEmail, int $administratorId): Administrator
    {
        try {
            $administrator = Administrator::where([
                'user_id' => $administratorId,
            ])->firstOrFail();

            $moderators = $administrator->moderators;
            $moderators[] = $moderatorEmail;

            $administrator->moderators = $moderators;
            $administrator->save();

            return $administrator;
        } catch (Throwable) {
            return Administrator::create([
                'user_id' => $administratorId,
                'moderators' => [$moderatorEmail],
                'administrator_email' => $administratorEmail,
            ]);
        }
    }

    public function getByAdministratorUserId(int $administratorUserId): Administrator
    {
        return Administrator::where([
            'user_id' => $administratorUserId,
        ])->firstOrFail();
    }
}
