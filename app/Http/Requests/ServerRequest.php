<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Server;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'sometimes|numeric',
            'name' => 'required|max:255',
            'hostname' => [
                'required|url|max:255',
                Rule::unique(Server::class, 'hostname')
                    ->ignore($this->route('server')->id),
            ],
            'path' => 'required|max:255',
            'settings' => 'sometimes|object',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'sometimes',
        ];
    }
}
