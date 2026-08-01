<?php

namespace App\Http\Controllers\Api\Concerns;

use OpenApi\Attributes as OA;

/**
 * Not a real controller - purely a place to hang l5-swagger's global
 * OpenAPI metadata (info, security scheme, server) as PHP attributes,
 * since zircote/swagger-php needs at least one #[OA\Info] somewhere under
 * the scanned annotation path (config/l5-swagger.php: base_path('app')).
 */
#[OA\Info(
    version: '1.0.0',
    title: 'Nusatim Partner Portal API',
    description: 'REST API untuk aplikasi mobile Partner Portal PT. Nusantara Teknologi Inovasi Mandiri. Semua endpoint (kecuali auth/register/login) memerlukan Bearer token Sanctum yang didapat dari /api/v1/auth/login.'
)]
#[OA\Server(
    url: '/api/v1',
    description: 'Partner API v1'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum token'
)]
#[OA\Tag(name: 'Auth', description: 'Registrasi, login, logout partner')]
#[OA\Tag(name: 'Profile', description: 'Profil partner yang sedang login')]
#[OA\Tag(name: 'Dashboard', description: 'Ringkasan aktivitas & keuangan partner')]
#[OA\Tag(name: 'Leads', description: 'Lead & Opportunity')]
#[OA\Tag(name: 'Pipeline', description: 'Sales Pipeline (kanban per status lead)')]
#[OA\Tag(name: 'Customers', description: 'Customer Management')]
#[OA\Tag(name: 'Projects', description: 'Project Board')]
#[OA\Tag(name: 'Commissions', description: 'Commission Management (read-only)')]
#[OA\Tag(name: 'Withdrawals', description: 'Withdrawal komisi')]
#[OA\Tag(name: 'MarketingMaterials', description: 'Marketing Center')]
#[OA\Tag(name: 'SupportTickets', description: 'Support Ticket')]
#[OA\Tag(name: 'Notifications', description: 'Notification Center')]
class OpenApiInfo
{
    //
}
