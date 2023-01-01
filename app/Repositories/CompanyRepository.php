<?php

namespace App\Repositories;


use App\Models\Company;
use Illuminate\Support\Collection;

class CompanyRepository
{
    public function get(int $id): Company
    {
        return Company::findOrFail($id);
    }

    public function list(array $filter = [], string $sort = 'id', string $direction = Company::COMPANY_SORT_DIRECTION_ASC): Collection
    {
        $games = Company::where($filter)->orderBy($sort, $direction)->get();

        $result = collect([]);
        $result['data'] = $games;
        $result['pagination'] = [];

        return $result;
    }

    public function create(array $data): Company
    {
        return Company::create(array_filter($data));
    }

    public function update(int $id, array $data): Company
    {
        $game = Company::findOrFail($id);

        $game->update(array_filter($data));

        return $game->refresh();
    }
}
