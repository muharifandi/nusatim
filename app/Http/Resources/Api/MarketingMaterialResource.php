<?php

namespace App\Http\Resources\Api;

use App\Models\MarketingMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin MarketingMaterial
 */
#[OA\Schema(
    schema: 'MarketingMaterial',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'category', type: 'string'),
        new OA\Property(property: 'category_label', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'is_file_based', type: 'boolean'),
        new OA\Property(property: 'download_url', type: 'string', nullable: true),
        new OA\Property(property: 'content', type: 'string', nullable: true),
    ]
)]
class MarketingMaterialResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'category_label' => MarketingMaterial::CATEGORIES[$this->category] ?? $this->category,
            'description' => $this->description,
            'is_file_based' => $this->isFileBased(),
            'download_url' => $this->isFileBased() && $this->file_path ? asset($this->file_path) : null,
            'content' => ! $this->isFileBased() ? $this->content : null,
        ];
    }
}
