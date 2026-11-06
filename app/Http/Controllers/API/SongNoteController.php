<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\SaveSongNoteRequest;
use App\Http\Resources\SongNoteResource;
use App\Models\Song;
use App\Models\User;
use App\Repositories\SongNoteRepository;
use App\Services\SongNoteService;
use Illuminate\Contracts\Auth\Authenticatable;

class SongNoteController extends Controller
{
    /** @param User $user */
    public function __construct(
        private readonly SongNoteRepository $repository,
        private readonly SongNoteService $songNoteService,
        private readonly Authenticatable $user,
    ) {}

    public function show(Song $song)
    {
        $this->authorize('access', $song);

        $note = $this->repository->findOneBySongAndUser($song, $this->user);

        return $note ? SongNoteResource::make($note) : response()->noContent();
    }

    public function update(SaveSongNoteRequest $request, Song $song)
    {
        $this->authorize('access', $song);

        return SongNoteResource::make($this->songNoteService->saveNote($song, $this->user, $request->content));
    }

    public function destroy(Song $song)
    {
        $this->authorize('access', $song);

        $this->songNoteService->deleteNote($song, $this->user);

        return response()->noContent();
    }
}
