<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\validator;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function login(Request $request)
    {
        try {
            // validasi input
            $request->validate([
                'email'=> 'emailrequired',
                'password'=> 'required'
            ]);

            // Mengecek credentials (login)
            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            //Jika hash tidak sesuai maka akan menampilkan error
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password, [])){
                throw new \Exception('Invalid Credential');
            }

            //Jika berhasil maka loginkan
            $tokenResult = $user->crateToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch(Exception $error){
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function register (Request $request)
    {
        try {
                $request->validate([
                    'name' => ['required','string','max:255'],
                    'email' => ['required','string','max:255','unique:users'],
                    'password' => $this->passwordRules()
                ]);
                
                //membuat user
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'housenumber' =>$request->housenumber,
                    'phonenumber' =>$request->phonenumber,
                    'city' =>$request->city,
                    'password' =>Hash::make($request->password),
                ]);

                //memanggil user yang telah dibuat (dimana me-request email)
                $user = User::where('email',$request->email)->first();

                $tokenResult = $user->createToken('authToken')->plainTextToken;

                return ResponseFormatter::success([
                    'access_Token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ]);
    
        //jika ada error atau kesalahan        
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ],'Authentication Failed',500);
        }
    }

    public function logout (Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    //mengambil data user untuk API, sediakan satu API user->(yang dimana nanti mobile dpt mengambil data profile
    //dari si user yang sudah login)
    public function fetch(Request $request)
    {
        //ketika sudah sukses data nya akan dibalikkan dan menampilkan pesan 'Data profile user berhasil diambil'
        return ResponseFormatter::success(
            $request->user(),'Data profile user berhasil diambil');
    }

    public function updateProfile (Request $request)
    {
        $data = $request->all();

        //ini variabel auth user yang mengarah ke tabel user
        $user = Auth::user();
        $user->update($data);

        //ketika sudah sukses data nya akan dibalikkan dan menampilkan pesan 'profile updated'
        return ResponseForrmatter::success($user, 'Profile Updated');
    }

    public function updatePhoto (Request $request)
    {
        //validasi upload foto max 2048
        $validator = validator::make($request->all(), [
            'file' => 'required|image|max:2048'
        ]);

        if($validator->fails())
        {
            return ResponseFormatter::error(
                ['error' => $validator->errors()],
                'Update photo fails',
                401
            );
        }

        if($request->file('file'))
        {
            $file = $request->file->store('assets/user','public');

            //simpan foto kedatabase (url nya)
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([$file],'File Successfully Updated');
        }
    }

}
  
