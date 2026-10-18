<?php
namespace App\Support;

use Illuminate\Support\Str;

class MkwepAccess
{
    public static function moduleForRoute(?string $name): ?string
    {
        if (!$name) return null;
        foreach (config('mkwep_access.route_exceptions', []) as $exception) {
            if (Str::is($exception, $name)) return null;
        }
        // Bir modülün alt işlemleri başka modüle aitse önce açık eşlemeyi uygula.
        foreach (config('mkwep_access.route_overrides', []) as $pattern => $module) {
            if (Str::is($pattern, $name)) return $module;
        }
        $matches = [];
        foreach (config('mkwep_access.modules', []) as $key => $module) {
            foreach ($module['prefixes'] as $prefix) {
                if ($name === $prefix || Str::startsWith($name, $prefix.'.')) {
                    $matches[strlen($prefix)] = $key;
                }
            }
        }
        if (!$matches) return null;
        krsort($matches, SORT_NUMERIC);
        return reset($matches);
    }

    public static function actionForRoute(string $name, array $methods): string
    {
        $part = Str::afterLast($name, '.');
        $method = strtoupper($methods[0] ?? 'GET');
        if (preg_match('/(approve|finalize|complete|reject|accept|evaluate)/i', $part)) return 'approve';
        if (preg_match('/(export|print|download|pdf|excel|labels|report-preview|generate|sicilCard)/i', $part)) return 'export';
        if (in_array($part, ['destroy','delete','detach'], true) || $method === 'DELETE') return 'delete';
        if (in_array($part, ['create','store','import','upload'], true) || $method === 'POST' && preg_match('/^(store|add|attach|import)/i', $part)) return 'create';
        if (in_array($part, ['edit','update','save','start'], true) || in_array($method,['PUT','PATCH'],true)) return 'edit';
        if (!in_array($method, ['GET','HEAD'], true)) return 'edit';
        return 'view';
    }

    public static function allowed($user, string $module, string $action = 'view'): bool
    {
        if (!config('mkwep_access.enabled', false)) return true;
        if (!$user) return false;
        // Mevcut admin rolü geri dönüş / kurtarma hesabıdır.
        if ($user->hasRole('admin')) return true;
        // Rolün otomatik verdiği izin, kullanıcıda kapatılan kutuyu geri açmasın.
        return $user->getDirectPermissions()->contains('name', "mkwep.$module.$action");
    }
}
