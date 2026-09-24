@extends('layouts.app')
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-tasks mr-1"></i>&nbsp; Joblar boshqaruvi
                </li>
            </ol>
        </div>
        <x-flash-message />

        <div class="row">
            <div class="col-md-3">
                <div class="card"><div class="card-body">
                    <div class="text-muted">Navbatda</div>
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body">
                    <div class="text-muted">Bajarilmoqda</div>
                    <h3 class="mb-0">{{ $stats['reserved'] }}</h3>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body">
                    <div class="text-muted">Xato bilan tugagan</div>
                    <h3 class="mb-0 text-danger">{{ $stats['failed'] }}</h3>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body">
                    <div class="text-muted">Queue ulanishi</div>
                    <h3 class="mb-0">{{ $stats['connection'] }}</h3>
                </div></div>
            </div>
        </div>

        {{-- pending jobs --}}
        <div class="card">
            <div class="card-header"><strong>Navbatdagi joblar</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered nowrap">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job</th>
                            <th>Queue</th>
                            <th>Urinishlar</th>
                            <th>Holati</th>
                            <th>Yaratilgan</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pending as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ class_basename($job->name) }}</td>
                                <td>{{ $job->queue }}</td>
                                <td>{{ $job->attempts }}</td>
                                <td>
                                    @if($job->reserved_at)
                                        <span class="badge bg-warning">Bajarilmoqda</span>
                                    @else
                                        <span class="badge bg-info">Kutmoqda</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($job->created_at)->format('d.m.Y H:i:s') }}</td>
                                <td>
                                    <form method="post" action="{{ route('jobs.pending.delete', $job->id) }}" class="d-inline"
                                          onsubmit="return confirm('Ushbu jobni navbatdan o\'chirasizmi?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Navbatda job yo'q</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pending->links() }}
            </div>
        </div>

        {{-- failed jobs --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <strong>Xato bilan tugagan joblar</strong>
                <div class="d-flex flex-wrap" style="gap: 6px;">
                    <form method="get" action="{{ route('jobs.index') }}" class="d-flex" style="gap: 6px;">
                        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Job yoki xato bo'yicha qidirish">
                        <button type="submit" class="btn btn-sm btn-secondary"><i class="fa fa-search"></i></button>
                    </form>
                    @if($stats['failed'])
                        <form method="post" action="{{ route('jobs.failed.retry_all') }}"
                              onsubmit="return confirm('Barcha xato joblarni qayta navbatga qo\'yasizmi?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> Hammasini qayta ishga tushirish</button>
                        </form>
                        <form method="post" action="{{ route('jobs.failed.flush') }}"
                              onsubmit="return confirm('Barcha xato joblar o\'chiriladi. Davom etasizmi?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hammasini o'chirish</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Job</th>
                            <th>Ulanish / Queue</th>
                            <th>Xato</th>
                            <th>Sana</th>
                            <th style="width: 150px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($failed as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ class_basename($job->name) }}</td>
                                <td>{{ $job->connection }} / {{ $job->queue }}</td>
                                <td><small>{{ \Illuminate\Support\Str::limit($job->error, 150) }}</small></td>
                                <td>{{ $job->failed_at }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('jobs.failed.show', $job->id) }}" class="btn btn-sm btn-info" title="Ko'rish"><i class="fa fa-eye"></i></a>
                                    <form method="post" action="{{ route('jobs.failed.retry', $job->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Qayta ishga tushirish"><i class="fa fa-refresh"></i></button>
                                    </form>
                                    <form method="post" action="{{ route('jobs.failed.delete', $job->id) }}" class="d-inline"
                                          onsubmit="return confirm('O\'chirasizmi?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="O'chirish"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Xato job yo'q</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $failed->links() }}
            </div>
        </div>
    </div>
@endsection
