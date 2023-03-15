<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatService\CreateChatRequest;
use App\Repositories\ChatService\ChatRepository;
use App\Repositories\ChatService\MessageRepository;
use Illuminate\Http\Response;

class ChatController extends Controller
{
    public function __construct(
        private ChatRepository $chatRepository,
        private MessageRepository $messageRepository,
    ) {
    }

    public function create(CreateChatRequest $request): Response
    {
        return new Response($this->chatRepository->create($request->game_id));
    }

    public function index(int $id): Response
    {
        return new Response([
            'chat' => $this->chatRepository->getById($id),
            'messages' => $this->messageRepository->getAllFromChat($id),
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

    public function getChatIdByGameId(int $gameId): Response
    {
        $chat = $this->chatRepository->getByGameId($gameId);

        return new Response([
            'chat_id' => $chat?->id ?? -1,
        ]);
    }
}
