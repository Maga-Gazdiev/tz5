<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:5|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            "required" => "поле :attribute обязательно к заполнению",
            "unique" => ":attribute должно быть уникальным",
            "min" => "минимальное количество символов - 5",
            "string" => "Ожидается текстовое значение",

            "title.max" => "Максимальное колличество символов 255",
            "content.max" => "Максимальное колличество символов 1000",
        ];
    }
}
