<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RegistrarSocioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => ['required', 'string', 'max:30'],
            'nombre' => ['required', 'string', 'max:120'],
        ];
    }
}
