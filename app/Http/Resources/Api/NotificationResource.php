<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\DatabaseNotification;
use OpenApi\Attributes as OA;

/**
 * @mixin DatabaseNotification
 *
 * All in-app notifications in this system are sent via
 * Filament\Notifications\Notification::make()->sendToDatabase($partner) -
 * its stored `data` payload always includes at least a `title` (see
 * Notification::getDatabaseMessage()), `body` is optional depending on
 * which notification sent it.
 */
#[OA\Schema(
    schema: 'Notification',
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'body', type: 'string', nullable: true),
        new OA\Property(property: 'read_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class NotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->data['title'] ?? null,
            'body' => $this->data['body'] ?? null,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
