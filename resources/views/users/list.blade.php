@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h2>Foydalanuvchilar</h2>

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Ismi</th>
                                <th>Familiyasi</th>
                                <th>User_name</th>
                                <th>Roli</th>
                                <th>Yaratilgan vaqti</th>
                                <th>O'zgartirilgan vaqti</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ strchr($user->name,' ',true) }}</td>
                                    <td>{{ strchr($user->name,' ') }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->roles->name ?? '' }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>{{ $user->updated_at }}</td>
                                    <td>
                                        <form action="{{ route('users.destroy',$user->id) }}" method="Post">
                                            <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Tahrirlash</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">O'chirish</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $users->links() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
