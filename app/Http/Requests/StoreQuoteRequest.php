<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:quotes,slug',
            'quote_text' => 'required|string',
            'lang' => 'nullable|string|in:english,gujarati,hindi',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'hashtags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'boolean',
        ];
    }
}
