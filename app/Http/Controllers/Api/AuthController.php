<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterPartnerRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Resources\Api\PartnerResource;
use App\Mail\PartnerPasswordResetRequested;
use App\Mail\PartnerRegistrationReceived;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\SiteSetting;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/auth/register',
        tags: ['Auth'],
        summary: 'Registrasi partner baru',
        description: 'Mendaftarkan partner baru dengan status pending_review. Sama persis dengan alur registrasi 4-langkah di panel web (akun, dokumen KYC, rekening bank, persetujuan perjanjian), dikirim sekaligus dalam satu request multipart.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'email', 'password', 'password_confirmation', 'profile_photo', 'ktp', 'bank_name', 'bank_account_number', 'bank_account_holder', 'agreement_accepted'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                        new OA\Property(property: 'password', type: 'string', format: 'password'),
                        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                        new OA\Property(property: 'profile_photo', type: 'string', format: 'binary'),
                        new OA\Property(property: 'ktp', type: 'string', format: 'binary'),
                        new OA\Property(property: 'npwp', type: 'string', format: 'binary', nullable: true),
                        new OA\Property(property: 'bank_name', type: 'string'),
                        new OA\Property(property: 'bank_account_number', type: 'string'),
                        new OA\Property(property: 'bank_account_holder', type: 'string'),
                        new OA\Property(property: 'agreement_accepted', type: 'boolean'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Berhasil, akun berstatus pending_review', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Partner'),
            ])),
            new OA\Response(response: 422, description: 'Validasi gagal'),
        ]
    )]
    public function register(RegisterPartnerRequest $request): JsonResponse
    {
        $data = $request->validated();

        $partner = Partner::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'pending_review',
            'profile_photo_path' => $request->file('profile_photo')->store('registrations', 'partner_documents'),
            'ktp_path' => $request->file('ktp')->store('registrations', 'partner_documents'),
            'npwp_path' => $request->hasFile('npwp') ? $request->file('npwp')->store('registrations', 'partner_documents') : null,
            'bank_name' => $data['bank_name'],
            'bank_account_number' => $data['bank_account_number'],
            'bank_account_holder' => $data['bank_account_holder'],
            'agreement_accepted_at' => now(),
            'email_notifications_enabled' => PartnerSetting::current()->default_email_notifications_enabled,
        ]);

        Mail::to($partner->email)->send(
            new PartnerRegistrationReceived($partner, SiteSetting::current())
        );

        return (new PartnerResource($partner))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Login partner, mendapatkan token Sanctum',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'device_name', type: 'string', nullable: true, description: 'Nama perangkat, default "mobile"'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Berhasil login', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'partner', ref: '#/components/schemas/Partner'),
            ])),
            new OA\Response(response: 422, description: 'Email/password salah'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $partner = Partner::where('email', $data['email'])->first();

        // The 'api' guard is a Sanctum RequestGuard (token-based), which
        // doesn't support ->attempt() like the session guards do - password
        // is checked directly instead.
        if (! $partner || ! Hash::check($data['password'], $partner->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $partner->createToken($data['device_name'] ?? 'mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partner' => new PartnerResource($partner),
        ]);
    }

    #[OA\Post(
        path: '/auth/forgot-password',
        tags: ['Auth'],
        summary: 'Minta kode reset password',
        description: 'Mengirim kode reset password ke email partner (berlaku 60 menit). Selalu mengembalikan pesan sukses yang sama baik email terdaftar maupun tidak, untuk mencegah enumerasi akun.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Jika email terdaftar, kode reset dikirim', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
            ])),
            new OA\Response(response: 422, description: 'Validasi gagal / terlalu sering meminta ulang'),
        ]
    )]
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('partners')->sendResetLink(
            $request->only('email'),
            function (Partner $partner, string $token): void {
                Mail::to($partner->email)->send(
                    new PartnerPasswordResetRequested($partner, $token, SiteSetting::current())
                );
            }
        );

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => ['Mohon tunggu sebelum meminta kode reset password lagi.'],
            ]);
        }

        // Same response whether or not the email is registered - avoids
        // leaking which emails have a partner account.
        return response()->json([
            'message' => 'Jika email terdaftar, kode reset password telah dikirim.',
        ]);
    }

    #[OA\Post(
        path: '/auth/reset-password',
        tags: ['Auth'],
        summary: 'Reset password menggunakan kode dari email',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'token', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'token', type: 'string', description: 'Kode dari email reset password'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Password berhasil direset', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string'),
            ])),
            new OA\Response(response: 422, description: 'Kode tidak valid/kedaluwarsa, atau validasi gagal'),
        ]
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('partners')->reset(
            $request->only('email', 'token', 'password', 'password_confirmation'),
            function (Partner $partner, string $password): void {
                $partner->forceFill(['password' => Hash::make($password)])->save();

                // Revoke existing API tokens so a stolen/leaked token can't
                // keep a session alive after the partner resets their password.
                $partner->tokens()->delete();

                event(new PasswordReset($partner));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => ['Kode reset password tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        return response()->json([
            'message' => 'Password berhasil direset, silakan login dengan password baru.',
        ]);
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Logout (revoke token yang sedang dipakai)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil logout'),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: 'Data partner yang sedang login',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Partner'),
            ])),
            new OA\Response(response: 401, description: 'Tidak terautentikasi'),
        ]
    )]
    public function me(Request $request): PartnerResource
    {
        return new PartnerResource($request->user());
    }
}
