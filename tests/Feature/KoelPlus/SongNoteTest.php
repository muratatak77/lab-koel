<?php

namespace Tests\Feature\KoelPlus;

use App\Models\Song;
use App\Models\SongNote;
use PHPUnit\Framework\Attributes\Test;
use Tests\PlusTestCase;

use function Tests\create_user;

class SongNoteTest extends PlusTestCase
{
    #[Test]
    public function savesANoteOnAnAccessibleSong(): void
    {
        $user = create_user();
        $song = Song::factory()->for($user, 'owner')->private()->createOne();

        $this->putAs("api/songs/{$song->id}/note", ['content' => 'Demo take'], $user)->assertCreated();

        $this->assertDatabaseHas(SongNote::class, ['user_id' => $user->id, 'song_id' => $song->id]);
    }

    #[Test]
    public function forbidsNotesOnAnInaccessibleSong(): void
    {
        $user = create_user();
        $song = Song::factory()->private()->createOne();

        $this->getAs("api/songs/{$song->id}/note", $user)->assertForbidden();
        $this->putAs("api/songs/{$song->id}/note", ['content' => 'Sneaky'], $user)->assertForbidden();
        $this->deleteAs("api/songs/{$song->id}/note", [], $user)->assertForbidden();

        $this->assertDatabaseMissing(SongNote::class, ['song_id' => $song->id]);
    }
}
