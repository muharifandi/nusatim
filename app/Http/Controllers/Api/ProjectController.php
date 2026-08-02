<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\ProjectResource;
use App\Models\PartnerProject;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\PartnerProjectResource - the board
 * shows projects open for claiming PLUS anything this partner has ever
 * claimed (not a plain partner_id scope like Lead/Customer), and claim/
 * cancelClaim delegate entirely to PartnerProject's own race-safe methods.
 */
class ProjectController extends Controller
{
    protected function scopedQuery(Partner $partner): Builder
    {
        return PartnerProject::query()
            ->where(fn (Builder $q) => $q->where('status', 'available')->orWhere('partner_id', $partner->id));
    }

    #[OA\Get(
        path: '/projects',
        tags: ['Projects'],
        summary: 'Papan project - project available untuk semua + project yang pernah diklaim partner ini',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $projects = $this->scopedQuery($request->user())
            ->with('service')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ProjectResource::collection($projects)->response();
    }

    #[OA\Get(
        path: '/projects/{project}',
        tags: ['Projects'],
        summary: 'Detail project',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $project): ProjectResource
    {
        $record = $this->scopedQuery($request->user())->with('service')->findOrFail($project);

        return new ProjectResource($record);
    }

    #[OA\Post(
        path: '/projects/{project}/claim',
        tags: ['Projects'],
        summary: 'Klaim project (race-safe - hanya 1 partner yang menang kalau bersamaan)',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil diklaim, menunggu approval admin'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
            new OA\Response(response: 409, description: 'Project baru saja diklaim partner lain'),
            new OA\Response(response: 422, description: 'Sudah mencapai batas maksimal project yang diklaim bersamaan'),
        ]
    )]
    public function claim(Request $request, int $project): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();
        $record = $this->scopedQuery($partner)->findOrFail($project);

        // claim() throws ValidationException itself when the partner is at
        // their concurrent-claim limit - left to propagate, Laravel's
        // default handler turns that into the same 422 shape as any other
        // validation failure.
        $claimed = $record->claim($partner);

        if (! $claimed) {
            return response()->json([
                'message' => 'Project ini baru saja diklaim partner lain.',
            ], 409);
        }

        return (new ProjectResource($record))->response();
    }

    #[OA\Post(
        path: '/projects/{project}/cancel-claim',
        tags: ['Projects'],
        summary: 'Batalkan klaim yang masih menunggu approval',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function cancelClaim(Request $request, int $project): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        $record = PartnerProject::where('partner_id', $partner->id)
            ->where('status', 'pending_approval')
            ->findOrFail($project);

        $record->cancelClaim();

        return (new ProjectResource($record->fresh()))->response();
    }
}
