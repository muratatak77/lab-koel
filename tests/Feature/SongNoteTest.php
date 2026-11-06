<?php

namespace Tests\Feature;

use App\Http\Requests\API\SaveSongNoteRequest;
use App\Http\Resources\SongNoteResource;
use App\Models\Song;
use App\Models\SongNote;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use function Tests\create_user;

class SongNoteTest extends TestCase
{
    #[Test]
    public function fetchesTheNoteOfTheCurrentUser(): void
    {
        $user = create_user();
        $song = Song::factory()->createOne();
        SongNote::factory()->for($user)->for($song)->createOne(['content' => 'Great bassline at 2:15']);

        $this
            ->getAs("api/songs/{$song->id}/note", $user)
            ->assertOk()
            ->assertJsonStructure(SongNoteResource::JSON_STRUCTURE)
            ->assertJsonPath('song_id', $song->id)
            ->assertJsonPath('content', 'Great bassline at 2:15');
    }

    #[Test]
    public function returnsNoContentWhenTheSongHasNoNote(): void
    {
        $song = Song::factory()->createOne();

        $this->getAs("api/songs/{$song->id}/note")->assertNoContent();
    }

    #[Test]
    public function doesNotRevealTheNoteOfAnotherUser(): void
    {
        $song = Song::factory()->createOne();
        SongNote::factory()->for(create_user())->for($song)->createOne();

        $this->getAs("api/songs/{$song->id}/note", create_user())->assertNoContent();
    }

    #[Test]
    public function createsANote(): void
    {
        $user = create_user();
        $song = Song::factory()->createOne();

        $this
            ->putAs("api/songs/{$song->id}/note", ['content' => 'Play this at the party'], $user)
            ->assertCreated()
            ->assertJsonStructure(SongNoteResource::JSON_STRUCTURE)
            ->assertJsonPath('content', 'Play this at the party');

        $this->assertDatabaseHas(SongNote::class, [
            'user_id' => $user->id,
            'song_id' => $song->id,
            'content' => 'Play this at the party',
        ]);
    }

    #[Test]
    public function updatesAnExistingNote(): void
    {
        $user = create_user();
        $song = Song::factory()->createOne();
        $note = SongNote::factory()->for($user)->for($song)->createOne(['content' => 'Old thoughts']);

        $this
            ->putAs("api/songs/{$song->id}/note", ['content' => 'New thoughts'], $user)
            ->assertOk()
            ->assertJsonPath('id', $note->id)
            ->assertJsonPath('content', 'New thoughts');

        self::assertSame(1, $user->songNotes()->count());
        self::assertSame('New thoughts', $note->refresh()->content);
    }

    #[Test]
    public function savingANoteLeavesTheNotesOfOtherUsersAlone(): void
    {
        $song = Song::factory()->createOne();
        $otherUsersNote = SongNote::factory()->for(create_user())->for($song)->createOne(['content' => 'Mine']);

        $this->putAs("api/songs/{$song->id}/note", ['content' => 'Yours'], create_user())->assertCreated();

        self::assertSame('Mine', $otherUsersNote->refresh()->content);
    }

    #[Test]
    public function rejectsAnEmptyNote(): void
    {
        $song = Song::factory()->createOne();

        $this->putAs("api/songs/{$song->id}/note", [
            'content' => '   ',
        ])->assertUnprocessable()->assertJsonValidationErrors('content');
    }

    #[Test]
    public function rejectsATooLongNote(): void
    {
        $song = Song::factory()->createOne();

        $this
            ->putAs("api/songs/{$song->id}/note", [
                'content' => str_repeat('a', SaveSongNoteRequest::MAX_CONTENT_LENGTH + 1),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('content');
    }

    #[Test]
    public function deletesTheNoteOfTheCurrentUser(): void
    {
        $user = create_user();
        $song = Song::factory()->createOne();
        $note = SongNote::factory()->for($user)->for($song)->createOne();
        $otherUsersNote = SongNote::factory()->for(create_user())->for($song)->createOne();

        $this->deleteAs("api/songs/{$song->id}/note", [], $user)->assertNoContent();

        $this->assertModelMissing($note);
        $this->assertModelExists($otherUsersNote);
    }
}
