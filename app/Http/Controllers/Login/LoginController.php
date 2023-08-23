<?php
namespace App\Http\Controllers\Login;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Medias;
use App\Models\UserActivitiesLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\UserHistories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;
use App\Mail\SetMail;
use App\Models\UserAccessTokens;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    /********************************* Login Page Start **************************/
    public function index(){
        $mediaList = Medias::where('status','=','1')->get(['id','name'])->toArray();
        $mediaData = array( 
        'data' => array( 'mediaList' => $mediaList ),
        'title' => 'Login' );
        if (Auth::user()) {  
            $dealStatusArray = Helper::dealStatusArray();
            $dealViewArray = Helper::dealViewArray();
            $dashboardData = array( 
                'title' => 'Dashboard',
                'dealStatus' => $dealStatusArray,
                'dealView' => $dealViewArray
            );                      
            return view('pages.dashboard.index', $dashboardData);
        } else{
            return view('pages.login.index', $mediaData );
        }
        
    }

    public function postLoginUser( Request $request ){
        if( isset( $request['data'] ) && count($request['data']) != 0 ){
            $userDetail = $request['data'];
            if( $userDetail['flag'] == 1 ){
                $userCount = User::where('user_name', '=' , $userDetail['username'])->count();
                if ( $userCount !== 1 ) {
                    $data = array( 'status' => 0 , 'class' => 'user_name', 'message' => 'Please enter User name is incorrect.' );
                    return response()->json($data);  
                }else{
                    return true;
                }
            } else if( $userDetail['flag'] == 2 ){
                $passwordDecrypt = base64_decode($userDetail['password']);
                $userCount = User::where('user_name', '=' , $userDetail['username'])->count();
                if( $userCount !== 0 ){
                    $userPassword = User::where('user_name', '=' , $userDetail['username'])->first()->toarray();
                    if( ( Hash::check($passwordDecrypt, $userPassword['password'] ) ) && ( $userPassword['status'] == 'active' ) && ( $userPassword['delete'] == 0 ) ){
                        return true;
                    }else{
                        $data = array( 'status' => 0 , 'class' => 'password', 'message' => 'Please enter Password is incorrect.' );
                        return response()->json($data);  
                    }
                } else {
                    $data = array( 'status' => 0 , 'class' => 'user_name', 'message' => 'Please enter User name is incorrect.' );
                    return response()->json($data);  
                }
                
            }  else if( $userDetail['flag'] == 3 ){
                $passwordDecrypt = base64_decode($userDetail['password']);
                $userCount = User::where('user_name', '=' , $userDetail['username'])->count();
                if( $userCount !== 0 ){
                    $userPassword = User::where('user_name', '=' , $userDetail['username'])->first()->toarray();
                    if( ( Hash::check($passwordDecrypt, $userPassword['password'] ) ) && ( $userPassword['status'] == 'active' ) && ( $userPassword['delete'] == 0 ) ){
                        $mediaCount = User::join('advertisers', 'advertisers.id', '=', 'users.advertiser_id')->where('users.user_name', '=' , $userDetail['username'])->where('advertisers.media_id', '=' , $userDetail['media'])->count();
                        if( ( $mediaCount === 1 ) && ( $userPassword['status'] == 'active' ) && ( $userPassword['delete'] == 0 )  ){
                            return true;
                        } else {
                            $data = array( 'status' => 0 , 'class' => 'media', 'message' => 'Please check Media Line is incorrect.' );
                            return response()->json($data);  
                        }
                    }else{
                        $data = array( 'status' => 0 , 'class' => 'password', 'message' => 'Please enter Password is incorrect.' );
                        return response()->json($data);  
                    }
                } else {
                    $data = array( 'status' => 0 , 'class' => 'user_name', 'message' => 'Please enter User name is incorrect.' );
                    return response()->json($data);  
                }
            }
        }
    }

    public function postLogin( Request $request ){
        if( ( $request->has('user_name') ) && ( $request->has('password')  ) && ( $request->has('media')  ) ){
            $userCount = User::where('user_name', '=' , $request['user_name'])->count();
            if( $userCount !== 0 ){
                $passwordDecrypt = base64_decode($request['password']);
                $userDetail = User::where('user_name', '=' , $request['user_name'])->first()->toarray();
                $usernameCount = User::join('advertisers', 'advertisers.id', '=', 'users.advertiser_id')->where('users.user_name', '=' , $request['user_name'])->where('users.delete', '=' , 0 )->where('users.status', '=' , 1 )->count();
                $mediaCount = User::join('advertisers', 'advertisers.id', '=', 'users.advertiser_id')->where('users.user_name', '=' , $request['user_name'])->where('advertisers.media_id', '=' , $request['media'])->count();
                if( $mediaCount !== 1 ){
                    $data = array( 'status' => 0 , 'class' => 'media', 'message' => 'Please check Media Line is incorrect.' );
                    return response()->json($data);
                } else if ( $userDetail['delete'] != 0 ){
                    $data = array( 'status' => 0 , 'class' => 'media', 'message' => 'Please check User was Deleted.' );
                    return response()->json($data);
                }else if ( $userDetail['status'] != 'active' ){
                    $data = array( 'status' => 0 , 'class' => 'media', 'message' => 'Please check User was Deactive.' );
                    return response()->json($data);
                } else if (  ( $mediaCount == 1 ) && ( $usernameCount == 1 ) && ( Hash::check($passwordDecrypt, $userDetail['password']) )  ) {
                    Session::put('login_user_id', $userDetail['id']);
                    Session::put('login_user_name', $request['user_name']);
                    Session::put('login_user_email_address', base64_encode($userDetail['email_address']) );
                    Session::put('login_display_name', $request['display_name']);
                    Session::put('login_password', base64_encode($passwordDecrypt));
                    Session::put('login_media', $request['media']);

                    $tokenNumber = LoginController::checkToken( $userDetail['id'] );
                    $emailAddress = $userDetail['email_address'];
                    $mailData = [
                        'title' => 'Verify Your Login Account',
                        'body' => '',
                        'subject' => 'Verify Your Login Account',
                        'otp' => $tokenNumber['token'],
                        'name' => $userDetail['display_name']
                    ];
                    if( Mail::to($emailAddress)->send(new SetMail($mailData)) ) {
                        Helper::activityLog('User Login completed Go to Verify Account');
                        $data = array( 'status' => 1 , 'message' => 'Please Account Verify.');
                        return response()->json($data);
                    }else{
                        $data = array( 'status' => 0 , 'class' => '' ,'message' => 'Mail Was Not Send.');
                        return response()->json($data);
                    }
                } else {
                    $message = 'Please check Password is incorrect.';
                    $data = array( 'status' => 0 , 'class' => 'password', 'message' => $message );
                    return response()->json($data);
                } 
            } else{
                $data = array( 'status' => 0 , 'class' => 'user_name', 'message' => 'Please enter User name is incorrect.');
                return response()->json($data);
            }
        } else {
            $data = array( 'status' => 0 , 'class' => 'user_name', 'message' => 'Please check Detail.');
            return response()->json($data);
        }  
    }

    /********************************* Login Page End **************************/

    /********************************* Verify Page Start **************************/
    
    public function checkToken( $userId ){
        $tokenNumber = random_int(100000, 999999); 
        $currentDate = date('Y-m-d H:i:s');
        $checkTokenNumber = UserAccessTokens::where('token','=', $tokenNumber)->count();
        if( $checkTokenNumber == 0 ){
            $tokenInsert = array(
                'user_id' => $userId,
                'token' => $tokenNumber,
                'created_at' => $currentDate,
            );
            $insertUserToken = UserAccessTokens::create($tokenInsert);
            if($insertUserToken){
                $tokenData = array('message' => 'Inserted', 'token' => $tokenNumber);
            }else{
                $tokenData = array('message' => 'Not Insert', 'token' => $tokenNumber);
            }
            return $tokenData;
        }else{
            LoginController::checkToken( $userId );
        }
    }

    public function postReSendOTP(){
        $userId = Session::get('login_user_id');
        $displayName = Session::get('login_display_name');
        $emailAddress = base64_decode(Session::get('login_user_email_address'));
        $tokenNumber = LoginController::checkToken( $userId );
        $mailData = [
            'title' => 'Verify Your Login Account',
            'body' => '',
            'subject' => 'Verify Your Login Account',
            'otp' => $tokenNumber['token'],
            'name' => $displayName
        ];
        if( Mail::to($emailAddress)->send(new SetMail($mailData)) ) {
            $data = array( 'status' => 1 , 'message' => 'Email was Sucessfully Send. Please check your Account.');
            return response()->json($data);
        }else{
            $data = array( 'status' => 0 , 'class' => '' ,'message' => 'Mail Was Not Send.');
            return response()->json($data);
        }
    }

    public function postVerifyOTP( Request $request ){
        //dd( $request );
        if(isset( $request['data'] ) && ( count( $request['data'] ) > 0 ) ){           
            $sessionUserId = Session::get('login_user_id');
            $userName = Session::get('login_user_name');
            $password = base64_decode(Session::get('login_password'));
            
            $otp = base64_decode($request['data']['otp']);
            $date = date('Y-m-d H:i:s');
            $aboveTwoMinite = date("Y-m-d H:i:s", strtotime("-5 minutes"));
            $getTokenQuery = UserAccessTokens::where('user_id', '=' , $sessionUserId)->where('token', '=' , $otp)->where('verify', '=' , 1)->whereBetween('created_at', [$aboveTwoMinite,  $date]);
            $checkCount = $getTokenQuery->count();
            if( $checkCount == 1 ){
                $getTokenArray = $getTokenQuery->first()->toArray();
                if( Auth::attempt( ['user_name' => $userName, 'password' => $password ] ) ){
                    $userId = Auth::id();
                    $getUserData = User::join('advertisers', 'advertisers.id', '=', 'users.advertiser_id')
                            ->join('clients', 'clients.id', '=', 'users.client_id')
                            ->join('medias', 'medias.id', '=', 'advertisers.media_id')
                            ->where('users.id', '=' , $userId)
                            ->first([
                                'advertisers.id as advertisers_id', 
                                'advertisers.name as advertisers_name', 
                                'users.*', 
                                'clients.name as clients_name', 
                                'clients.id as clients_id',
                                'medias.name as medias_name',
                                'medias.id as medias_id',
                            ]);
                    $currentDate = date('m-d-Y H:i:s');
                    if( $getUserData ){
                        Session::put('user_id', $userId);
                        Session::put('user_name', $getUserData->display_name);
                        Session::put('clients_id', $getUserData->clients_id);
                        Session::put('clent_name', $getUserData->clients_name );
                        Session::put('advertiser_id', $getUserData->advertisers_id);
                        Session::put('advertiser_name', $getUserData->advertisers_name);
                        Session::put('advertiser_email_address', $getUserData->email_address);
                        Session::put('advertiser_logintime', $currentDate);
                        Session::put('media_line', $getUserData->medias_name);
                        Session::put('medias_id', $getUserData->medias_id);
                    }

                    $checkCount = UserAccessTokens::where('user_id', '=' , $sessionUserId)->where('token', '=' , $otp)->where('id', '=' , $getTokenArray['id'])
                    ->update(['verify' => 0, 'updated_at' => $date]);

                    Helper::activityLog('User Verify Completed Go to Dashboard');
                    Helper::userHistory();

                    $data = array( 'status' => 1 , 'message' => 'Your Account Sucessfully Verify.');
                    return response()->json($data);
                } else {
                    $data = array( 'status' => 0 , 'class' => '' ,'message' => 'Please Check Account Detail.');
                     return response()->json($data);
                }
            } else{
                $data = array( 'status' => 0 , 'class' => '' ,'message' => 'Please check Token is Incorrecct.');
                return response()->json($data);
            }
        }
    }

    /********************************* Verify Page End **************************/
}
