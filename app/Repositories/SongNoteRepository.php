<?php

namespace App\Repositories;

use App\Models\Song;
use App\Models\SongNote;
use App\Models\User;

/**
 * @extends Repository<SongNote>
 */
class SongNoteRepository extends Repository
{
    public function findOneBySongAndUser(Song $song, User $user): ?SongNote
    {
        return $user->songNotes()->whereBelongsTo($song)->first();
    }
}
