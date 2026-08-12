<?php

namespace App\Notifications;

use App\Models\Property;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class PropertyApprovedNotification extends Notification
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

        return [
            'type' => 'property_approved',
            'title' => __('notifications.property_approved.title', [], $locale),
            'message' => __('notifications.property_approved.message', [
                'title' => $title,
            ], $locale),
            'property_id' => $this->property->id,
            'property_title' => $title,
            'url' => route('owner.properties.show', $this->property),
        ];
    }
}
