<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//queue jobs control, only for super admins (config app.super_admin_ids)
class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::user() && Auth::user()->isSuperAdmin(), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $pending = DB::table('jobs')->orderBy('id')->paginate(20, ['*'], 'pending_page');
        $pending->getCollection()->transform(function ($job) {
            $job->name = $this->jobName($job->payload);
            return $job;
        });

        $failed = DB::table('failed_jobs')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payload', 'like', '%' . $search . '%')
                        ->orWhere('exception', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(20, ['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'], 'failed_page')
            ->withQueryString();
        $failed->getCollection()->transform(function ($job) {
            $job->name = $this->jobName($job->payload);
            $job->error = strtok($job->exception, "\n");
            return $job;
        });

        $stats = [
            'pending' => DB::table('jobs')->count(),
            'reserved' => DB::table('jobs')->whereNotNull('reserved_at')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'connection' => config('queue.default'),
        ];

        return view('jobs.index', compact('pending', 'failed', 'stats', 'search'));
    }

    public function showFailed($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();
        abort_unless($job, 404);

        $job->name = $this->jobName($job->payload);

        return view('jobs.show', compact('job'));
    }

    public function retry($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();
        abort_unless($job, 404);

        Artisan::call('queue:retry', ['id' => [$job->uuid]]);

        return redirect()->route('jobs.index')->with('message', 'Job qayta navbatga qo\'yildi');
    }

    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->route('jobs.index')->with('message', 'Barcha xato joblar qayta navbatga qo\'yildi');
    }

    public function forget($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->back()->with('message', 'Xato job o\'chirildi');
    }

    public function flush()
    {
        $count = DB::table('failed_jobs')->delete();

        return redirect()->route('jobs.index')->with('message', $count . ' ta xato job o\'chirildi');
    }

    public function deletePending($id)
    {
        DB::table('jobs')->where('id', $id)->delete();

        return redirect()->route('jobs.index')->with('message', 'Navbatdagi job o\'chirildi');
    }

    private function jobName($payload)
    {
        $data = json_decode($payload, true);

        return $data['displayName'] ?? ($data['job'] ?? '-');
    }
}
