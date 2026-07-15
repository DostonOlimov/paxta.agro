@extends('layouts.app')
@section('content')

<div class="section">
    <div class="page-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="fe fe-file-text mr-1"></i>&nbsp;Shartnomalar ro'yxati
            </li>
        </ol>
    </div>

    <x-flash-message />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="panel panel-primary">
                        <div class="tab_wrapper page-tab">
                            <ul class="tab_list">
                                <li class="active">
                                    <a href="{{ url('/sifat-contracts/list') }}">
                                        <i class="fa fa-list fa-lg">&nbsp;</i>
                                        {{ trans('app.Ro\'yxat') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/sifat-contracts/add') }}">
                                        <i class="fa fa-plus-circle fa-lg">&nbsp;</i>
                                        <b>{{ trans('app.Qo\'shish') }}</b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Search bar --}}
                    <form method="GET" action="{{ url('/sifat-contracts/list') }}" class="mb-3 d-flex" style="gap:8px; max-width:450px;">
                        <input type="text" name="search" class="form-control"
                               placeholder="Shartnoma raqami, korxona nomi yoki STIR..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ url('/sifat-contracts/list') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered nowrap" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Shartnoma raqami</th>
                                    <th>Korxona nomi</th>
                                    <th>Korxona STIRi</th>
                                    <th>Sana</th>
                                    <th>Shartnoma fayli</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($contracts as $contract)
                                <tr>
                                    <td>{{ $contracts->firstItem() + $loop->index }}</td>
                                    <td>{{ $contract->number }}</td>
                                    <td>{{ optional($contract->organization)->name }}</td>
                                    <td>{{ optional($contract->organization)->inn }}</td>
                                    <td>{{ $contract->date ? \Carbon\Carbon::parse($contract->date)->format('d.m.Y') : '—' }}</td>
                                    <td>
                                        @if($contract->attachment)
                                            <a href="{{ route('attachment.download', ['id' => $contract->attachment->id]) }}" class="text-azure">
                                                <i class="fa fa-download"></i> Yuklab olish
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Ma'lumot topilmadi</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $contracts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
