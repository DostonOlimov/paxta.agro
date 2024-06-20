<?php
namespace App\Http\Controllers\Traits;

use App\Models\Dalolatnoma;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait DalolatnomaTrait
{
    public function buildQuery(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $sort_by = $request->get('sort_by', 'id'); // default sorting by 'id'
        $sort_order = $request->get('sort_order', 'desc'); // default order is ascending

        // Validate the sort_by column to prevent SQL injection
        $columns = ['id','number', 'party_number', 'date','prepared']; // Add your table columns here
        if (!in_array($sort_by, $columns)) {
            $sort_by = 'id';
        }

        $apps = Dalolatnoma::with('test_program')
            ->with('test_program.application')
            ->with('test_program.application.decision')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.organization');

        if ($user->branch_id == User::BRANCH_STATE) {
            $user_city = $user->state_id;
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }

        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }

        if ($city) {
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }

        if ($crop) {
            $apps = $apps->whereHas('test_program.application.crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }

        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->whereHas('test_program.application', function ($query) use ($searchQuery) {
                        $query->where('app_number', $searchQuery);
                    });
                } else {
                    $query->whereHas('test_program.application.crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });
                }
            });
        });
        if ($sort_by == 'prepared') {
            $apps->join('test_programs', 'dalolatnoma.test_program_id', '=', 'test_programs.id')
                ->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->join('prepared_companies', 'applications.prepared_id', '=', 'prepared_companies.id')
                ->orderBy('prepared_companies.name', $sort_order);
        } elseif ($sort_by == 'party_number') {
            $apps->join('test_programs', 'dalolatnoma.test_program_id', '=', 'test_programs.id')
                ->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
                ->orderBy('crop_data.party_number', $sort_order);
        }elseif ($sort_by == 'number') {
            $apps->join('test_programs', 'dalolatnoma.test_program_id', '=', 'test_programs.id')
                ->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->join('decisions', 'decisions.app_id', '=', 'applications.id')
                ->orderBy('decisions.number', $sort_order);
        }
        else{
            $apps->orderBy($sort_by, $sort_order);
        }

        return $apps;
    }
}
