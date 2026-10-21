<?php

declare(strict_types=1);

namespace App\Http\Requests\Announcements;

use App\Services\Announcements\AnnouncementService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared rules for creating and editing an announcement. Who may open the form at all is decided
 * by the route permission (AuthorizeRoute), not here.
 */
abstract class AnnouncementFormRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'form_action' => ['required', Rule::in([AnnouncementService::SAVE, AnnouncementService::PUBLISH])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => __('Title'),
            'body' => __('Content'),
            'starts_on' => __('Start date'),
            'ends_on' => __('End date'),
        ];
    }

    /**
     * @return array{title: string, body: string, starts_on: string, ends_on: ?string}
     */
    public function announcementData(): array
    {
        return [...['ends_on' => null], ...$this->safe()->only(['title', 'body', 'starts_on', 'ends_on'])];
    }

    public function formAction(): string
    {
        return $this->string('form_action')->toString();
    }
}
