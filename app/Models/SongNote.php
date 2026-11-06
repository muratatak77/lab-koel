<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\SongNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property string $song_id
 * @property Song $song
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static SongNoteFactory factory(...$parameters)
 */
#[Unguarded]
class SongNote extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
