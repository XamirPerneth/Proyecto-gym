<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ActualizarAjustesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_gimnasio' => ['required', 'string', 'max:120'],
            'descuento_trimestral' => ['required', 'integer', 'min:0', 'max:100'],
            'descuento_anual' => ['required', 'integer', 'min:0', 'max:100'],
            'hora_simulada' => ['nullable', 'string'],
            'planes' => ['required', 'array', 'min:1'],
            'planes.*.id' => ['required', 'integer'],
            'planes.*.hora_inicio' => ['required', 'string'],
            'planes.*.hora_fin' => ['required', 'string'],
            'planes.*.precio_base_mensual' => ['required', 'integer', 'min:0'],
        ];
    }
}
