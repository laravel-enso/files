<?php

namespace LaravelEnso\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

class Browse extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['required', 'integer', 'min:1'],
            'pagination' => ['required', 'integer', 'in:'.implode(',', Config::get('enso.files.paginate'))],
        ];
    }
}
