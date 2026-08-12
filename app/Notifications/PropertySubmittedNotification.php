<?php

namespace App\Notifications;

use App\Models\Property;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class PropertySubmittedNotification extends Notification
{
    use ResolvesNotifiableLocale;

    public function __construct(private readonly Property $property)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $locale = $this->localeFor($notifiable);
        $title = $this->propertyTitleFor($this->property, $locale);
        $ownerName = $this->property->user?->name ?? '';

        return [
            'type' => 'property_submitted',
            'title' => __('notifications.property_submitted.title', [], $locale),
            'message' => __('notifications.property_submitted.message', [
                'title' => $title,
                'owner' => $ownerName,
            ], $locale),
            'property_id' => $this->property->id,
            'property_title' => $title,
            'owner_name' => $ownerName,
            'url' => route('admin.properties.show', $this->property),
        ];
    }
}
