<?php
namespace App\Http\Controllers;

use App\Support\MkwepAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MkwepMenuController extends Controller
{
    private function admin(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403);
    }

    private function parents()
    {
        return DB::table('mkwep_menu_items')->where('item_type', 'parent')->orderBy('sort_order')->get();
    }

    /** Routes usable as global navigation links, never record-specific URLs. */
    private function routeChoices(): array
    {
        $defaults = config('mkwep_menu_routes.defaults', []);
        $choices = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name || !in_array('GET', $route->methods(), true)) continue;
            // The navigation page is for read-only pages, not action endpoints.
            if (MkwepAccess::actionForRoute($name, ['GET']) !== 'view') continue;
            if (preg_match('/(delete|destroy|approve|reject|complete|logout|export|download|send|mail|verify|qr|print|pdf)/i', $name)) continue;
            $parameters = $route->parameterNames();
            $resolved = $defaults[$name] ?? [];
            if (!is_array($resolved)) continue;
            $required = [];
            foreach ($parameters as $param) {
                if (!str_contains($route->uri(), '{'.$param.'?}')) $required[] = $param;
            }
            if (array_diff($required, array_keys($resolved))) continue;
            try { route($name, $resolved); }
            catch (\Throwable $e) { continue; }
            $choices[$name] = $name;
        }
        ksort($choices, SORT_NATURAL);
        return array_values($choices);
    }

    public function index()
    {
        $this->admin();
        $menus = DB::table('mkwep_menu_items')->orderBy('sort_order')->orderBy('id')->get();
        $parents = $this->parents();
        $routes = $this->routeChoices();
        return view('users.menus', compact('menus', 'parents', 'routes'));
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'label' => 'required|string|max:150',
            'item_type' => ['required', Rule::in(['header', 'parent', 'link'])],
            'parent_key' => 'nullable|string|max:110',
            'route_name' => 'nullable|string|max:160',
            'icon' => ['nullable','string','max:100','regex:/^bi-[a-z0-9-]+$/'],
            'sort_order' => 'required|integer|min:0|max:100000',
            'is_active' => 'sometimes|boolean',
        ]);
        $kind = $data['item_type'];
        $parent = $data['parent_key'] ?? null;
        $parent = $parent === '' ? null : $parent;
        if ($parent !== null) {
            $parentRow = DB::table('mkwep_menu_items')->where('item_key', $parent)->where('item_type', 'parent')->first();
            if (!$parentRow || ($id !== null && (int)$parentRow->id === $id) || $kind !== 'link') {
                throw ValidationException::withMessages(['parent_key' => 'Geçerli bir açılır üst menü seçin.']);
            }
        }
        $routeName = trim((string)($data['route_name'] ?? ''));
        $params = [];
        if ($kind === 'link') {
            if (!in_array($routeName, $this->routeChoices(), true)) {
                throw ValidationException::withMessages(['route_name' => 'Genel menüye uygun bir sayfa seçin. Kayıt ID isteyen veya işlem yapan route eklenemez.']);
            }
            $params = config('mkwep_menu_routes.defaults.'.$routeName, []);
            // Never take URL parameters from the browser. Preserve legacy fixed
            // parameters when editing an existing route that is unchanged.
            if ($id !== null) {
                $old = DB::table('mkwep_menu_items')->where('id', $id)->first();
                if ($old && $old->route_name === $routeName && !isset(config('mkwep_menu_routes.defaults', [])[$routeName])) {
                    $raw = $old->route_params;
                    for ($i = 0; $i < 2 && is_string($raw); $i++) {
                        $decoded = json_decode($raw, true);
                        if (json_last_error() !== JSON_ERROR_NONE) break;
                        $raw = $decoded;
                    }
                    if (is_array($raw) && !array_is_list($raw)) $params = $raw;
                }
            }
            try { route($routeName, $params); }
            catch (\Throwable $e) {
                throw ValidationException::withMessages(['route_name' => 'Seçilen sayfanın bağlantısı oluşturulamadı.']);
            }
        }
        return [
            'label' => trim($data['label']), 'item_type' => $kind,
            'parent_key' => $parent,
            'route_name' => $kind === 'link' ? $routeName : null,
            'route_params' => $kind === 'link' && $params ? json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'module_key' => $kind === 'link' ? MkwepAccess::moduleForRoute($routeName) : null,
            'icon' => $data['icon'] ?: 'bi-circle',
            'sort_order' => (int)$data['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ];
    }

    public function store(Request $request)
    {
        $this->admin();
        $data = $this->validated($request);
        // Yeni kayıtlarda aktif kutusu varsayılan olarak açık gelir.
        $data['item_key'] = 'custom-' . \Illuminate\Support\Str::uuid()->toString();
        $data['created_at'] = now();
        DB::table('mkwep_menu_items')->insert($data);
        return back()->with('success','Yeni menü eklendi.');
    }

    public function update(Request $request, int $menu)
    {
        $this->admin();
        $row = DB::table('mkwep_menu_items')->where('id', $menu)->first();
        abort_unless($row,404);
        $data = $this->validated($request, $menu);
        // V4 kayıtlarının anahtarları korunur: alt menü bağları kopmaz.
        if ($row->item_type === 'parent' && $data['item_type'] !== 'parent' &&
            DB::table('mkwep_menu_items')->where('parent_key',$row->item_key)->exists()) {
            throw ValidationException::withMessages(['item_type'=>'Alt menüleri olan grubu önce boşaltın.']);
        }
        DB::table('mkwep_menu_items')->where('id',$menu)->update($data);
        return back()->with('success','Menü ayarları kaydedildi.');
    }

    public function move(Request $request, int $menu)
    {
        $this->admin();
        $request->validate(['direction'=>['required',Rule::in(['up','down'])]]);
        DB::transaction(function () use ($menu,$request) {
            $item=DB::table('mkwep_menu_items')->where('id',$menu)->lockForUpdate()->first();
            abort_unless($item,404);
            $siblings=DB::table('mkwep_menu_items')->where('parent_key',$item->parent_key);
            if ($item->parent_key === null) $siblings->whereNull('parent_key');
            $rows=$siblings->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
            $i=$rows->search(fn($r)=>(int)$r->id===$menu);
            $j=$i+($request->direction==='up' ? -1 : 1);
            if ($i===false || $j<0 || $j>=$rows->count()) return;
            // Sıra değerleri aynı olabileceği için kardeşleri aralıklı yeniden numaralandır.
            $ids=$rows->pluck('id')->all();
            [$ids[$i],$ids[$j]]=[$ids[$j],$ids[$i]];
            // Diğer modüllerin sıralama ölçeğine dokunmadan, iki komşunun yerini değiştir.
            $a=$rows[$i]; $b=$rows[$j];
            if ((int)$a->sort_order !== (int)$b->sort_order) {
                DB::table('mkwep_menu_items')->where('id',$a->id)->update(['sort_order'=>$b->sort_order,'updated_at'=>now()]);
                DB::table('mkwep_menu_items')->where('id',$b->id)->update(['sort_order'=>$a->sort_order,'updated_at'=>now()]);
            } else {
                foreach($ids as $position=>$id) DB::table('mkwep_menu_items')->where('id',$id)->update(['sort_order'=>($position+1)*10,'updated_at'=>now()]);
            }
        });
        return back()->with('success','Menü sırası güncellendi.');
    }

    public function destroy(int $menu)
    {
        $this->admin();
        $row=DB::table('mkwep_menu_items')->where('id',$menu)->first();
        abort_unless($row,404);
        if ($row->item_type === 'parent' && DB::table('mkwep_menu_items')->where('parent_key',$row->item_key)->exists()) {
            throw ValidationException::withMessages(['menu'=>'Alt menüsü olan grup silinemez; önce alt menüleri taşıyın.']);
        }
        DB::table('mkwep_menu_items')->where('id',$menu)->delete();
        return back()->with('success','Menü silindi.');
    }
}
