<?php


namespace App\Models;


use App\Models\Contracts\CropDataInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CropData extends Model implements CropDataInterface
{
    // Constants grouped by category with type hints in PHPDoc
    /** @var int Meausre types */
    public const MEASURE_TYPE_TONNA= 1;
    public const MEASURE_TYPE_KG = 2;

    /** @var string Table name */
    protected $table = 'crop_data';

    /** @var array Mass assignable fields */
    protected $fillable = [
        'name_id',
        'kodtnved',
        'party_number',
        'measure_type',
        'amount',
        'year',
        'sxeme_number',
        'toy_count',
        'country_id',
        'selection_code'
    ];

    // Relationships
    public function name(): BelongsTo
    {
        return $this->belongsTo(CropsName::class, 'name_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CropsType::class, 'type_id');
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(CropsGeneration::class, 'generation_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function selection(): BelongsTo
    {
        return $this->belongsTo(CropsSelection::class, 'selection_code');
    }

    public function application(): HasOne
    {
        return $this->hasOne(Application::class,'id','crop_data_id');
    }

    /**
     * Get measure types as an array or a specific type's name
     *
     * @param int|null $type
     */
    public static function getMeasureType(?int $type = null)
    {
        $measures = [
            self::MEASURE_TYPE_TONNA => 'tonna',
            self::MEASURE_TYPE_KG => 'kg',
        ];

        return $type === null ? $measures : ($measures[$type] ?? null);
    }
    /**
     * Get available years as an array or a specific year
     *
     * @param int|null $year
     */
    public static function getYear(?int $year = null)
    {
        $years = range(2018, (int) date('Y')); // Dynamically generate years up to current year

        // Convert to associative array where keys match values
        $years = array_combine($years, $years);

        return $year === null ? $years : ($years[$year] ?? null);
    }
    /**
     * Get the formatted amount with measure type
     *
     * @return string|null
     */
    public function getAmountNameAttribute(): ?string
    {
        if ($this->amount > 0) { // More explicit check for valid amount
            $measure = self::getMeasureType($this->measure_type);
            return $measure ? "{$this->amount} {$measure}" : null;
        }

        return null;
    }
}
