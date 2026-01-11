<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;

use Illuminate\Http\Request;

class PermissionsManagerController extends Controller
{
    public function index() {
        $user_groups = UserGroup::all();
        //dd($user_groups);
        return view('permissions_manager', compact('user_groups'));
    }

    public function store(Request $request) {
        $data = request()->validate([
            'user-group-names' => 'array',
            'user-group-is-global' => 'array',
            'user-group-permissions' => 'array',
        ]);
        dump($data);
        $parsed_perms = self::parsePermissions($data['user-group-permissions']);
        dump($parsed_perms);
        /*$groups = [
            'name' => 'user-group-names',
            'is-global' => 'user-group-is-global' === 'global'
        ];*/
        $usergroup = [];
        
        $userGroupNames = $data['user-group-names'];
        $userGroupIsGlobal = $data['user-group-is-global'];

        if (count($userGroupNames) !== count($userGroupIsGlobal)) {
            abort(500);
        }

        $user_group_attr_magic = UserGroup::first();

        // Получаем все атрибуты, которые начинаются с 'can_'
        $attributes = collect($user_group_attr_magic->getAttributes())
        ->filter(function ($value, $key) {
            return str_starts_with($key, 'can_');
        });

        dump($attributes);

        for ($i = 0; $i < count($userGroupNames); $i++) {
            foreach($attributes as $key => $value) {

            }
            $usergroup[] = [
                'name' => $userGroupNames[$i],
                'is_global' => $userGroupIsGlobal[$i] === 'global'
            ];
        }

        dd($usergroup);
        return 'Заглушка для стора разрешений';
    }


    private function parsePermissions(array $permissions): array
    {
        $result = [];
        
        foreach ($permissions as $permission) {
            // Регулярное выражение для разделения числа и строки, начинающейся с can
            if (preg_match('/^(\d+)_(can_[a-z_]+)$/', $permission, $matches)) {
                $result[] = [
                    'user_group_id' => (int)$matches[1],  // число
                    'permission' => $matches[2]     // строка, начинающаяся с can
                ];
            }
        }
        
        return $result;
    }
}
