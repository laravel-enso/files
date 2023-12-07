<?php

namespace LaravelEnso\Files\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use ReflectionClass;

class ValidateType extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', $this->unique('name')],
            'model' => ['nullable', 'required_if:is_system,true', 'string', $this->unique('model')],
            'icon' => 'nullable|required_if:is_browsable,true|string',
            'folder' => 'required_with:model|string',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'is_browsable' => 'required|boolean',
            'is_system' => 'required|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(fn ($validator) => $this->modelIsValid($validator));
    }

    private function unique(string $attribute)
    {
        Rule::unique('file_types', $attribute)
            ->ignore($this->route('type')?->id);
    }

    private function modelIsValid($validator): void
    {
        $valid = class_exists($this->get('model'))
            && (new ReflectionClass($this->get('model')))
            ->isSubclassOf(Model::class);

        if (! $valid) {
            $validator->errors()->add('model', __('Model is not valid'));
        }
    }
}
