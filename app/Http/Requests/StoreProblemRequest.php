<?php

namespace App\Http\Requests;

class StoreProblemRequest extends ProblemRequest
{
    /**
     * Get the validation rules for the uploaded image.
     *
     * @return array<int, string>
     */
    protected function imageRules(): array
    {
        return ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }
}
