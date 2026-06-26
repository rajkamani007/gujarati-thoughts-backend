<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'category_id' => 'sometimes|required|exists:categories,id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:quotes,slug,' . $id,
            'quote_text' => 'sometimes|required|string',
            'lang' => 'nullable|string|in:english,gujarati,hindi',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'hashtags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'boolean',
        ];
    }
}
