<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionsManagerController extends Controller
{
    public function index() {
        $user_groups = UserGroup::all();
        return view('permissions_manager', compact('user_groups'));
    }

    public function store(Request $request) {
        $data = request()->validate([
            'user-group-names' => 'array',
            'user-group-is-global' => 'array',
            'user-group-permissions' => 'array',
        ]);
        $parsed_perms = self::parsePermissions($data['user-group-permissions']);
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

        // Получаем все группы из базы данных для получения ID
        $userGroupsFromDb = UserGroup::all();

        for ($i = 0; $i < count($userGroupNames); $i++) {
            $userGroupId = $userGroupsFromDb[$i]->id;
            $permissions = [];
            
            foreach($attributes as $key => $value) {
                // Проверяем, есть ли в $parsed_perms запись для этой группы и этого разрешения
                $hasPermission = collect($parsed_perms)->contains(function ($item) use ($userGroupId, $key) {
                    return $item['user_group_id'] === $userGroupId && $item['permission'] === $key;
                });
                $permissions[$key] = $hasPermission;
            }
            
            $usergroup[] = array_merge([
                'name' => $userGroupNames[$i],
                'is_global' => $userGroupIsGlobal[$i] === 'global'
            ], $permissions);
        }

        UserGroup::truncate();
        UserGroup::insert($usergroup);

        return 'Разрешения успешно обновлены';
    }

    public function delete_perm(UserGroup $perm) {
        $perm->delete();
        return $perm;
    }

    public function create() {
        $user_group = UserGroup::first();
        return view('create-user-group', compact('user_group'));
    }

    public function store_usergroup() {
        $data = request()->validate([
            'user-group-name' => 'string',
            'user-group-is-global' => 'string',
            'user-group-permissions' => 'array',
        ]); 
        $user_group = array_merge([
            'name' => $data['user-group-name'],
            'is_global' => $data['user-group-is-global'] === 'global',
        ], array_fill_keys($data['user-group-permissions'], true));
        UserGroup::create($user_group);
        return __('User group created successfully');
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
