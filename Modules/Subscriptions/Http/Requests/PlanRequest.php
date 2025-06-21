<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
            $rules = [
                'name' => ['required'],
                'duration' => ['required'],
                'description' => ['required'],
                'duration_value' => ['required', 'numeric', 'min:1'],
                'price' => ['required', 'numeric', 'min:0.01'],
                'language' => ['nullable', 'string'],
                'custom_gateway' => ['nullable', 'in:CartPanda,For4pay,TriboPay'],
                'external_product_id' => ['nullable', 'string'],
                'external_url' => ['nullable', 'url'],
            ];

            if ($this->isMethod('put')) {
                $rules['level'] = ['required'];
            }

            return $rules;

    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'level.required' =>'Level is required',
            'duration.required' =>'Duration is required',
            'duration_value.required' =>'Duration value is required',
            'duration_value.numeric' => 'Duration value must be a number.',
            'duration_value.min' => 'Duration value must be at least 1.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.01.',
            'language.string' => 'Language must be a valid string.',
            'custom_gateway.in' => 'Custom gateway must be one of: CartPanda, For4pay, TriboPay.',
            'external_product_id.string' => 'External product ID must be a valid string.',
            'external_url.url' => 'External URL must be a valid URL.',
        ];
    }
}
