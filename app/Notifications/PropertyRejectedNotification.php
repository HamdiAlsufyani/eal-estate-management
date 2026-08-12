<?php

namespace App\Notifications;

use App\Models\Property;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class PropertyRejectedNotification extends Notification
{
    use ResolvesNotifiableLocale;

    public function __construct(
        private readonly Property $property,
        private readonly ?string $reason,
    ) {
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
            'type' => 'property_rejected',
            'title' => __('notifications.property_rejected.title', [], $locale),
            'message' => __('notifications.property_rejected.message', [
                'title' => $title,
            ], $locale),
            'property_id' => $this->property->id,
            'property_title' => $title,
            'reason' => $this->reason,
            'url' => route('owner.properties.show', $this->property),
        ];
    }
}
