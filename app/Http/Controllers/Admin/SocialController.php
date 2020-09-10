<?php

namespace App\Http\Controllers\Admin;

use App\Enumeration\CouponType;
use App\Model\SocialLinks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;

class SocialController extends Controller
{
    public function index() 
    {
        $socialLinks = SocialLinks::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard.social_links.index', compact('socialLinks'))->with('page_title', 'Social Links');
    }

    public function addUpdatePost(Request $request) 
    {
        // Check this user has social links or not
        $checkSocialLinks = SocialLinks::count();
        if ( $checkSocialLinks > 0 ) {
            $updateData = [
                'facebook' => isset($request->facebook) ? $request->facebook : '',
                'twitter' => isset($request->twitter) ? $request->twitter : '',
                'pinterest' => isset($request->pinterest) ? $request->pinterest : '',
                'instagram' => isset($request->instagram) ? $request->instagram : '',
                'instagram_baevely' => isset($request->instagram_baevely) ? $request->instagram_baevely : '',
                // 'whatsapp' => isset($request->whatsapp) ? $request->whatsapp : '',
                // 'google_plus' => isset($request->google_plus) ? $request->google_plus : '',
            ];
            SocialLinks::first()->update($updateData);
        }
        else {
            SocialLinks::create([
                'facebook' =>isset($request->facebook) ? $request->facebook : '',
                'twitter' => isset($request->twitter) ? $request->twitter : '',
                'pinterest' => isset($request->pinterest) ? $request->pinterest : '',
                'instagram' => isset($request->instagram) ? $request->instagram : '',
                'instagram_baevely' => isset($request->instagram_baevely) ? $request->instagram_baevely : '',
                // 'whatsapp' => isset($request->whatsapp) ? $request->whatsapp : '',
                // 'google_plus' => isset($request->google_plus) ? $request->google_plus : '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return redirect()->back()->with('message', 'Information updated!');
    }

    public function social_feed_access()
    {
        $social_feeds = DB::table('social_feeds')->get();
        $data_array = array();
        if(count($social_feeds) == 0)
        {
            // $data_array['facebook']['access_token'] = "";
            // $data_array['facebook']['user_token'] = "";
            // $data_array['facebook']['pass_token'] = "";

            $data_array['instagram']['access_token'] = "";

            // $data_array['twitter']['access_token'] = "";
            // $data_array['twitter']['user_token'] = "";
            // $data_array['twitter']['pass_token'] = "";
        }
        else
        {
            // $data_array['facebook']['access_token'] = DB::table('social_feeds')->select('access_token')->where('type','facebook')->get()[0]->access_token;
            // $data_array['facebook']['user_token'] = DB::table('social_feeds')->select('user_token')->where('type','facebook')->get()[0]->user_token;
            // $data_array['facebook']['pass_token'] = DB::table('social_feeds')->select('pass_token')->where('type','facebook')->get()[0]->pass_token;

            $data_array['instagram']['access_token'] = DB::table('social_feeds')->select('access_token')->where('type','instagram')->get()[0]->access_token;

            // $data_array['twitter']['access_token'] = DB::table('social_feeds')->select('access_token')->where('type','twitter')->get()[0]->access_token;
            // $data_array['twitter']['user_token'] = DB::table('social_feeds')->select('user_token')->where('type','twitter')->get()[0]->user_token;
            // $data_array['twitter']['pass_token'] = DB::table('social_feeds')->select('pass_token')->where('type','twitter')->get()[0]->pass_token;
        }
        return view('admin.dashboard.social_feeds.index', compact('data_array'))->with('page_title', 'Social Feeds');
    }

    public function socialFeedaddUpdatePost(request $request)
    {
        $social_feeds = DB::table('social_feeds')->get();
        if(sizeof($social_feeds) == 0)
        {
            // $data = array();
            // $data['type'] = 'facebook';
            // $data['access_token'] = $request->facebook_access_token;
            // $data['user_token'] = $request->facebook_user_token;
            // $data['pass_token'] = $request->facebook_pass_token;
            // DB::table('social_feeds')->insert($data);

            $data = array();
            $data['type'] = 'instagram';
            $data['access_token'] = $request->instagram_access_token;
            DB::table('social_feeds')->insert($data);

            // $data = array();
            // $data['type'] = 'twitter';
            // $data['access_token'] = $request->twitter_access_token;
            // $data['user_token'] = $request->twitter_user_token;
            // $data['pass_token'] = $request->twitter_poass_token;
            // DB::table('social_feeds')->insert($data);
            return redirect()->route('admin_social_feed')->with('message', 'Successfully added!');

        }
        else
        {
            // $data = array();
            // $data['type'] = 'facebook';
            // $data['access_token'] = $request->facebook_access_token;
            // $data['user_token'] = $request->facebook_user_token;
            // $data['pass_token'] = $request->facebook_pass_token;
            // DB::table('social_feeds')->where('type','facebook')->update($data);

            $data = array();
            $data['type'] = 'instagram';
            $data['access_token'] = $request->instagram_access_token;
            DB::table('social_feeds')->where('type','instagram')->update($data);

            // $data = array();
            // $data['type'] = 'twitter';
            // $data['access_token'] = $request->twitter_access_token;
            // $data['user_token'] = $request->twitter_user_token;
            // $data['pass_token'] = $request->twitter_poass_token;
            // DB::table('social_feeds')->where('type','twitter')->update($data);
            return redirect()->route('admin_social_feed')->with('message', 'Successfully updated!');
        }
    }
}
