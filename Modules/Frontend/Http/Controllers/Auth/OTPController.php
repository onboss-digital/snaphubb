<?php


namespace Modules\Frontend\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Auth;
use Str;
use App\Models\Device;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeviceEmail;
use App\Models\Setting;
use Jenssegers\Agent\Agent;
use App\Models\UserMultiProfile;
use Modules\Frontend\Trait\LoginTrait;


class OTPController extends Controller
{
    use LoginTrait;

    public function otpLogin()
    {
        $userId = auth()->id(); 
        
        $settings = Setting::getAllSettings($userId);
        $isOtpLoginEnabled = Setting::where('name', 'is_otp_login')->value('val') == 1;
        
        return view('frontend::auth.otp_login', compact('settings', 'isOtpLoginEnabled'));
    }


    public function otpLoginStore(Request $request)
    {
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' =>  $request->email,
            'mobile' =>  $request->mobile,
            'password' => Hash::make(Str::random(8)),
            'user_type' => 'user',
            'login_type' => 'otp'
        ];

        $user=User::where('email', $request->email)->first();

        $user = User::create($data);

        $request->session()->regenerate();

        $user->createOrUpdateProfileWithAvatar();

        $user->assignRole($data['user_type']);

        $user->save();

        if($user->login_type == 'otp' )
        {
            Auth::login($user);
            $this->setDevice($user, $request);
        }
        else
        {
            $user=Auth::user();
            Auth::logout();
            $this->removeDevice($user, $request);
           return Redirect::to('/login')->with('error', 'Something went wrong! During login');
        }

        return redirect('/'); // Redirect to intended page
    }

    public function checkUserExists(Request $request)
    {
        $data = $request->all();

        $current_device=$request->has('device_id')?$request->device_id:$request->getClientIp();

        $flag = 0;
        $user = User::where('mobile', $request->mobile)->where('login_type','otp')->with('subscriptionPackage')->first();

        if(!empty($user))
        {

            if($user->user_type !='user'){

                return response()->json(['message'=>"Admin doesn't have access to login", 'status' => 406]);
            }

            $response=$this->CheckDeviceLimit($user, $current_device);

            if(isset($response['error'])) {

                return response()->json(['message'=>$response['error'], 'status' => 406]);
            }

            $this->setDevice($user, $request);

            Auth::login($user);
            $flag = 1;
        }

        return response()->json(['is_user_exists' => $flag, 'url' => route('user.login')]);
    }

    /**
     * API endpoint for frontend to check if a user exists by email or mobile.
     * Returns JSON: { "exists": true|false }
     */
    public function checkUserExistsApi(Request $request)
    {
        $email = $request->input('email');
        $mobile = $request->input('mobile');

        // Prefer email when provided
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['exists' => false, 'error' => 'invalid_email'], 400);
            }

            $exists = User::where('email', $email)->exists();
            return response()->json(['exists' => (bool)$exists], 200);
        }

        if (!empty($mobile)) {
            $exists = User::where('mobile', $mobile)->exists();
            return response()->json(['exists' => (bool)$exists], 200);
        }

        return response()->json(['exists' => false, 'error' => 'missing_parameter'], 400);
    }
}
