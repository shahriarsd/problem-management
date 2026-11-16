<?php

namespace App\Http\Requests;

class UpdateProblemRequest extends ProblemRequest
{
    /**
     * Get the validation rules for the uploaded image.
     *
     * @return array<int, string>
     */
    protected function imageRules(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }
}
