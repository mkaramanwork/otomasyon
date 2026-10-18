@extends('layouts.admin')
@section('title','Kullanıcı Yönetimi')
@section('content')
<div class="container">
    <h4>👥 Kullanıcı Listesi</h4>
<a href="{{ route('mkwep.menus.index') }}" class="btn btn-outline-primary mb-3">Menü Yönetimi</a>

    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-success mb-4">
      <div class="card-header"><strong><i class="bi bi-person-plus"></i> Yeni Kullanıcı Ekle</strong></div>
      <div class="card-body">
        <p class="text-muted small">Register sayfasi da acik kalir. Buradan olusturulan hesapta oturum acilmaz; yeni kullanicinin modül yetkilerini sonradan belirleyebilirsiniz.</p>
        <form method="POST" action="{{ route('users.store') }}" autocomplete="off">
          @csrf
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="mkwep-new-name">Ad Soyad</label><input id="mkwep-new-name" name="name" class="form-control" maxlength="255" required value="{{ old('name') }}"></div>
            <div class="col-md-6"><label class="form-label" for="mkwep-new-email">E-posta</label><input id="mkwep-new-email" name="email" type="email" class="form-control" maxlength="255" required value="{{ old('email') }}"></div>
            <div class="col-md-6"><label class="form-label" for="mkwep-new-password">Gecici Sifre</label><input id="mkwep-new-password" name="password" type="password" class="form-control" required autocomplete="new-password"></div>
            <div class="col-md-6"><label class="form-label" for="mkwep-new-password-confirmation">Sifre Tekrari</label><input id="mkwep-new-password-confirmation" name="password_confirmation" type="password" class="form-control" required autocomplete="new-password"></div>
            <div class="col-12"><label class="form-label">Baslangic Rolleri (birden fazla secilebilir)</label>
              <div class="row g-2">
                @foreach($roles as $role)
                  <div class="col-md-4 col-lg-3"><label class="border rounded p-2 d-flex gap-2 align-items-center h-100"><input type="checkbox" class="form-check-input mt-0" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', ['user']), true))><span>{{ $role->name }}</span></label></div>
                @endforeach
              </div>
              <div class="form-text">Rol secilmezse otomatik user atanir. Admin rolünü yalnizca yetkili kisilere verin.</div>
            </div>
            <div class="col-12"><button class="btn btn-success" type="submit"><i class="bi bi-person-plus"></i> Kullaniciyi Olustur</button></div>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-primary mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-person-badge"></i> Rol Yönetimi</strong>
        <span class="badge text-bg-secondary">{{ $roles->count() }} rol</span>
      </div>
      <div class="card-body">
        <p class="small text-muted">Yeni rol ekleyebilir, kopyalayabilir ve bir kullanıcıya birden fazla rol atayabilirsin. Mevcut Roller ve kullanıcı atamaları korunur.</p>
        <form method="POST" action="{{ route('users.roles.store') }}" class="row g-2 mb-3">
          @csrf
          <div class="col-md-9"><input name="name" class="form-control" maxlength="100" placeholder="Yeni rol adı (ör. kalite müdürü)" required></div>
          <div class="col-md-3"><button class="btn btn-success w-100" type="submit"><i class="bi bi-plus-circle"></i> Yeni Rol Ekle</button></div>
        </form>
        <div class="table-responsive"><table class="table table-sm table-hover align-middle">
          <thead><tr><th>Rol</th><th class="text-center">Kullanıcı</th><th>Kopyala</th><th>Adını Değiştir</th><th class="text-end">Sil</th></tr></thead>
          <tbody>
          @foreach($roles as $role)
          <tr>
            <td><strong>{{ $role->name }}</strong>@if($role->name === 'admin') <span class="badge text-bg-info">Korunan rol</span>@endif</td>
            <td class="text-center">{{ $role->users_count }}</td>
            <td><form method="POST" action="{{ route('users.roles.clone', $role->id) }}" class="d-flex gap-1">@csrf
              <input name="name" class="form-control form-control-sm" placeholder="Kopya adı" required maxlength="100" aria-label="Kopya rol adı">
              <button type="submit" class="btn btn-sm btn-outline-primary">Kopyala</button></form></td>
            <td>@if($role->name !== 'admin')<form method="POST" action="{{ route('users.roles.rename', $role->id) }}" class="d-flex gap-1">@csrf @method('PUT')
              <input name="name" class="form-control form-control-sm" value="{{ $role->name }}" required maxlength="100" aria-label="Rol adını değiştir">
              <button type="submit" class="btn btn-sm btn-outline-secondary">Kaydet</button></form>@else <span class="text-muted">Sabit</span> @endif</td>
            <td class="text-end">@if($role->name !== 'admin' && $role->users_count == 0)
              <form method="POST" action="{{ route('users.roles.destroy', $role->id) }}" onsubmit="return confirm('Bu rolü silmek istiyor musun?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Sil</button></form>
              @else <span class="text-muted small">Korunuyor</span> @endif</td>
          </tr>
          @endforeach
          </tbody></table></div>
      </div>
    </div>

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

                                         <button class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#accessModal{{ $user->id }}">Modül / İşlem Yetkileri</button>
                     <div class="modal fade" id="accessModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                       <div class="modal-dialog modal-xl modal-dialog-scrollable">
                         <div class="modal-content">
                           <form action="{{ route('users.updatePermissions',$user->id) }}" method="POST">
                             @csrf @method('PUT')
                             <div class="modal-header"><h5 class="modal-title">{{ $user->name }} — Kullanıcı Yetkileri</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                             <div class="modal-body">
                               <p class="text-muted small">Seçilen izinler yalnızca bu kullanıcıya aittir. Menüler ve route erişimi birlikte denetlenir. Admin rolü tam erişim sahibidir.</p>
                               <div class="d-flex flex-wrap gap-2 mb-2"><button type="button" class="btn btn-sm btn-outline-success" onclick="mkwepSelectBoxes('accessModal{{ $user->id }}',true)">Tümünü Ekle</button><button type="button" class="btn btn-sm btn-outline-danger" onclick="mkwepSelectBoxes('accessModal{{ $user->id }}',false)">Tümünü Kaldır</button></div>
                               <div class="table-responsive" style="max-height:60vh"><table class="table table-bordered table-sm align-middle">
                                 <thead><tr><th>Modül</th>@foreach($actions as $actionLabel)<th class="text-center">{{ $actionLabel }}</th>@endforeach</tr></thead>
                                 <tbody>
                                 @foreach($modules as $key=>$module)
                                   <tr><th>{{ $module['label'] }}</th>
                                   @foreach($actions as $action=>$label)
                                    @php $permissionName = "mkwep.$key.$action"; @endphp
                                    <td class="text-center"><input type="checkbox" name="permissions[]" value="{{ $permissionName }}" class="form-check-input" @checked($user->hasDirectPermission($permissionName))></td>
                                   @endforeach
                                   </tr>
                                 @endforeach
                                 </tbody>
                               </table></div>
                             </div>
                             <div class="modal-footer"><button type="submit" class="btn btn-success">Yetkileri Kaydet</button></div>
                           </form>
                         </div>
                       </div>
                     </div>
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
                              <div class="d-flex gap-2 mb-2"><button type="button" class="btn btn-sm btn-outline-success" onclick="mkwepSelectRoles('roleModal{{ $user->id }}',true)">Tüm Rolleri Ekle</button><button type="button" class="btn btn-sm btn-outline-danger" onclick="mkwepSelectRoles('roleModal{{ $user->id }}',false)">Tümünü Kaldır</button></div>
                              <select name="roles[]" class="form-select" multiple size="10">
                                  @foreach($roles as $role)
                                      <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                          {{ $role->name }}
                                      </option>
                                  @endforeach
                              </select>
                              <small class="text-muted">Birden fazla rol seçebilirsin. Seçtikten sonra Kaydet butonuna bas.</small>
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
<script>
function mkwepSelectBoxes(id,checked){document.getElementById(id).querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(e=>e.checked=checked);}
function mkwepSelectRoles(id,selected){document.getElementById(id).querySelectorAll('select[name="roles[]"] option').forEach(e=>e.selected=selected);}
</script>
@endsection
