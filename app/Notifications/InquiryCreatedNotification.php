<?php

namespace App\Notifications;

use App\Models\Inquiry;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class InquiryCreatedNotification extends Notification
{
    use ResolvesNotifiableLocale;

    public function __construct(private readonly Inquiry $inquiry)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $locale = $this->localeFor($notifiable);
        $title = $this->propertyTitleFor($this->inquiry->property, $locale);
        $customerName = $this->inquiry->user?->name ?? '';

        return [
            'type' => 'inquiry_created',
            'title' => __('notifications.inquiry_created.title', [], $locale),
            'message' => __('notifications.inquiry_created.message', [
                'title' => $title,
                'customer' => $customerName,
            ], $locale),
            'inquiry_id' => $this->inquiry->id,
            'property_id' => $this->inquiry->property_id,
            'property_title' => $title,
            'customer_name' => $customerName,
            'url' => route('owner.inquiries.show', $this->inquiry),
        ];
    }
}
