<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreWithdrawalRequest;
use App\Http\Resources\Api\WithdrawalResource;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\WithdrawalResource - create goes
 * through Withdrawal::submit() (balance/minimum validation lives there,
 * not in this controller), view-only afterwards (no edit/delete on the
 * Filament side either).
 */
class WithdrawalController extends Controller
{
    #[OA\Get(
        path: '/withdrawals/balance',
        tags: ['Withdrawals'],
        summary: 'Saldo tersedia & minimum penarikan',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'available_balance', type: 'number'),
            new OA\Property(property: 'minimum_withdrawal', type: 'number'),
        ]))]
    )]
    public function balance(Request $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();

        return response()->json([
            'available_balance' => $partner->availableBalance(),
            'minimum_withdrawal' => (float) (PartnerSetting::current()->minimum_withdrawal ?? 0),
        ]);
    }

    #[OA\Get(
        path: '/withdrawals',
        tags: ['Withdrawals'],
        summary: 'Daftar withdrawal milik partner yang login',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $withdrawals = Withdrawal::query()
            ->where('partner_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return WithdrawalResource::collection($withdrawals)->response();
    }

    #[OA\Post(
        path: '/withdrawals',
        tags: ['Withdrawals'],
        summary: 'Ajukan withdrawal baru',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['amount', 'ktp'], properties: [
                new OA\Property(property: 'amount', type: 'number'),
                new OA\Property(property: 'ktp', type: 'string', format: 'binary'),
                new OA\Property(property: 'note', type: 'string', nullable: true),
            ])
        )),
        responses: [
            new OA\Response(response: 201, description: 'Berhasil, status pending'),
            new OA\Response(response: 422, description: 'Saldo tidak cukup / di bawah minimum penarikan'),
        ]
    )]
    public function store(StoreWithdrawalRequest $request): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user();
        $data = $request->validated();

        $withdrawal = Withdrawal::submit($partner, [
            'amount' => $data['amount'],
            'ktp_path' => $request->file('ktp')->store('withdrawals', 'partner_documents'),
            'note' => $data['note'] ?? null,
        ]);

        return (new WithdrawalResource($withdrawal))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/withdrawals/{withdrawal}',
        tags: ['Withdrawals'],
        summary: 'Detail withdrawal',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'withdrawal', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $withdrawal): WithdrawalResource
    {
        $record = Withdrawal::where('partner_id', $request->user()->id)->findOrFail($withdrawal);

        return new WithdrawalResource($record);
    }
}
