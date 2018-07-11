<?php

namespace App\Http\Controllers\Admin;

use App\Lib\ModelFactory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = new ModelFactory(User::class);

        $users->availableSortables([
            'name', 'email'
        ]);

        $users->setSearchCols([
            'name', 'email'
        ]);
        $users = $users->paginate();

        return response()->json($users);
    }

    /**
     * Mostrar Vista Inicial
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showIndex()
    {
        return view('admin.pages.usuarios');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|min:5|max:250',
            'email'         => 'required|email',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        $user = new User($request->input());
        $user->password = \Hash::make($request->input('password'));

        $user->save();

        return response()->json($user);
    }

    public function update(User $user, Request $request)
    {
        //dd($user);
        $this->validate($request, [
            'name'        => 'string|min:5|max:250',
            'email'         => 'required|email',
        ]);

        $user->fill($request->input());
        $user->save();

        return response()->json($user);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function delete(User $user)
    {
        $user->delete();
        return response()->json('ok');
    }
}
