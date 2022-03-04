<?php

namespace LaravelEnso\Files\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateName extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['name' => 'required|string|max:255'];
    }
}
