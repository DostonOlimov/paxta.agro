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

    protected $table = 'applications';

    protected $fillable = [
        'crop_data_id',
        'organization_id',
        'prepared_id',
        'type',
        'date',
        'data',
        'status',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
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
        return $this->belongsTo(TestPrograms::class, 'id','app_id');
    }
    public function decision()
    {
        return $this->belongsTo(Decision::class, 'id','app_id');
    }
    public function comment()
    {
        return $this->belongsTo(AppStatusChanges::class, 'id','app_id');
    }
    public function files()
    {
        return $this->belongsTo(Files::class, 'id','app_id');
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
        $year = $new_date->format('Y');
        return $year;
    }

    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Ensure the user is authenticated
        $user = auth()->user();

        if ($user) {
            // Add global scope for filtering by user's state
            if ($user->branch_id == User::BRANCH_STATE) {
                $user_city = $user->state_id;

                static::addGlobalScope('cityStateScope', function ($query) use ($user_city) {
                    $query->whereHas('organization', function ($query) use ($user_city) {
                        $query->whereHas('city', function ($query) use ($user_city) {
                            $query->where('state_id', '=', $user_city);
                        });
                    });
                });
            }
            if ($user->crop_branch == User::CROP_BRANCH_CHIGIT) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('crops', function ($query) {
                        $query->where('name_id', '=', 2);
                    });
                });
            } elseif ($user->crop_branch == User::CROP_BRANCH_TOLA) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('crops', function ($query) {
                        $query->where('name_id', '=', 1);
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) {
            $query->where('status', '!=', self::STATUS_DELETED);
        });
    }


}
