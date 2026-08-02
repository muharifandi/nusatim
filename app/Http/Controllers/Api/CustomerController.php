<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UpdateCustomerRequest;
use App\Http\Resources\Api\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Resources\CustomerResource - a Customer is
 * always derived from a Won lead or an approved project claim, never
 * created manually here either (canCreate() is false on the Filament side).
 */
class CustomerController extends Controller
{
    protected function resolveCustomer(Request $request, int $id): Customer
    {
        return Customer::where('partner_id', $request->user()->id)
            ->with(['service', 'partnerProject', 'commission', 'lead.documents'])
            ->findOrFail($id);
    }

    #[OA\Get(
        path: '/customers',
        tags: ['Customers'],
        summary: 'Daftar customer milik partner yang login',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->where('partner_id', $request->user()->id)
            ->with(['service', 'partnerProject', 'commission'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return CustomerResource::collection($customers)->response();
    }

    #[OA\Get(
        path: '/customers/{customer}',
        tags: ['Customers'],
        summary: 'Detail customer - termasuk timeline, follow up/meeting, proposal',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Tidak ditemukan')]
    )]
    public function show(Request $request, int $customer): CustomerResource
    {
        return new CustomerResource($this->resolveCustomer($request, $customer));
    }

    #[OA\Put(
        path: '/customers/{customer}',
        tags: ['Customers'],
        summary: 'Update data customer',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function update(UpdateCustomerRequest $request, int $customer): CustomerResource
    {
        $record = $this->resolveCustomer($request, $customer);
        $record->update($request->validated());

        return new CustomerResource($record->fresh(['service', 'partnerProject', 'commission', 'lead.documents']));
    }

    #[OA\Patch(
        path: '/customers/{customer}/progress',
        tags: ['Customers'],
        summary: 'Update progress project terkait (0-100), hanya jika customer punya project',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'customer', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['progress'],
            properties: [new OA\Property(property: 'progress', type: 'integer', minimum: 0, maximum: 100)]
        )),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
            new OA\Response(response: 422, description: 'Customer ini tidak punya project terkait / progress tidak valid'),
        ]
    )]
    public function updateProgress(Request $request, int $customer): CustomerResource
    {
        $data = $request->validate(['progress' => ['required', 'integer', 'min:0', 'max:100']]);

        $record = $this->resolveCustomer($request, $customer);

        if (! $record->partnerProject) {
            throw ValidationException::withMessages([
                'progress' => ['Customer ini tidak punya project terkait.'],
            ]);
        }

        $record->partnerProject->update(['progress' => $data['progress']]);

        return new CustomerResource($record->fresh(['service', 'partnerProject', 'commission', 'lead.documents']));
    }
}
