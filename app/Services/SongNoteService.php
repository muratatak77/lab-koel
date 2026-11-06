<?php

namespace App\Services;

use App\Models\Song;
use App\Models\SongNote;
use App\Models\User;

class SongNoteService
{
    public function saveNote(Song $song, User $user, string $content): SongNote
    {
        return $user->songNotes()->updateOrCreate(['song_id' => $song->id], ['content' => $content]);
    }

    public function deleteNote(Song $song, User $user): void
    {
        $user->songNotes()->whereBelongsTo($song)->delete();
    }
}
