<?php

declare(strict_types=1);

namespace App\Gym\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['documento' => ['required', 'string', 'max:30']];
    }
}
