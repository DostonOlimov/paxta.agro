<?php

namespace App\Http\Services\Message;

use App\Enums\MessageType;
use App\Models\Message;
use App\Models\VehicleInspection;

class MessageService
{
    public function createExpiringInspectionNotification(VehicleInspection $inspection)
    {
        Message::create([
            'type_id' => MessageType::TYPE_EXPIRING_INSPECTION,
            'object_id' => $inspection->id,
            'phone' => $inspection->customer->clean_phone,
            'send_at' => now()->setTime(9, 0),
            'text' => __('notification.expiring_inspection.message', [
                'date' => $inspection->date->addYear()->format('d.m.Y'),
            ])
        ]);
    }
}
