<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileSetting;
use Modules\Entertainment\Models\Entertainment;
use Modules\Banner\Models\Banner;
use Modules\Entertainment\Models\ContinueWatch;
use App\Models\Device;
use App\Models\User;
use Modules\Banner\Transformers\SliderResource;
use Modules\Entertainment\Transformers\ContinueWatchResource;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\Entertainment\Transformers\MoviesResource;
use Modules\Entertainment\Transformers\TvshowResource;
use Modules\Tax\Models\Tax;
use Modules\Constant\Models\Constant;
use Modules\LiveTV\Models\LiveTvChannel;
use Modules\LiveTV\Transformers\LiveTvChannelResource;
use Modules\CastCrew\Models\CastCrew;
use Modules\CastCrew\Transformers\CastCrewListResource;
use Modules\Genres\Transformers\GenresResource;
use Modules\Genres\Models\Genres;
use Modules\Video\Transformers\VideoResource;
use Modules\Video\Models\Video;
use Modules\Page\Models\Page;
use Modules\FAQ\Models\FAQ;
use App\Services\RecommendationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Subscriptions\Trait\SubscriptionTrait;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    use SubscriptionTrait;
    /**
     * Display a listing of the resource.
     */
    protected $recommendationService;
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;

    }
    public function index(Request $request)
    {
        $user_id = auth()->id();
        $profile_id=0;
        $continue_watch = [];

        $cacheKey = 'slider';

        $sliders = Cache::get($cacheKey);

        if (!$sliders) {

           $sliderList = Banner::where('status', 1)->get();
           $sliders = SliderResource::collection($sliderList->map(function ($slider) use ($user_id) {
                return new SliderResource($slider, $user_id);
           }));

           $sliders = $sliders->toArray(request());

           Cache::put($cacheKey, $sliders);

        }

            if( $user_id){

                $profile_id=getCurrentProfile( $user_id, $request);
                $continueWatchList = ContinueWatch::where('user_id', $user_id)->where('profile_id',$profile_id)->with('entertainment', 'episode', 'video')->orderBy('id','desc')->get();
                $continue_watch = $continueWatchList->map(function ($item) {
                    return new ContinueWatchResource($item);
                })->toArray();
            }

            $cacheKey = 'top_10_movie';
            $top_10 = Cache::get($cacheKey);

            if(!$top_10){
                $topMovieIds = MobileSetting::getValueBySlug('top-10');
                $topMovies = Entertainment::whereIn('id',json_decode($topMovieIds))->where('status',1)->get();
                $top_10 = MoviesResource::collection($topMovies);
                $top_10 = $top_10->toArray(request());
                Cache::put($cacheKey, $top_10);
            }


            $cacheKey = 'latest_movie';
            $latest_movie = Cache::get($cacheKey);

            if(!$latest_movie){

               $latestMovieIds = MobileSetting::getValueBySlug('latest-movies');
               $latestMovie = Entertainment::whereIn('id',json_decode($latestMovieIds))->where('status',1)->get();
               $latest_movie = MoviesResource::collection($latestMovie);
               Cache::put($cacheKey, $latest_movie);

            }

            $cacheKey = 'popular_language';
            $popular_language = Cache::get($cacheKey);

            if(!$popular_language){
                $languageIds = MobileSetting::getValueBySlug('enjoy-in-your-native-tongue');
                $popular_language = Constant::whereIn('id',json_decode($languageIds))->get();

            }

            $cacheKey = 'popular_movie';
            $popular_movie = Cache::get($cacheKey);

            if(!$popular_movie){

             $popularMovieIds = MobileSetting::getValueBySlug(slug: 'popular-movies');
             $popularMovies = Entertainment::whereIn('id',json_decode($popularMovieIds))->where('status',1)->get();
             $popular_movie = MoviesResource::collection($popularMovies);

            }

            $cacheKey = 'top_channel';
            $top_channel = Cache::get($cacheKey);

            if(!$top_channel){

              $channelIds = MobileSetting::getValueBySlug('top-channels');
              $channels = LiveTvChannel::whereIn('id',json_decode($channelIds))->where('status',1)->get();
              $top_channel = LiveTvChannelResource::collection($channels);
              $top_channel = $top_channel->toArray(request());

            }

            $cacheKey = 'personality';
            $personality = Cache::get($cacheKey);


            if(!$personality){

                $castIds =  MobileSetting::getValueBySlug('your-favorite-personality');
                $casts = CastCrew::whereIn('id',json_decode($castIds))->get();
                $personality = [];
                foreach ($casts as $key => $value) {
                    $personality[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'type' => $value->type,
                        'profile_image' => setBaseUrlWithFileName($value->file_url),
                    ];
                }
           }


            $cacheKey = 'free_movie';
            $free_movie = Cache::get($cacheKey);

            if(!$free_movie){

            $movieIds = MobileSetting::getValueBySlug('500-free-movies');
            $free_movies_tvshow = Entertainment::whereIn('id',json_decode($movieIds))->where('status',1)->get();
            $free_movie = MoviesResource::collection($free_movies_tvshow);

            }

            $cacheKey = 'popular_tvshow';
            $popular_tvshow = Cache::get($cacheKey);

            if(!$popular_tvshow){

               $popular_tvshowIds = MobileSetting::getValueBySlug(slug: 'popular-tvshows');
               $popular_tvshow = Entertainment::whereIn('id',json_decode($popular_tvshowIds))->where('status',1)->get();
               $popular_tvshow = MoviesResource::collection($popular_tvshow);

            }

            $cacheKey = 'genres';
            $genres = Cache::get($cacheKey);

            if(!$genres){

              $genreIds = MobileSetting::getValueBySlug(slug: 'genre');
              $genres = Genres::whereIn('id',json_decode($genreIds))->where('status',1)->get();
              $genres = GenresResource::collection($genres);
              $genres = $genres->toArray(request());

            }

            $cacheKey = 'popular_videos';
            $popular_videos = Cache::get($cacheKey);

            if(!$popular_videos){

            $videoIds = MobileSetting::getValueBySlug(slug: 'popular-videos');
            $popular_videos = Video::whereIn('id',json_decode($videoIds))->where('status',1)->get();
            $popular_videos = VideoResource::collection($popular_videos);
            }


        if( $user_id){

             $profile_id=getCurrentProfile($user_id, $request);

             $user = User::where('id',$user_id)->first();
             $based_on_last_watch = $this->recommendationService->recommendByLastHistory($user,$profile_id);

             $Lastwatchrecommendation = MoviesResource::collection($based_on_last_watch );


            $likedMovies = $this->recommendationService->getLikedMovies($user, $profile_id);
            $likedMovies = MoviesResource::collection($likedMovies);
            $viewedMovies = $this->recommendationService->getEntertainmentViews($user, $profile_id);
            $viewedMovies = MoviesResource::collection($viewedMovies);

            $favorite_gener = $this->recommendationService->getFavoriteGener($user, $profile_id);
            $FavoriteGener = GenresResource::collection($favorite_gener);
            $FavoriteGener = $FavoriteGener->toArray(request());


            $favorite_personality = $this->recommendationService->getFavoritePersonality($user, $profile_id);

            $favorite_personality = CastCrewListResource::collection($favorite_personality);
            $favorite_personality = $favorite_personality->toArray(request());


            $watchlist = $this->recommendationService->getUserWatchlist($user, $profile_id);
            $watchlist = $watchlist->toArray(request());

            $trendingMovies = $this->recommendationService->getTrendingMoviesByCountry($user, $request);
            $trendingMovies = MoviesResource::collection($trendingMovies);

            }

            $data = [
                'slider' => $sliders,
                'continue_watch' => $continue_watch,
                'top_10' => $top_10,
                'latest_movie' => $latest_movie,
                'popular_language' => $popular_language,
                'popular_movie' => $popular_movie,
                'top_channel' => $top_channel,
                'personality' => $personality,
                'free_movie' => $free_movie,
                'genres' => $genres,
                'popular_tvshow' => $popular_tvshow,
                'popular_videos' => $popular_videos,
                'likedMovies' => $likedMovies ?? [],
                'viewedMovies' => $viewedMovies ?? [],
                'trendingMovies' => $trendingMovies ?? [],
                'favorite_gener' => $FavoriteGener ?? [],
                'favorite_personality' => $favorite_personality ?? [],
                'based_on_last_watch'=>$Lastwatchrecommendation ?? [],

            ];

        return view('frontend::index', compact('data','user_id'));
    }
    public function searchList()
    {
        $entertainment_list = Entertainment::query()
        ->with([
            'entertainmentGenerMappings',
            'plan',
            'entertainmentTalentMappings',
            'entertainmentStreamContentMappings',
            'entertainmentReviews' => function ($query) {
                $query->whereBetween('rating', [4, 5]);
            }
        ])
        ->where('status', 1);

    // Fetch movies
    $movieList = $entertainment_list->where('type', 'movie')->take(10)->get();
    $movieData = (isenablemodule('movie') == 1) ? MoviesResource::collection($movieList) : [];

    $entertainment_data = Entertainment::query()
    ->with([
        'entertainmentGenerMappings',
        'plan',
        'entertainmentTalentMappings',
        'entertainmentStreamContentMappings',
        'entertainmentReviews' => function ($query) {
            $query->whereBetween('rating', [4, 5]);
        }
    ])
    ->where('status', 1);


    // Fetch TV shows
    $tvshowList = $entertainment_data->where('type', 'tvshow')->take(10)->get();
    $tvshowData = (isenablemodule('tvshow') == 1) ? TvshowResource::collection($tvshowList) : [];


        return view('frontend::search', compact('movieData', 'tvshowData'));
    }

    public function tvshowList()
    {

        return view('frontend::movie');
    }

    public function continueWatchList()
    {
        return view('frontend::continueWatch');
    }

    public function languageList()
    {
        $languageIds = MobileSetting::getValueBySlug('enjoy-in-your-native-tongue');
        $popular_language = Constant::whereIn('id',json_decode($languageIds))->get();

        return view('frontend::language',compact('popular_language'));
    }
    public function languageData(Request $request){
        $perPage = $request->input('per_page', 10);
        $languageIds = MobileSetting::getValueBySlug('enjoy-in-your-native-tongue');
        $popular_language = Constant::whereIn('id',json_decode($languageIds));

        $html = '';
        $popular_language = $popular_language->paginate($perPage);
            foreach($popular_language as $language) {
                $html .= view('frontend::components.card.card_language',['popular_language' => $language])->render();
            }
            $hasMore = $popular_language->hasMorePages();
            return response()->json([
                'status' => true,
                'html' => $html,
                'message' => __('movie.tvshow_list'),
                'hasMore' => $hasMore,
            ], 200);
    }
    public function topChannelList()
    {
        return view('frontend::topChannel');
    }
    public function genresList()
    {
        return view('frontend::genres');
    }

    public function comingsoon()
    {
        return view('frontend::comingsoon');
    }
    public function livetv()
    {
        return view('frontend::livetv');
    }
    public function subscriptionPlan()
    {
        $plans = Plan::with('planLimitation')->get();
        $activeSubscriptions = Subscription::where('user_id', auth()->id())->where('status', 'active')->where('end_date', '>', now())->orderBy('id','desc')->first();
        $currentPlanId = $activeSubscriptions ? $activeSubscriptions->plan_id : null;
        $subscriptions = Subscription::where('user_id', auth()->id())
        ->with('subscription_transaction')
        ->where('end_date', '<', now())
        ->get();

        return view('frontend::subscriptionplan', compact('plans','currentPlanId','activeSubscriptions'));
    }
    public function watchList()
    {
        return view('frontend::watchlist');
    }

    public function accountSetting()
    {
        $user = auth()->user();

         $subscriptions = Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->orderBy('id','desc')
            ->first();

        $devices = $user->devices;

        $your_device = null;
        $other_devices = [];

        $currentDeviceIp = request()->getClientIp();

        foreach ($devices as $device) {
            if ($device->device_id == $currentDeviceIp) {
                $your_device = $device;
            } else {
                $other_devices[] = $device;
            }
        }

        return view('frontend::accountSetting', compact('subscriptions', 'user', 'your_device', 'other_devices'));
    }


    public function deviceLogout(Request $request)
    {
        $userId = auth()->user()->id;

        $deviceQuery = Device::where('user_id', $userId);

        if ($request->has('device_id')) {
            $deviceQuery->where('device_id', $request->device_id);
        }

        if ($request->has('id')) {
            $deviceQuery->orWhere('id', $request->id);
        }

        $device = $deviceQuery->first();
        if (!$device) {
            return redirect()->back()->with('error', __('users.device_not_found'));
        }

        $device->delete();

        $sessionQuery = DB::table('sessions')->where('user_id', $userId);

        if ($request->has('device_id')) {
            $sessionQuery->where('ip_address', $request->device_id);
        }

        if ($request->has('id')) {
            $sessionQuery->orWhere('id', $request->id);
        }

        $session = $sessionQuery->first();
        if ($session) {
            $sessionQuery->delete();
        }

        return redirect()->back()->with('success', __('users.device_logout'));
    }


    public function faq()
    {
        $content = FAQ::where('status',1)->get();
        return view('frontend::faq',compact('content'));
    }


    public function PaymentHistory()
    {
        $subscriptions = Subscription::where('user_id', auth()->id())
        ->with('subscription_transaction')
        ->get();

        $activeSubscriptions =  Subscription::where('user_id', auth()->id())
        ->where('status', 'active')
        ->where('end_date', '>', now())
        ->orderBy('id', 'desc')
        ->first();


        return view('frontend::paymentHistory', compact('activeSubscriptions', 'subscriptions'));

    }


    public function allReview($id)
    {
        $entertainment = Entertainment::findOrFail($id);
        $reviews = $entertainment->entertainmentReviews;
        $ratingCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
        foreach ($reviews as $review) {
            if (isset($ratingCounts[$review->rating])) {
                $ratingCounts[$review->rating]++;
            }
        }
        $totalRating = $reviews->sum('rating');
        $reviewCount = $reviews->count();
        $averageRating = $reviewCount > 0 ? $totalRating / $reviewCount : 0;

        return view('frontend::review', compact('entertainment', 'reviews', 'averageRating', 'ratingCounts', 'reviewCount'));
    }

    public function EpisodeDetails()
    {
        return view('frontend::episode_detail');
    }

    public function VideoDetails()
    {
     return view('frontend::video_detail');
    }

    public function profile()
    {
        return view('frontend::components.user.profile');
    }

    public function cancelSubscription(Request $request)
    {
        try {
            $planId = $request->input('plan_id');
            Subscription::where('user_id', auth()->id())
                ->where('id', $request->id)
                ->where('status', 'active')
                ->update(['status' => 'cancel']);

            $otherSubscription=Subscription::where('user_id', auth()->id())
                ->where('status', 'active')->get();

            if($otherSubscription->isEmpty()){

                $user=User::where('id',auth()->id() )->first();

                $user->update(['is_subscribe'=>0]);

            }



            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function decryptUrl(Request $request)
  {
      $encryptedUrl = $request->input('encrypted_url');

      try {
          $decryptedUrl = Crypt::decryptString($encryptedUrl);
          return response()->json(['url' => $decryptedUrl], 200);
      } catch (\Exception $e) {
          return response()->json(['error' => 'Invalid URL'], 400);
      }
  }
    public function getPaymentDetails(Request $request)
    {
        $planId = $request->input('plan_id');
        $plan = Plan::find($planId);
        $discount_percentage = $plan->discount_percentage;

        $discount_amount= ($discount_percentage*$plan->price)/100;

        $taxes=Tax::where('status',1)->get();
        $baseAmount = $plan->total_price;
        $totalTaxamount = 0;
        $totalTax=0;
        $taxesArray = [];
        foreach ($taxes as $tax) {

            if (strtolower($tax->type) == 'fixed') {

                $totalTax = $tax->value;

            } elseif (strtolower($tax->type) == 'percentage') {

                $totalTax = ($baseAmount * $tax->value) / 100;

            }

            $taxesArray[] = [
                'name' => $tax->title, // Assuming there's a 'name' field for the tax
                'type' => $tax->type,
                'value' => $tax->value,
                'tax_amount' => $totalTax
            ];

            $totalTaxamount +=  $totalTax;
        }

        $totalAmount = $baseAmount + $totalTaxamount;
        $total = $totalAmount ;

        return response()->json([
            'price'=>$plan->price,
            'subtotal' => $baseAmount,
            'discount' => $discount_percentage,
            'discount_amount'=>$discount_amount,
            'tax' => $totalTaxamount ,
            'total' => $total,
            'tax_array'=> $taxesArray
        ]);
    }

    public function checkSubscription($planId)
   {
    $user = auth()->user();
    $currentSubscription = Subscription::where('user_id', $user->id)->where('status', 'active')->get();

    $planData=Plan::Where('id',$planId)->first();

    $level=$planData->level;

    foreach($currentSubscription as $plan)
    {

        if ($plan->level >= $level) {
            return response()->json(['isActive' => true]);
        }
    }
    return response()->json(['isActive' => false]);
   }


   public function checkDeviceType() {
        $checkDeviceType = Subscription::checkPlanSupportDevice(auth()->id());
        return $checkDeviceType;
    }


    public function downloadInvoice(Request $request)
    {
        // Retrieve the booking by ID with related services, user, and products
        $subscription = Subscription::with('plan','subscription_transaction','user')->find($request->id);
            if (!$subscription) {
                return response()->json(['status' => false, 'message' => 'subscription not found'], 404);
            }


        // Render the view for the invoice
        $view = view('frontend::components.partials.invoice',['data' => $subscription])->render();

        // Generate the PDF from the rendered view
        $pdf = Pdf::loadHTML($view);

        // Return the generated PDF as a download
        return $pdf->download('invoice.pdf');
    }



}


