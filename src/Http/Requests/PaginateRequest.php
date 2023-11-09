<?php

namespace DevBRLucas\LaravelBaseApp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric',
            'items' => 'nullable|numeric',
        ];
    }
}
