<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    private function guardAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403);
    }

    public function index()
    {
        $this->guardAdmin();
        $users = User::with(['roles','permissions'])->get();
        $roles = Role::withCount('users')->orderBy('name')->get();
        $modules = config('mkwep_access.modules');
        $actions = config('mkwep_access.actions');
        return view('users.index', compact('users','roles','modules','actions'));
    }

    /** Admin tarafindan hesap olusturma; herkese acik Register akisi degismez. */
    public function storeUser(Request $request)
    {
        $this->guardAdmin();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);
        $roles = array_values(array_unique($data['roles'] ?? []));
        $user = DB::transaction(function () use ($data, $roles) {
            $user = User::create([
                'name' => $data['name'],
                'email' => mb_strtolower(trim($data['email'])),
                'password' => Hash::make($data['password']),
            ]);
            $user->syncRoles($roles ?: ['user']);
            return $user;
        });
        // Admin tarafindan eklenmis kullanici otomatik oturum acmaz.
        // E-posta dogrulama altyapisi kullaniyorsan event bildirim tetikler.
        event(new Registered($user));
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('users.index')->with('success',
            $user->name . ' kullanicisi olusturuldu. Modül izinlerini ayrıca atayabilirsiniz.');
    }

    public function updateRoles(Request $request, User $user)
    {
        $this->guardAdmin();
        $data = $request->validate(['roles'=>'array','roles.*'=>'string|exists:roles,name']);
        // Yetki panelini yönetebilmek için son admin'in yetkisini kaldırmayın.
        if ($user->is(auth()->user()) && !in_array('admin', $data['roles'] ?? [], true)) {
            return back()->withErrors(['roles'=>'Kendi yönetici rolünüzü kaldıramazsınız.']);
        }
        $user->syncRoles($data['roles'] ?? []);
        return back()->with('success','Kullanıcı rolleri güncellendi.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $this->guardAdmin();
        $moduleKeys = array_keys(config('mkwep_access.modules'));
        $actions = array_keys(config('mkwep_access.actions'));
        $valid = [];
        foreach ($moduleKeys as $key) foreach ($actions as $action) $valid[] = "mkwep.$key.$action";
        $data = $request->validate([
            'permissions'=>'sometimes|array',
            'permissions.*'=>['string',Rule::in($valid)],
        ]);
        $names = array_values(array_unique($data['permissions'] ?? []));
        // Sadece MKWeP izinlerini değiştir; var olan başka Spatie izinlerini silme.
        DB::transaction(function () use ($user,$names) {
            $other = $user->getDirectPermissions()->pluck('name')->reject(fn($name) => str_starts_with($name,'mkwep.'))->all();
            $user->syncPermissions(array_merge($other,$names));
        });
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success', $user->name.' için modül ve işlem izinleri kaydedildi.');
    }

    /** Yeni rol: mevcut roller ve kullanıcı atamaları korunur. */
    public function storeRole(Request $request)
    {
        $this->guardAdmin();
        $data = $request->validate([
            'name' => ['required','string','max:100','regex:/^[\pL\pN _.-]+$/u',
                       Rule::unique('roles','name')->where('guard_name','web')],
        ]);
        $name = trim($data['name']);
        if ($name === '') return back()->withErrors(['name'=>'Rol adı boş olamaz.']);
        Role::findOrCreate($name, 'web');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success', "{$name} rolü eklendi.");
    }

    /** Var olan bir rolü yeni adla kopyalar; kullanıcıları otomatik taşımaz. */
    public function cloneRole(Request $request, Role $role)
    {
        $this->guardAdmin();
        abort_unless($role->guard_name === 'web', 404);
        $data = $request->validate([
            'name' => ['required','string','max:100','regex:/^[\pL\pN _.-]+$/u',
                       Rule::unique('roles','name')->where('guard_name','web')],
        ]);
        $name = trim($data['name']);
        DB::transaction(function () use ($name, $role) {
            $newRole = Role::firstOrCreate(['name'=>$name, 'guard_name'=>'web']);
            // Spatie rol izinleri kopyalanır; kullanıcı atamaları kopyalanmaz.
            $newRole->syncPermissions($role->permissions);
        });
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success', "{$role->name} rolü {$name} olarak kopyalandı.");
    }

    public function renameRole(Request $request, Role $role)
    {
        $this->guardAdmin();
        abort_unless($role->guard_name === 'web', 404);
        if ($role->name === 'admin') return back()->withErrors(['role'=>'admin rolünün adı değiştirilemez.']);
        $data = $request->validate([
            'name' => ['required','string','max:100','regex:/^[\pL\pN _.-]+$/u',
                Rule::unique('roles','name')->where('guard_name','web')->ignore($role->id)],
        ]);
        $role->update(['name'=>trim($data['name'])]);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success','Rol adı güncellendi.');
    }

    public function destroyRole(Role $role)
    {
        $this->guardAdmin();
        abort_unless($role->guard_name === 'web', 404);
        if ($role->name === 'admin') return back()->withErrors(['role'=>'admin rolü silinemez.']);
        if ($role->users()->exists()) return back()->withErrors(['role'=>'Bu rol kullanıcılara atanmış. Önce kullanıcı atamalarını kaldırın.']);
        $role->delete();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success','Kullanılmayan rol silindi.');
    }

}
