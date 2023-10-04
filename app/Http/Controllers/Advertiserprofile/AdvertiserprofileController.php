<?php

namespace App\Http\Controllers\Advertiserprofile;
use App\Http\Controllers\Controller;

class AdvertiserprofileController extends Controller
{

    public function index(){
        $data = array( 
            'title' => 'Advertiser Profile',
        );
        return view( 'pages.advertiserprofile.index', $data );
    }
}
?>