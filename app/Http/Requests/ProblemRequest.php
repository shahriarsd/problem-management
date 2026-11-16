<?php

namespace App\Http\Requests;

use App\Support\HtmlSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

abstract class ProblemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sanitize the rich text fields before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $content = [];

        foreach (['description', 'solution'] as $field) {
            $raw = $this->input($field);
            $clean = is_string($raw) ? HtmlSanitizer::clean($raw) : '';

            $content[$field] = HtmlSanitizer::isBlank($clean) ? '' : $clean;
        }

        $this->merge($content);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:20000'],
            'image' => $this->imageRules(),
            'solution' => ['required', 'string', 'max:20000'],
        ];
    }

    /**
     * Get the validation rules for the uploaded image.
     *
     * @return array<int, string>
     */
    abstract protected function imageRules(): array;

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The problem title field is required.',
            'description.required' => 'The problem description field is required.',
            'solution.required' => 'The solution description field is required.',
            'image.required' => 'Please upload an image for the problem.',
            'image.image' => 'The uploaded file must be a valid image.',
            'image.mimes' => 'The image must be a JPG, JPEG, PNG, or WebP file.',
            'image.max' => 'The image may not be larger than 2 MB.',
        ];
    }
}
