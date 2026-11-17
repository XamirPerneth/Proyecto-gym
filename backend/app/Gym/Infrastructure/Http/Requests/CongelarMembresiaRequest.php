<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CongelarMembresiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['dias' => ['required', 'integer']];
    }
}
