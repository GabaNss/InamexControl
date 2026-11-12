<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * O e-mail de LOGIN e imutavel pelo usuario (definido apenas por um admin na
     * criacao da conta), por isso NAO entra nas regras — qualquer 'email' enviado
     * no request e ignorado (nunca chega ao $request->validated()). Ver tambem
     * App\Http\Controllers\ProfileController::update.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
