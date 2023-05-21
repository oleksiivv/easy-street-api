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

    public function removeModerator(string $administratorEmail, string $moderatorEmail, int $administratorId): Administrator
    {
        $administrator = Administrator::where([
            'user_id' => $administratorId,
        ])->firstOrFail();

        $moderators = array_filter($administrator->moderators, function ($email) use ($moderatorEmail) {
            return $email !== $moderatorEmail;
        });

        $administrator->moderators = $moderators;
        $administrator->save();

        return $administrator;
    }

    public function getByAdministratorUserId(int $administratorUserId): Administrator
    {
        return Administrator::where([
            'user_id' => $administratorUserId,
        ])->firstOrFail();
    }
}
