<?php

namespace App\Models;

use Database\Factories\VisualizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Acesso a um link encurtado.
 *
 * @property int $visualization_id
 * @property int $url_id
 * @property Carbon $access_date_hour
 * @property array{os: string, browser: string, device: string}|null $device_info
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Visualization extends Model
{
    /** @use HasFactory<VisualizationFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $primaryKey = 'visualization_id';

    /** @var list<string> */
    protected $fillable = [
        'url_id',
        'access_date_hour',
        'device_info',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_date_hour' => 'datetime',
            'device_info' => 'array',
        ];
    }

    /**
     * @return BelongsTo<ShortUrl, $this>
     */
    public function shortUrl(): BelongsTo
    {
        return $this->belongsTo(ShortUrl::class, 'url_id', 'url_id');
    }
}
