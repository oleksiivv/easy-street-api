<?php

namespace App\Http\Resources;

use App\Repositories\GameRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role->name,
            'downloads' => $this->downloads,
            'subscriptions' => $this->subscriptions,
            'likes' => $this->likes,
            'companies' => $this->companies ? $this->getCompanies($this->companies) : [],
            'icon' => $this->icon
        ];
    }

    private function getCompanies($companies)
    {
        return $companies->map(function ($company) {
            $esIndex = 0;

            $company->games->map(function ($game) use (&$esIndex) {
                $esIndex += $game->es_index;
            });

            $companyData = $company->toArray();
            $companyData['es_index'] = $esIndex;
            $companyData['signature_game'] = $this->getBestGame($company->id);

            return $companyData;
        });
    }

    private function getBestGame(int $companyId)
    {
        /** @var \App\Repositories\GameRepository $gameRepository */
        $gameRepository = app(GameRepository::class);

        return $gameRepository->getBest($companyId);
    }
}
