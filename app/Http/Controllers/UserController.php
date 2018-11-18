<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use function Couchbase\defaultDecoder;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = \App\User::paginate(4);
        $filterKeyword = $request->get('keyword');
        $status = $request->get('status');
        if ($filterKeyword) {
            if ($status) {
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")
                    ->where('status', $status)
                    ->paginate(4);
            } else {
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")->paginate(4);
            }
        }


        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validasidata = $request->validate([
            "name" => "required|min:5|max:100",
            "username" => "required|min:5|max:30",
            "roles" => "required",
            "phone" => "required|digits_between:10,12",
            "address" => "required|min:20|max:200",
            "email" => "required|email",
            "password" => "required",
            "password_confirmation" => "required|same:password"
        ]);
        $datas = $request->all();
        if ($request->file('avatar')) {
            $file = Storage::disk('public')->put('avatars',$request->avatar);
            $datas['avatar'] = $file;
        }
        User::create($datas);
        return redirect()->route('users.create')->with('status', 'User successfully created');
    }
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = \App\User::findOrFail($id);
        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = \App\User::findOrFail($id);
        return view('users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validasidata = $request->validate([
            "name" => "required|min:5|max:100",
            "roles" => "required",
            "phone" => "required|digits_between:10,12",
            "address" => "required|min:20|max:200"
        ]);
        $user = \App\User::findOrFail($id);
        $datas = $request->all();
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar')->store('avatars', 'public');
            $datas['avatar'] = $file;
            \Storage::delete('public/' . $user->avatar);

        }

        $user->update($datas);

//        $user->name = $request->get('name');
//        $user->roles = json_encode($request->get('roles'));
//        $user->address = $request->get('address');
//        $user->phone = $request->get('phone');
//        $user->status= $request->status;
//        if ($request->file('avatar')) {
//            $file = $request->file('avatar')->store('avatars', 'public');
//            $user->avatar = $file;
//        }
//        if ($user->avatar && file_exists(storage_path('app/public/' . $user->avatar))) {
//            \Storage::delete('public/' . $user->avatar);
//            $file = $request->file('avatar')->store('avatars', 'public');
//            $user->avatar = $file;
//        }
//
//        $user->save();

        return redirect()->route('users.edit', ['id' => $id])->with('status', 'User succesfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);

        $user->delete();
        return redirect()->route('users.index')->with('status', 'User successfully deleted');
    }
}
