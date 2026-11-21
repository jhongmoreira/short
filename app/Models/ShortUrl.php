<?php

namespace App\Models;

use Database\Factories\ShortUrlFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Link encurtado (tabela `urls`).
 *
 * @property int $url_id
 * @property string $original_link
 * @property string $short_url
 * @property string $pin_manager
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ShortUrl extends Model
{
    /** @use HasFactory<ShortUrlFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'urls';

    protected $primaryKey = 'url_id';

    /** @var list<string> */
    protected $fillable = [
        'original_link',
        'short_url',
        'pin_manager',
    ];

    /** @var list<string> */
    protected $hidden = [
        'pin_manager',
    ];

    public function getRouteKeyName(): string
    {
        return 'short_url';
    }

    /**
     * @return HasMany<Visualization, $this>
     */
    public function visualizations(): HasMany
    {
        return $this->hasMany(Visualization::class, 'url_id', 'url_id');
    }

    /**
     * URL curta completa, respeitando o domínio/subdomínio atual.
     */
    public function fullShortUrl(): string
    {
        return url($this->short_url);
    }
}
