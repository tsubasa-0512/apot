<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'item-image'  => ['required', 'file', 'image'],
            'file'        => ['required', 'file'],
            'description' => ['required', 'string', 'max:2000'],
            'category'    => ['required', 'integer'],
            'price'       => ['required', 'integer', 'min:100', 'max:9999999'],
       ];
    }

    public function attributes()
    {
        return [
            'item-image'  => 'コンテンツサンプル',
            'title'        => 'コンテンツ名',
            'file'        => 'コンテンツ',
            'description' => 'コンテンツ概要',
            'category'    => 'カテゴリ',
            'price'       => '販売価格',
        ];
    }
}
