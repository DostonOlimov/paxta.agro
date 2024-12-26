<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;

    const STATUS_NEW = 1;
    const STATUS_REJECTED = 2;
    const STATUS_ACCEPTED = 3;
    const STATUS_FINISHED = 4;
    const STATUS_DELETED = 5;

    const PROGRESS_INITIAL = 1;
    const PROGRESS_ANSWERED = 2;
    const PROGRESS_DECISION = 3;
    const PROGRESS_EXAMPLE = 4;
    const PROGRESS_LABORATORY = 5;
    const PROGRESS_CONCLUSION = 6;
    const PROGRESS_FINISHED = 7;

    public $table = 'applications';

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

    public function user()
    {
        return $this->belongsTo(User::class,'created_by', 'id');
    }

    public function crops()
    {
        return $this->belongsTo(CropData::class, 'crop_data_id');
    }

    public function organization()
    {
        return $this->belongsTo(OrganizationCompanies::class, 'organization_id');
    }

    public function prepared()
    {
        return $this->belongsTo(PreparedCompanies::class, 'prepared_id');
    }
    public function tests()
    {
        return $this->hasOne(TestPrograms::class, 'app_id', 'id');
    }
    public function decision()
    {
        return $this->hasOne(Decision::class,'app_id', 'id');
    }
    public function comment()
    {
        return $this->hasOne(AppStatusChanges::class,'app_id', 'id');
    }
    public function files()
    {
        return $this->hasOne(Files::class,'app_id', 'id');
    }
    public function client_data()
    {
        return $this->hasOne(ClientData::class,'app_id','id');
    }
    public function chigit_result()
    {
        return $this->hasMany(ChigitResult::class,'app_id','id');
    }
    public function sifat_sertificate()
    {
        return $this->hasOne(SifatSertificates::class,'app_id','id');
    }

    public static function getType($type = null)
    {
        $arr = [
            self::TYPE_1 => 'Maxalliy ishlab chiqarish uchun',
            self::TYPE_2 => 'Import qilingan mahsulotlar uchun',
            self::TYPE_3 => 'Eski hosil uchun',
        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public static function getStatus($type = null)
    {
        $arr = [
            self::STATUS_NEW => 'Yangi ariza',
            self::STATUS_ACCEPTED => 'Qabul qilingan',
            self::STATUS_REJECTED => 'Rad etilgan',
            self::STATUS_FINISHED => 'Yakunlangan',
            self::STATUS_DELETED => 'O\'chirilgan',
        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public function getStatusNameAttribute(){
        return self::getStatus($this->status);
    }
    public function getStatusColorAttribute(){
         if($this->status == self::STATUS_NEW){
             return 'warning';
        }elseif($this->status == self::STATUS_REJECTED){
             return 'danger';
         }elseif($this->status == self::STATUS_ACCEPTED){
             return 'success';
         }elseif($this->status == self::STATUS_DELETED){
             return 'danger';
         }elseif($this->status == self::STATUS_FINISHED){
             return 'secondary';
         }
    }

    public function getYear()
    {
        $new_date = \DateTime::createFromFormat("Y-m-d", $this->date);
        return $new_date->format('Y');
    }

    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Retrieve year and crop from session or use defaults
        $year = session('year', date("Y"));
        $crop = session('crop', 1);

        // Ensure the user is authenticated
        if ($user = auth()->user()) {
            // Add global scope for filtering by user's state if in branch state
            if ($user->branch_id == User::BRANCH_STATE) {
                static::addGlobalScope('cityStateScope', function ($query) use ($user) {
                    $query->whereHas('prepared', function ($query) use ($user) {
                        $query->where('state_id', $user->state_id);
                    });
                });
            }
        }

        // Add global scope to exclude deleted status and filter crops
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year, $crop) {
            $query->where('status', '!=', self::STATUS_DELETED)
                ->where('app_type', '=', $crop)
                ->whereHas('crops', function ($query) use ($year) {
                    $query->where('year', $year);
                });
        });
    }
}
