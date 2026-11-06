<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;

/**
 * @property-read string $content
 */
class SaveSongNoteRequest extends Request
{
    public const int MAX_CONTENT_LENGTH = 10_000;

    /** @inheritdoc */
    public function rules(): array
    {
        return [
            'content' => [Rule::required(), Rule::string()->max(self::MAX_CONTENT_LENGTH)],
        ];
    }
}
