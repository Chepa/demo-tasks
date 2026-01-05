<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['sometimes', 'string', 'max:500'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
