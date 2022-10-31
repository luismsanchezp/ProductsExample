<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\api\v1\UserStoreRequest;
use App\Http\Requests\api\v1\UserUpdateRequest;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        return response()->json([
            'data' => UserResource::collection(
                    $users->loadMissing('products')
                )
            ],
            200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $username = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $password = Hash::make($password);

        $user = User::create([
            'name'=>$username,
            'email'=>$email,
            'password'=>$password
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return (
            new UserResource(
                $user->loadMissing('products')
            )
        )
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $id = Auth::user()->id;
        if ($user->id == $id){
            if ($request->exists('name')){
                $name = $request->input('name');
                $user->name = $name;
            }
            if ($request->exists('email')){
                $email = $request->input('email');
                $user->email = $email;
            }
            if ($request->exists('password')){
                $password = $request->input('password');
                $password = Hash::make($password);
                $user->password = $password;
            }
            $user->save();
            return (new UserResource($user))
                ->response()
                ->setStatusCode(200);
        } else {
            return response()
                ->json(['data' => 'You cannot update information to other users.'])
                ->setStatusCode(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $id = Auth::user()->id;
        if ($user->id == $id){
            $user->delete();
            return response(null, 204);
        } else {
            return response()
                ->json(['data' => 'You cannot delete other users.'])
                ->setStatusCode(403);
        }
    }
}
