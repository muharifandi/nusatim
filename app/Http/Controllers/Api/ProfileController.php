<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\Api\PartnerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Mirrors App\Filament\Partner\Pages\Auth\EditProfile - same editable
 * fields (biodata, bank, photo/KTP/NPWP, password, notification pref).
 * Old files are auto-deleted on replace via Partner's DeletesOldFiles
 * trait (fires on ->update() when the path field is dirty).
 */
class ProfileController extends Controller
{
    #[OA\Get(
        path: '/profile',
        tags: ['Profile'],
        summary: 'Profil partner yang sedang login',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Partner'),
        ]))]
    )]
    public function show(Request $request): PartnerResource
    {
        return new PartnerResource($request->user());
    }

    #[OA\Put(
        path: '/profile',
        tags: ['Profile'],
        summary: 'Update biodata, rekening bank, preferensi notifikasi',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'bank_name', type: 'string'),
            new OA\Property(property: 'bank_account_number', type: 'string'),
            new OA\Property(property: 'bank_account_holder', type: 'string'),
            new OA\Property(property: 'email_notifications_enabled', type: 'boolean'),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function update(UpdateProfileRequest $request): PartnerResource
    {
        $request->user()->update($request->validated());

        return new PartnerResource($request->user()->fresh());
    }

    #[OA\Post(
        path: '/profile/photo',
        tags: ['Profile'],
        summary: 'Ganti foto profil',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['profile_photo'], properties: [
                new OA\Property(property: 'profile_photo', type: 'string', format: 'binary'),
            ])
        )),
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function updatePhoto(Request $request): PartnerResource
    {
        return $this->replaceDocument($request, 'profile_photo', 'profile_photo_path');
    }

    #[OA\Post(
        path: '/profile/ktp',
        tags: ['Profile'],
        summary: 'Ganti foto KTP',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['ktp'], properties: [
                new OA\Property(property: 'ktp', type: 'string', format: 'binary'),
            ])
        )),
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function updateKtp(Request $request): PartnerResource
    {
        return $this->replaceDocument($request, 'ktp', 'ktp_path');
    }

    #[OA\Post(
        path: '/profile/npwp',
        tags: ['Profile'],
        summary: 'Ganti foto NPWP',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['npwp'], properties: [
                new OA\Property(property: 'npwp', type: 'string', format: 'binary'),
            ])
        )),
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 422, description: 'Validasi gagal')]
    )]
    public function updateNpwp(Request $request): PartnerResource
    {
        return $this->replaceDocument($request, 'npwp', 'npwp_path');
    }

    protected function replaceDocument(Request $request, string $field, string $column): PartnerResource
    {
        $request->validate([
            $field => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file($field)->store('profile', 'partner_documents');

        $request->user()->update([$column => $path]);

        return new PartnerResource($request->user()->fresh());
    }

    #[OA\Put(
        path: '/profile/password',
        tags: ['Profile'],
        summary: 'Ganti password',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['current_password', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'current_password', type: 'string', format: 'password'),
                new OA\Property(property: 'password', type: 'string', format: 'password'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 422, description: 'Password lama salah / validasi gagal'),
        ]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}
