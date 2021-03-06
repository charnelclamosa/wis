<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserHRD;
use App\Models\UserWKN;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        return User::withTrashed()->select(
            'users.*',
            'roles.RoleName'
        )
        ->join('roles', 'roles.id', 'users.RoleId')
        ->get();
    }
    public function store(Request $request) {
        $validator = Validator::make($request->User, [
            'Username' => 'required|string|min:5|max:7',
            'Password' => 'required|string|min:5|max:20'
        ]);
        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }
        $user = new User;
        $user->Username = $request->User['Username'];
        $user->Password = Hash::make($request->User['Password']);
        $user->RoleId = $request->User['RoleId'];
        $user->updated_by = $request->updated_by;
        $user->save();
        return response()->json(['message' => 'Stored successful.'], 201);
    }
    public function update(Request $request) {
        $user = User::findOrFail($request->id);
        if(!$user) return;
        $user->update([
            'Username' => $request->User['Username'],
            'RoleId' => $request->User['RoleId'],
            'updated_by' => $request->User['updated_by']
        ]);
        return response()->json(['message' => 'Update successful.', 200]);
    }
    public function password(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update([
            'Password' => Hash::make($request->Password),
            'updated_by' => $request->updated_by
        ]);
        return response()->json(['message' => 'Update password successful.'], 200);
    }
    public function delete($id) {
        $user = User::findOrFail($id);
        $user->delete($user->all());
        return response()->json(['message' => 'Delete successful.'], 200);
    }
    public function restore($id) {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore($user->all());
        return response()->json(['message' => 'Restore successful.'], 200);
    }
    public function employeeInfo($id) {
        $employeeCode = strlen($id);
        $info = $employeeCode == 7 ? $this->wukong($id) : $this->hrd($id);
        return $info;
    }
    public function wukong($id) {
        return UserWKN::where('EmployeeCode', $id)->firstOrFail();
    }
    public function hrd($id) {
        return UserHRD::where('EmployeeCode', $id)->firstOrFail();
    }
}
