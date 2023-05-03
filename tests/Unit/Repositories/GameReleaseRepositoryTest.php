<?php

namespace Tests\Unit\Repositories;

use App\DTO\GameReleaseDTO;
use App\Models\Game;
use App\Models\GameRelease;
use Throwable;

class GameReleaseRepositoryTest
{
    public function create(GameReleaseDTO $data, int $gameId): GameRelease
    {
        $data->game_id = $gameId;

        return GameRelease::create($data->toArray());
    }

    public function update(GameReleaseDTO $data): GameRelease
    {
        try {
            $gameRelease = GameRelease::whereHas('game', function ($query) use ($data) {
                return $query->where('id', $data->game_id);
            })->where('version', $data->version)->firstOrFail();
            $gameRelease->update(array_filter($data->toArray()));

            $gameRelease->save();
        } catch (Throwable) {
            $gameRelease = GameRelease::create($data->toArray());
        }

        return $gameRelease;
    }

    public function getAvailableVersions(int $gameId): array
    {
        $release = Game::findOrFail($gameId)->gameReleases?->last();

        if (!$release) {
            return [];
        }

        return array_filter([
            'android_file_url' => $release->android_file_url,
            'ios_file_url' => $release->ios_file_url,
            'windows_file_url' => $release->windows_file_url,
            'mac_file_url' => $release->mac_file_url,
            'linux_file_url' => $release->linux_file_url,

            'images' => [
                'android_file_url' => $release->android_icon,
                'ios_file_url' => $release->ios_icon,
                'windows_file_url' => $release->windows_icon,
                'mac_file_url' => $release->mac_icon,
                'linux_file_url' => $release->linux_icon,
            ],
        ]);
    }

    public function updateByArray(?int $id, array $data): GameRelease
    {
        try {
            $gameRelease = GameRelease::find($id);
            $gameRelease->update(array_filter($data));

            $gameRelease->save();
        } catch (Throwable) {
            $gameRelease = GameRelease::create($data);
        }

        return $gameRelease;
    }
}
