<?php

namespace LaravelEnso\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LaravelEnso\Files\Enums\TemporaryLinkDuration;

class ValidateLink extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'seconds' => Rule::enum(TemporaryLinkDuration::class),
        ];
    }
}
