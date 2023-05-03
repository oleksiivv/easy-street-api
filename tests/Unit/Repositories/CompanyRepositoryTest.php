<?php

namespace Tests\Unit\Repositories;


use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CompanyRepositoryTest
{
    public function get(int $id): Company
    {
        return Company::findOrFail($id)->load('games', 'games.gamePage');
    }

    public function list(array $filter = [], string $sort = 'id', string $direction = Company::COMPANY_SORT_DIRECTION_ASC): Collection
    {
        if ($sort === 'es_index') {
            $games = array_values(Company::where($filter)
                ->whereHas('games', function($q)
                {
                    $q->where('status', '!=', 'rejected');

                })->get()->load('games', 'games.gamePage')
                ->sortByDesc(function ($company) {
                    return $company->games->sum('es_index');
                })->toArray());
        }
        else {
            $games = Company::where($filter)->orderBy($sort, $direction)->get()->load('games', 'games.gamePage');
        }

        for ($i = 0; $i < count($games); $i++) {
            if (count($games[$i]['games'] ?? []) === 0) {
                $games[$i]['games'] = [];
            } else {
                $gamesSorted = $games[$i]['games'];
                usort($gamesSorted, function ($a, $b) {
                    return $a['es_index'] < $b['es_index'] ? 1 : -1;
                });

                $games[$i]['games'] = $gamesSorted;
            }
        }

        $result = collect([]);
        $result['data'] = $games;
        $result['pagination'] = [];

        return $result;
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(int $id, array $data): Company
    {
        $company = Company::findOrFail($id);

        $company->update($data);

        return $company->refresh();
    }
}
