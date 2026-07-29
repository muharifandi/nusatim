<?php

namespace App\Models;

use App\Models\Concerns\DeletesOldFiles;
use Illuminate\Database\Eloquent\Model;

class MarketingMaterial extends Model
{
    use DeletesOldFiles;

    public const CATEGORIES = [
        'brosur' => 'Brosur',
        'company_profile' => 'Company Profile',
        'price_list' => 'Price List',
        'proposal' => 'Proposal',
        'logo' => 'Logo',
        'banner' => 'Banner',
        'video' => 'Video',
        'template_whatsapp' => 'Template WhatsApp',
        'template_email' => 'Template Email',
        'faq' => 'FAQ',
        'selling_point' => 'Selling Point',
    ];

    /**
     * Categories whose main asset is an uploaded file. The rest
     * (templates, FAQ, selling point) are plain text a partner copies.
     */
    public const FILE_CATEGORIES = [
        'brosur',
        'company_profile',
        'price_list',
        'proposal',
        'logo',
        'banner',
        'video',
    ];

    protected $fillable = [
        'title',
        'category',
        'description',
        'file_path',
        'content',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isFileBased(): bool
    {
        return in_array($this->category, self::FILE_CATEGORIES);
    }

    protected function fileFields(): array
    {
        return ['file_path'];
    }
}
