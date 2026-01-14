<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
