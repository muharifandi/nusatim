<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\MarketingMaterialResource;
use App\Models\MarketingMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\MarketingMaterialResource -
 * read-only, active-only, grouped by category. File-based materials live
 * on the public `media` disk (unlike KYC/lead documents), so a plain
 * asset() URL is enough - no auth-gated stream endpoint needed.
 */
class MarketingMaterialController extends Controller
{
    #[OA\Get(
        path: '/marketing-materials',
        tags: ['MarketingMaterials'],
        summary: 'Daftar materi marketing aktif, dikelompokkan per kategori',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'category', in: 'query', schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $materials = MarketingMaterial::query()
            ->active()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        $grouped = $materials->map(fn ($items) => MarketingMaterialResource::collection($items->values()));

        return response()->json($grouped);
    }

    #[OA\Get(
        path: '/marketing-materials/{marketingMaterial}',
        tags: ['MarketingMaterials'],
        summary: 'Detail materi marketing',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'marketingMaterial', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(int $marketingMaterial): MarketingMaterialResource
    {
        return new MarketingMaterialResource(MarketingMaterial::active()->findOrFail($marketingMaterial));
    }
}
