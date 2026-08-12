<?php

namespace App\Notifications;

use App\Models\Inquiry;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class InquiryStatusUpdatedNotification extends Notification
{
    use ResolvesNotifiableLocale;

    public function __construct(
        private readonly Inquiry $inquiry,
        private readonly string $newStatus,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $locale = $this->localeFor($notifiable);
        $title = $this->propertyTitleFor($this->inquiry->property, $locale);
        $statusLabel = __("inquiries.status.{$this->newStatus}", [], $locale);

        return [
            'type' => 'inquiry_status_updated',
            'title' => __('notifications.inquiry_status_updated.title', [], $locale),
            'message' => __('notifications.inquiry_status_updated.message', [
                'title' => $title,
                'status' => $statusLabel,
            ], $locale),
            'inquiry_id' => $this->inquiry->id,
            'property_id' => $this->inquiry->property_id,
            'property_title' => $title,
            'status' => $this->newStatus,
            'status_label' => $statusLabel,
            'url' => route('properties.show', $this->inquiry->property),
        ];
    }
}
