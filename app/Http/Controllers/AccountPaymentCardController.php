<?php

namespace App\Http\Controllers;

use App\Repositories\UserPaymentCardDataRepository;
use App\UseCases\MakePaymentCardInformationDefault;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountPaymentCardController extends Controller
{
    public function __construct(private UserPaymentCardDataRepository $userPaymentCardDataRepository)
    {
    }

    public function index(int $userId)
    {
        return new Response($this->userPaymentCardDataRepository->findBy([
            'user_id' => $userId,
        ]));
    }

    public function add(int $userId, Request $request)
    {
        return new Response($this->userPaymentCardDataRepository->create($userId, $request->all()));
    }

    public function makeDefault(int $userId, int $cardId, MakePaymentCardInformationDefault $useCase)
    {
        return new Response($useCase->handle($userId, $cardId));
    }

    public function getDefault(int $userId)
    {
        return new Response($this->userPaymentCardDataRepository->findBy([
            'user_id' => $userId,
            'is_default' => true,
        ])->first() ?? []);
    }

    public function delete(int $userId, int $cardId)
    {
        $this->userPaymentCardDataRepository->delete($cardId);

        return response()->noContent();
    }

    public function deleteAll(int $userId)
    {
        $this->userPaymentCardDataRepository->deleteAll($userId);

        return response()->noContent();
    }
}
