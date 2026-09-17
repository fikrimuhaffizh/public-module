<?php

namespace Modules\Public\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Modules\Public\Services\LandingPageService;

class UpdateLandingTemplateRequest extends BaseRequest
{
    public function rules(): array
    {
        $landing = app(LandingPageService::class);

        return [
            'landing_template' => ['required', Rule::in($landing->themeKeys())],
        ];
    }

    protected function customAttributes(): array
    {
        return [
            'landing_template' => 'Template Landing',
        ];
    }
}
