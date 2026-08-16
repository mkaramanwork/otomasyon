@extends('layouts.admin')
@section('title','Kullanıcı Yönetimi')
@section('content')
<div class="container">
    <h4>👥 Kullanıcı Listesi</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ad</th>
                <th>Email</th>
                <th>Roller</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->getRoleNames()->isNotEmpty())
                        {{ $user->getRoleNames()->implode(', ') }}
                    @else
                        <span class="text-muted">Rol yok</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}">
                        Rol Güncelle
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="{{ route('users.updateRoles',$user->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $user->name }} için Rol Güncelle</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <select name="roles[]" class="form-select" multiple>
                                  @foreach($roles as $role)
                                      <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                          {{ $role->name }}
                                      </option>
                                  @endforeach
                              </select>
                              <small class="text-muted">CTRL veya CMD ile birden fazla rol seçebilirsin.</small>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Kaydet</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Henüz kullanıcı yok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
