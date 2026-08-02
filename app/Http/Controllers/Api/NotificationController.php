<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\NotificationResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors the bell-icon database notifications panel enabled via
 * ->databaseNotifications() in PartnerPanelProvider - Partner already
 * `use`s Laravel's built-in Notifiable trait, so this is a thin wrapper
 * over notifications()/unreadNotifications(), not a new notification
 * system.
 */
class NotificationController extends Controller
{
    #[OA\Get(
        path: '/notifications',
        tags: ['Notifications'],
        summary: 'Daftar notifikasi (terbaru dulu)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $notifications = $partner->notifications()->paginate($request->integer('per_page', 20));

        return NotificationResource::collection($notifications)->response();
    }

    #[OA\Get(
        path: '/notifications/unread-count',
        tags: ['Notifications'],
        summary: 'Jumlah notifikasi belum dibaca',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'unread_count', type: 'integer'),
        ]))]
    )]
    public function unreadCount(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        return response()->json(['unread_count' => $partner->unreadNotifications()->count()]);
    }

    #[OA\Patch(
        path: '/notifications/{notification}/read',
        tags: ['Notifications'],
        summary: 'Tandai satu notifikasi sudah dibaca',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'notification', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function markRead(Request $request, string $notification): NotificationResource
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $record = $partner->notifications()->findOrFail($notification);
        $record->markAsRead();

        return new NotificationResource($record->fresh());
    }

    #[OA\Patch(
        path: '/notifications/read-all',
        tags: ['Notifications'],
        summary: 'Tandai semua notifikasi sudah dibaca',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function markAllRead(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $partner->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
