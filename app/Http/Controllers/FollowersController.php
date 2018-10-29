<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FollowersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(User $user)
    {
        if($user->id === Auth::user()->id){
            return redirect('/');
        }
        Log::info(!Auth::user()->isFollowing($user->id));
        if(!Auth::user()->isFollowing($user->id)){
            Auth::user()->follow($user->id);
        }
        return redirect()->route('users.show', $user->id);

    }

    public function destroy(User $user)
    {
        if($user->id === Auth::user()->id){
            return redirect('/');
        }
        if(Auth::user()->isFollowing($user->id)){
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route('users.show', $user->id);
    }

    public function test()
    {
        $users = \App\Models\User::all();
        $user = $users->first();
        $user_id = $user->id;

        // 获取去除掉 ID 为 1 的所有用户 ID 数组
        $followers = $users->slice(1);
        $follower_ids = $followers->pluck('id')->toArray();

        // 关注除了 1 号用户以外的所有用户
        $user->follow($follower_ids);

        // 除了 1 号用户以外的所有用户都来关注 1 号用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }

}
