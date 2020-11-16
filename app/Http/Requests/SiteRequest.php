<?php

namespace App\Http\Requests;

use App\Models\Site;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteRequest extends FormRequest
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
            'id' => 'sometimes',
            'server_id' => 'required|exists:servers,id',
            'domain' => [
                'required',
                'max:255',
                Rule::unique(Site::class, 'domain')
                    ->ignore($this->site),
            ],
            'name' => 'required|max:255',
            'path' => [
                'required',
                'max:255',
                Rule::unique(Site::class, 'path')
                    ->ignore($this->site),
            ],
            'active' => 'sometimes|boolean',
            'settings' => 'sometimes|object',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'sometimes',
        ];
    }
}
