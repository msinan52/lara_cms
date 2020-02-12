<?php

namespace App\Http\Controllers\Admin;

use App\Kullanici;
use App\Models\Auth\Role;
use App\Models\KullaniciDetay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Hash;

class KullaniciController extends Controller
{
    public function login()
    {
        if (Auth::guard('admin')->check())
            return redirect(route('admin.home_page'));
        if (request()->isMethod('POST')) {
            $validatedData = request()->validate([
                'email' => 'required|min:6|email:',
                'password' => 'required|min:6',
            ]);
            $user_login_data = ['email' => request('email'), 'password' => request('password'), 'is_admin' => 1, 'is_active' => 1];
            if (Auth::guard('admin')->attempt($user_login_data, request()->has('remember_me', 0))) {
                return redirect(route('admin.home_page'));
            }
            return back()->withInput()->withErrors(['email' => 'hatalı kullanıcı adı veya şifre']);
        }
        return view('admin.login');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->flush();
        request()->session()->regenerate();
        return redirect(route('admin.login'));
    }


    public function listUsers()
    {
        $perPageItem = 10;
        $query = request('q');
        $auth = Auth::guard('admin')->user();
        if ($query) {
            $list = Kullanici::where(function ($qq) use ($query) {
                $qq->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%")
                    ->orWhere('surname', 'like', "%$query%");
            })->when($auth->email != config('admin.username'), function ($qq) {
                $qq->where('email', '!=', config('admin.username'));
            })->orderByDesc('id')
                ->paginate($perPageItem);

        } else {
            $list = Kullanici::when($auth->email != config('admin.username'), function ($qq) {
                $qq->where('email', '!=', config('admin.username'));
            })->orderByDesc('id')->paginate($perPageItem);
        }

        return view('admin.user.list_users', compact('list'));
    }

    public function newOrEditUser($user_id = 0)
    {
        $user = new Kullanici();
        $roles = Role::all();
        $auth = Auth::guard('admin')->user();
        if ($user_id > 0) {
            $user = Kullanici::whereId($user_id)->firstOrFail();
            if ($auth->email != config('admin.username'))
                $roles = Role::whereNotIn('name', ['super-admin'])->get();
            if ($user->email == config('admin.username') && $auth->email != config('admin.username'))
                return back()->withErrors('yetkiniz yok');

        }

        return view('admin.user.new_edit_user', compact('user', 'roles'));
    }


    public function saveUser($user_id = 0)
    {

        $email_validate = (int)$user_id == 0 ? 'email|unique:kullanicilar' : 'email';
        $this->validate(request(), [
            'name' => 'required|min:3|max:50',
            'surname' => 'required|min:3|max:50',
            'email' => $email_validate,
        ]);
        $request_data = request()->only('name', 'surname', 'email', 'role_id');
        if (\request()->filled('password'))
            $request_data['password'] = Hash::make(request('password'));
        $request_data['is_active'] = request()->has('is_active') ? 1 : 0;
        $request_data['is_admin'] = request()->has('is_admin') ? 1 : 0;
        if ($user_id > 0) { // update
            $user = Kullanici::where('id', $user_id)->firstOrFail();
            $user->update($request_data);
        } else {
            $user = Kullanici::create($request_data);
        }
        KullaniciDetay::updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => request('phone')
            ]
        );
        session()->flash('message', 'İşlem başarılı şekilde gerçekleşti');
        return redirect(route('admin.user.edit', $user->id));
    }

    public function deleteUser($user_id)
    {
        $user = Kullanici::where('id', $user_id)->firstOrFail();
        if ($user->email == config('admin.username'))
            return back()->withErrors('Bu kullanıcı silinemez');
        $user->email = $user->email . '|' . str_random(10);
        $user->save();
        $user->delete();
        session()->flash('message', ' isimli kullanıcı başarıyla silindi');
        return redirect(route('admin.users'));
    }

}
