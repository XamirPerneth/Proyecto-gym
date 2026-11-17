<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AsignarMembresiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'socio_id' => ['required', 'integer'],
            'plan_id' => ['required', 'integer'],
            'tipo_tarifa' => ['required', 'string'],
        ];
    }
}
