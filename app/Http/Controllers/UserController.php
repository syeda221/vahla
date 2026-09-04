<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser ? $currentUser->isSuperAdmin() : false;

        if ($isSuperAdmin) {
            $users = User::all();
            $allRoles = Role::orderBy('name', 'asc')->get();
        } else {
            $myPermNames = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];

            $users = User::where('id', '!=', $currentUser ? $currentUser->id : 0)
                ->where('email', '!=', 'superadmin@example.com')
                ->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['superadmin', 'Super Admin', 'superAdmin', 'admin']);
                })
                ->get()
                ->filter(function ($u) use ($myPermNames) {
                    // Only show users whose permissions do not exceed the current user's scope
                    $userPerms = $u->getAllPermissions()->pluck('name')->toArray();
                    return empty(array_diff($userPerms, $myPermNames));
                })
                ->values();

            $allRoles = $currentUser ? $currentUser->getManageableRoles() : collect();
        }

        return view('admin_panel.users.users', compact(['users', 'allRoles']));
    }

    public function store(Request $request)
    {
        $editId = $request->edit_id ?? null;
        $passwordRule = $editId ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$request->edit_id,
            'password' => $passwordRule,
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentUser = auth()->user();
        $manageableRoleNames = $currentUser ? $currentUser->getManageableRoles()->pluck('name')->toArray() : [];
        $requestedRoles = $request->roles ?? [];

        // Security check: if non-superadmin tries to assign a role with unowned permissions
        if ($currentUser && ! $currentUser->isSuperAdmin()) {
            $unauthorizedRoles = array_diff($requestedRoles, $manageableRoleNames);
            if (! empty($unauthorizedRoles)) {
                return response()->json([
                    'errors' => [
                        'roles' => ['You cannot assign roles that contain permissions you do not possess.'],
                    ],
                ], 422);
            }
        }

        if (! empty($editId)) {
            $user = User::findOrFail($editId);

            // Prevent non-superadmin from editing superadmin user
            if ($user->isSuperAdmin() && ($currentUser && ! $currentUser->isSuperAdmin())) {
                return response()->json([
                    'errors' => [
                        'name' => ['You cannot modify a Super Admin user.'],
                    ],
                ], 403);
            }

            $msg = [
                'success' => 'User Updated Successfully',
                'reload' => true,
            ];
        } else {
            $user = new User;
            $msg = [
                'success' => 'User Created Successfully',
                'redirect' => route('users.index'),
            ];
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($currentUser && $currentUser->isSuperAdmin()) {
            $user->syncRoles($requestedRoles);
        } else {
            // Keep roles the user already has which are outside acting user's manageable scope
            $existingRoles = $user->getRoleNames()->toArray();
            $unmanagedRoles = array_diff($existingRoles, $manageableRoleNames);
            $finalRoles = array_values(array_unique(array_merge($unmanagedRoles, $requestedRoles)));
            $user->syncRoles($finalRoles);
        }

        return response()->json($msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser && $user->id === $currentUser->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Super Admin users cannot be deleted.');
        }

        if ($currentUser && ! $currentUser->isSuperAdmin()) {
            $myPermNames = $currentUser->getAllPermissions()->pluck('name')->toArray();
            $targetUserPerms = $user->getAllPermissions()->pluck('name')->toArray();
            
            // If target user has permissions outside current user's scope, prevent deletion
            if (! empty(array_diff($targetUserPerms, $myPermNames))) {
                return redirect()->route('users.index')->with('error', 'You cannot delete users with privileges higher than your own.');
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required|exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($request->edit_id);
        $currentUser = auth()->user();

        if ($user->isSuperAdmin() && ($currentUser && ! $currentUser->isSuperAdmin())) {
            return response()->json([
                'errors' => [
                    'roles' => ['You cannot modify roles of a Super Admin user.'],
                ],
            ], 403);
        }

        $manageableRoleNames = $currentUser ? $currentUser->getManageableRoles()->pluck('name')->toArray() : [];
        $requestedRoles = $request->roles ?? [];

        if ($currentUser && ! $currentUser->isSuperAdmin()) {
            $unauthorizedRoles = array_diff($requestedRoles, $manageableRoleNames);
            if (! empty($unauthorizedRoles)) {
                return response()->json([
                    'errors' => [
                        'roles' => ['You cannot assign roles that contain permissions you do not possess.'],
                    ],
                ], 422);
            }

            $existingRoles = $user->getRoleNames()->toArray();
            $unmanagedRoles = array_diff($existingRoles, $manageableRoleNames);
            $finalRoles = array_values(array_unique(array_merge($unmanagedRoles, $requestedRoles)));
            $user->syncRoles($finalRoles);
        } else {
            $user->syncRoles($requestedRoles);
        }

        return response()->json(['success' => 'User roles updated successfully!', 'reload' => true]);
    }
}
