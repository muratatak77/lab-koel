<?php

namespace App\Http\Resources;

use App\Models\SongNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongNoteResource extends JsonResource
{
    public const array JSON_STRUCTURE = [
        'type',
        'id',
        'song_id',
        'content',
        'created_at',
        'updated_at',
    ];

    public function __construct(
        private readonly SongNote $note,
    ) {
        parent::__construct($note);
    }

    /** @inheritdoc */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'song-notes',
            'id' => $this->note->id,
            'song_id' => $this->note->song_id,
            'content' => $this->note->content,
            'created_at' => $this->note->created_at,
            'updated_at' => $this->note->updated_at,
        ];
    }
}
