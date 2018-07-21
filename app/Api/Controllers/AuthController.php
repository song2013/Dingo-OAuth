<?php

namespace App\Api\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends BaseController
{
    //
    public function register(Request $request)
    {
        $data=$request->all();
//        return $data;
        $validator=Validator::make($data,[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails())
        {
            return array(
                'status'    =>  false,
                'error'     =>  $validator->errors()
            );
        }

        $user= new User();
        $user->name=$data['name'];
        $user->email=$data['email'];
        $user->password=Hash::make($data['password']);
        if($user->save())
        {
            $token=$user->createToken(config('api.token'))->accessToken;
            return array(
                'status'    =>  true,
                'token'     =>  $token
            );
        }
        return array(
            'status'    =>  true,
            'error'     =>  '失败'
        );
    }


    public function login(Request $request)
    {
        $payload = [
            'name' => $request->post('name'),
            'password' => $request->post('password')
        ];
        if(Auth::attempt($payload))
        {
            $user=Auth::user();
            $token=$user->createToken(config('api.token'))->accessToken;
            return array(
                'status'    =>  true,
                'token'     =>  $token
            );
        }

    }
}
