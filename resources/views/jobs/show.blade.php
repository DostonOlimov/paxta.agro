@extends('layouts.app')
@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('jobs.index') }}"><i class="fa fa-tasks mr-1"></i>&nbsp; Joblar boshqaruvi</a>
                </li>
                <li class="breadcrumb-item">#{{ $job->id }}</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $job->name }}</strong>
                <div>
                    <form method="post" action="{{ route('jobs.failed.retry', $job->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> Qayta ishga tushirish</button>
                    </form>
                    <form method="post" action="{{ route('jobs.failed.delete', $job->id) }}" class="d-inline"
                          onsubmit="return confirm('O\'chirasizmi?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> O'chirish</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-1"><b>UUID:</b> {{ $job->uuid }}</p>
                <p class="mb-1"><b>Ulanish / Queue:</b> {{ $job->connection }} / {{ $job->queue }}</p>
                <p><b>Sana:</b> {{ $job->failed_at }}</p>

                <h6>Xato</h6>
                <pre style="max-height: 400px; overflow: auto; white-space: pre-wrap;">{{ $job->exception }}</pre>

                <h6>Payload</h6>
                <pre style="max-height: 300px; overflow: auto; white-space: pre-wrap;">{{ json_encode(json_decode($job->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
@endsection
