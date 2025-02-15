<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\UserResource;

class RolePermissionController extends Controller
{
    protected $guard_name = 'sanctum';

    public function getRoles(Request $request)
    {
        try {
            $roles = Role::where('guard_name', $this->guard_name)
                ->with('permissions')
                ->orderBy('name')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $roles->items(),
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPermissions()
    {
        try {
            $permissions = Permission::where('guard_name', $this->guard_name)
                ->orderBy('name')
                ->paginate(10);
            
            return response()->json([
                'status' => 'success',
                'data' => $permissions->items(),
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string'
            ]);

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $this->guard_name
            ]);
            
            $permissions = Permission::where('guard_name', $this->guard_name)
                ->where(function($query) use ($request) {
                    $query->whereIn('id', $request->permissions)
                          ->orWhereIn('name', $request->permissions);
                })
                ->get();
            
            $role->syncPermissions($permissions);

            return response()->json([
                'status' => 'success',
                'message' => 'Role created successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id,
                'permissions' => 'required|array',
                'permissions.*' => 'required|string'
            ]);

            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
                'guard_name' => $this->guard_name
            ]);
            
            $permissions = Permission::where('guard_name', $this->guard_name)
                ->where(function($query) use ($request) {
                    $query->whereIn('id', $request->permissions)
                          ->orWhereIn('name', $request->permissions);
                })
                ->get();
            
            $role->syncPermissions($permissions);

            return response()->json([
                'status' => 'success',
                'message' => 'Role updated successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignRoleToUser(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $roles = Role::where('guard_name', 'sanctum')
            ->whereIn('name', $request->roles)
            ->get();

        $user->syncRoles($roles);

        return response()->json([
            'status' => 'success',
            'message' => 'Roles assigned successfully',
            'user' => new UserResource($user->load(['roles.permissions', 'department']))
        ]);
    }

    public function getUserRolesAndPermissions(User $user)
    {
        return response()->json([
            'status' => 'success',
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'sanctum'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission created successfully',
            'permission' => new PermissionResource($permission)
        ], 201);
    }

    public function getAllPermissions()
    {
        try {
            $permissions = Permission::where('guard_name', $this->guard_name)
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 