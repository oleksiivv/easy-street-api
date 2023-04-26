<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatService\CreateChatRequest;
use App\Models\GameAction;
use App\Repositories\ChatService\ChatRepository;
use App\Repositories\ChatService\MessageRepository;
use App\Services\GameAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChatController extends Controller
{
    public function __construct(
        private ChatRepository $chatRepository,
        private MessageRepository $messageRepository,
        private GameAccessService $gameAccessService,
    ) {
    }

    public function create(CreateChatRequest $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $request->game_id)) {
            throw new HttpException(422);
        }

        return new Response($this->chatRepository->create($request->game_id));
    }

    public function index(int $id): Response
    {
        return new Response([
            'chat' => $this->chatRepository->getById($id),
            'messages' => $this->messageRepository->getAllFromChat($id),
        ]);
    }

    public function activeChats(Request $request): Response
    {
        return new Response([
            'chats' => $this->chatRepository->getActive()
        ]);
    }

    public function getByGameId(int $gameId): Response
    {
        $chat = $this->chatRepository->getByGameId($gameId);

        return new Response([
            'chat' => $chat,
            'messages' => $this->messageRepository->getAllFromChat($chat->id),
        ]);
    }

    public function getByUserId(int $userId, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $userId) {
            throw new HttpException(422);
        }

        $chats = $this->chatRepository->getByUserId($userId)->map(function ($item) {
            $item['last_message'] = $this->messageRepository->getLastByChatId($item->id);

            return $item;
        });

        return new Response([
            'chats' => $chats,
        ]);
    }

    public function getChatIdByGameId(int $gameId): Response
    {
        $chat = $this->chatRepository->getByGameId($gameId);

        return new Response([
            'chat_id' => $chat?->id ?? -1,
        ]);
    }
}
