<?php


namespace App\Models;


use App\Models\Contracts\ApplicationInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\ApplicationObserver;
use Illuminate\Database\Eloquent\Model;

class Application extends Model implements ApplicationInterface
{
// Constants grouped by category with type hints in PHPDoc
    /** @var int Application types */
    public const TYPE_1 = 1; // Local production
    public const TYPE_2 = 2; // Imported products
    public const TYPE_3 = 3; // Old crop

    /** @var int Status codes */
    public const STATUS_NEW = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_ACCEPTED = 3;
    public const STATUS_FINISHED = 4;
    public const STATUS_DELETED = 5;

    /** @var int Progress stages */
    public const PROGRESS_INITIAL = 1;
    public const PROGRESS_ANSWERED = 2;
    public const PROGRESS_DECISION = 3;
    public const PROGRESS_EXAMPLE = 4;
    public const PROGRESS_LABORATORY = 5;
    public const PROGRESS_CONCLUSION = 6;
    public const PROGRESS_FINISHED = 7;

    /** @var string Table name */
    protected $table = 'applications';

    /** @var array Mass assignable fields */
    protected $fillable = [
        'crop_data_id',
        'organization_id',
        'prepared_id',
        'type',
        'date',
        'data',
        'status',
        'app_type',
        'created_by',
    ];

    /** @var array Attributes to append to the model's array form */
    protected $appends = ['status_name', 'status_color'];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function crops(): BelongsTo
    {
        return $this->belongsTo(CropData::class, 'crop_data_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationCompanies::class, 'organization_id');
    }

    public function prepared(): BelongsTo
    {
        return $this->belongsTo(PreparedCompanies::class, 'prepared_id');
    }

    public function tests(): HasOne
    {
        return $this->hasOne(TestPrograms::class, 'app_id', 'id');
    }

    public function decision(): HasOne
    {
        return $this->hasOne(Decision::class, 'app_id', 'id');
    }

    public function comment(): HasOne
    {
        return $this->hasOne(AppStatusChanges::class, 'app_id', 'id');
    }

    public function files(): HasOne
    {
        return $this->hasOne(Files::class, 'app_id', 'id');
    }

    public function client_data(): HasOne
    {
        return $this->hasOne(ClientData::class, 'app_id', 'id');
    }

    public function chigit_result(): HasMany
    {
        return $this->hasMany(ChigitResult::class, 'app_id', 'id');
    }

    public function sifat_sertificate(): HasOne
    {
        return $this->hasOne(SifatSertificates::class, 'app_id', 'id');
    }

    // Type and Status Methods
    public function getId(): int
    {
        return $this->id;
    }

    // Type and Status Methods
    public static function getType(?int $type = null)
    {
        $types = [
            self::TYPE_1 => 'Maxalliy ishlab chiqarish uchun',
            self::TYPE_2 => 'Import qilingan mahsulotlar uchun',
            self::TYPE_3 => 'Eski hosil uchun',
        ];

        return $type === null ? $types : ($types[$type] ?? 'Unknown Type');
    }

    public static function getStatus(?int $status = null)
    {
        $statuses = [
            self::STATUS_NEW => 'Yangi ariza',
            self::STATUS_ACCEPTED => 'Qabul qilingan',
            self::STATUS_REJECTED => 'Rad etilgan',
            self::STATUS_FINISHED => 'Yakunlangan',
            self::STATUS_DELETED => "O'chirilgan",
        ];

        return $status === null ? $statuses : ($statuses[$status] ?? 'Unknown Status');
    }
    // Accessors
    public function getStatusNameAttribute(): string
    {
        return self::getStatus($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return [
                self::STATUS_NEW => 'warning',
                self::STATUS_REJECTED => 'danger',
                self::STATUS_ACCEPTED => 'success',
                self::STATUS_DELETED => 'danger',
                self::STATUS_FINISHED => 'secondary',
            ][$this->status] ?? 'default';
    }


    // Helper Methods
    public function getYear(): string
    {
        $date = \DateTime::createFromFormat('Y-m-d', $this->date);
        return $date ? $date->format('Y') : date('Y');
    }

    // Global Scopes and Boot Method
    protected static function booted(): void
    {
        parent::boot();
        static::observe(ApplicationObserver::class);

        $year = getCurrentYear();
        $crop = getApplicationType();

        if ($user = auth()->user()) {
            if ($user->branch_id === User::BRANCH_STATE) {
                static::addGlobalScope('cityStateScope', function ($query) use ($user) {
                    $query->whereHas('prepared', fn($q) => $q->where('state_id', $user->state_id));
                });
            }
        }

        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year, $crop) {
            $query->where('status', '!=', self::STATUS_DELETED)
                ->where('app_type', $crop)
                ->whereHas('crops', fn($q) => $q->where('year', $year));
        });
    }
}
