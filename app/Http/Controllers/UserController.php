<?php

namespace App\Http\Controllers;

use App\User;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Mail\NewUser;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    public function index() {
      return Inertia::render('Admin/Users/Index', [
        'users' => User::where('id', '!=', auth()->id())->orderBy('email')->paginate()
      ]);
    }

    public function show() {
      return Inertia::render('Admin/Users/Show', [
        'user' => Auth::user()
      ]);
    }

    public function create() {
      return Inertia::render('Admin/Users/Create');
    }

    public function store() {

      Request::validate([
        'name' => ['required'],
        'email' => ['required', 'unique:users'],
        'password' => ['required','min:8'],
        'passwordconfirm' => ['required', 'same:password'],
      ]);

      $user = User::create(Request::all());
      $password = Request::get('password');

      Mail::to($user)->send(new NewUser($password));

      return Redirect::route('users')->with('success', 'User added Successfully');
    }

    public function edit(User $user) {
      return Inertia::render('Admin/Users/Edit', [
        'user' => $user
      ]);
    }

    public function update(User $user) {

      Request::validate([
        'name' => ['required'],
        'email' => ['required', Rule::unique('users')->ignore($user)],
      ]);

      $user->update(Request::all());

      return Redirect::back()->with('success', 'User updated Successfully');
    }

    public function update_current(User $user) {

      Request::validate([
        'name' => ['required'],
        'email' => ['required', Rule::unique('users')->ignore($user)],
        'password' => ['sometimes', 'required', 'min:8'],
        'passwordconfirm' => ['sometimes', 'required', 'same:password'],
      ]);

      $user->update(Request::all());

      return Redirect::back()->with('success', 'User updated Successfully');
    }

    public function destroy(User $user) {
      $user->delete();
      return Redirect::back()->with('success', 'User has been removed');
    }
}
