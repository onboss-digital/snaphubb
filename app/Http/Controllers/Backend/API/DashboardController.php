<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MobileSetting;
use Modules\Entertainment\Models\Entertainment;
use Modules\Banner\Models\Banner;
use Modules\Entertainment\Models\ContinueWatch;
use Modules\Banner\Transformers\SliderResource;
use Modules\Entertainment\Transformers\ContinueWatchResource;
use Modules\LiveTV\Models\LiveTvChannel;
use Modules\LiveTV\Transformers\LiveTvChannelResource;
use Modules\CastCrew\Models\CastCrew;
use Modules\CastCrew\Transformers\CastCrewListResource;
use Modules\Genres\Transformers\GenresResource;
use Modules\Genres\Models\Genres;
use Modules\Video\Models\Video;
use App\Services\RecommendationService;
use Modules\Entertainment\Transformers\MoviesResource;
use Modules\Entertainment\Transformers\CommanResource;
use Modules\Constant\Models\Constant;



class DashboardController extends Controller
{
    protected $recommendationService;
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;

    }
    public function DashboardDetail(Request $request){

        $user_id = !empty($request->user_id) ? $request->user_id : null;
        $continueWatch = [];

        $isBanner = MobileSetting::getValueBySlug('banner');
        $sliderList = $isBanner
            ? Banner::where('status', 1)->get()
            : collect();

        $sliders = SliderResource::collection(
            $sliderList->map(fn($slider) => new SliderResource($slider, $user_id))
        );

       if($request->has('user_id')){
           $continueWatchList = ContinueWatch::where('user_id', $user_id)
           ->where('profile_id',$request->profile_id)->get();
           $continueWatch = ContinueWatchResource::collection($continueWatchList);

           $user = User::where('id',$request->user_id)->first();
           $profile_id=$request->profile_id;

           if( $user_id !=null){
               $user = User::where('id',$user_id)->first();

           $likedMovies = $this->recommendationService->getLikedMovies($user, $profile_id);
           $likedMovies = CommanResource::collection($likedMovies);
           $viewedMovies = $this->recommendationService->getEntertainmentViews($user, $profile_id);
           $viewedMovies = CommanResource::collection($viewedMovies);

           $favorite_gener = $this->recommendationService->getFavoriteGener($user, $profile_id);
           $FavoriteGener = GenresResource::collection($favorite_gener);
           $FavoriteGener = $FavoriteGener->toArray(request());

           $favorite_personality = $this->recommendationService->getFavoritePersonality($user, $profile_id);
           $favorite_personality = CastCrewListResource::collection($favorite_personality);
           $favorite_personality = $favorite_personality->toArray(request());

           $trendingMovies = $this->recommendationService->getTrendingMoviesByCountry($user, $request);
           $trendingMovies = CommanResource::collection($trendingMovies);

           }

       }



       $topMovieIds = MobileSetting::getValueBySlug('top-10');
       $topMovies = Entertainment::whereIn('id',json_decode($topMovieIds))->where('status',1)->get();
       $top_10 = CommanResource::collection($topMovies);
       $top_10 = $top_10->toArray(request: request());



       $latestMovieIds = MobileSetting::getValueBySlug('latest-movies');
       $latestMovie = Entertainment::whereIn('id',json_decode($latestMovieIds))->where('status',1)->get();
       $latest_movie = CommanResource::collection($latestMovie);




       $languageIds = MobileSetting::getValueBySlug('enjoy-in-your-native-tongue');
       $popular_language = Constant::whereIn('id',json_decode($languageIds))->get();



       $popularMovieIds = MobileSetting::getValueBySlug(slug: 'popular-movies');
       $popularMovies = Entertainment::whereIn('id',json_decode($popularMovieIds))->where('status',1)->get();
       $popular_movie = CommanResource::collection($popularMovies);



       $channelIds = MobileSetting::getValueBySlug('top-channels');
       $channels = LiveTvChannel::whereIn('id',json_decode($channelIds))->where('status',1)->get();
       $top_channel = LiveTvChannelResource::collection($channels);
       $top_channel = $top_channel->toArray(request());


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



       $movieIds = MobileSetting::getValueBySlug('500-free-movies');
       $free_movies_tvshow = Entertainment::whereIn('id',json_decode($movieIds))->where('status',1)->get();
       $free_movie = CommanResource::collection($free_movies_tvshow);



       $popular_tvshowIds = MobileSetting::getValueBySlug(slug: 'popular-tvshows');
       $popular_tvshow = Entertainment::whereIn('id',json_decode($popular_tvshowIds))->where('status',1)->get();
       $popular_tvshow = CommanResource::collection($popular_tvshow);



       $genreIds = MobileSetting::getValueBySlug(slug: 'genre');
       $genres = Genres::whereIn('id',json_decode($genreIds))->where('status',1)->get();
       $genres = GenresResource::collection($genres);
       $genres = $genres->toArray(request());


       $videoIds = MobileSetting::getValueBySlug(slug: 'popular-videos');
       $popular_videos = Video::whereIn('id',json_decode($videoIds))->where('status',1)->get();
       $popular_videos = CommanResource::collection($popular_videos);



       $entertainment_list = Entertainment::with([
           'entertainmentReviews' => function ($query) {
               $query->whereBetween('rating', [4, 5])->take(6);
           }
       ])->where('status', 1)
       ->where('type', 'movie')
       ->get();

       $tranding_movie = CommanResource::collection($entertainment_list);
       $responseData = [
           'slider' => $sliders,
           'continue_watch' => $continueWatch,
           'top_10' => $top_10,
           'latest_movie' => $latest_movie,
           'popular_language' => $popular_language,
           'popular_movie' => $popular_movie,
           'top_channel' => $top_channel,
           'personality' => $personality,
           'tranding_movie'=>$tranding_movie,
           'free_movie' => $free_movie,
           'genres' => $genres,
           'popular_tvshow' => $popular_tvshow,
           'popular_videos' => $popular_videos,
           'likedMovies' => $likedMovies ?? [],
           'viewedMovies' => $viewedMovies ?? [],
           'trendingMovies' => $trendingMovies ?? [],
           'favorite_gener' => $FavoriteGener ?? [],
           'favorite_personality' => $favorite_personality ?? [],
           'base_on_last_watch'=>$Lastwatchrecommendation ?? [],
       ];

       // Cache::put($cacheKey,$responseData);

       return response()->json([
           'status' => true,
           'data' => $responseData,
           'message' => __('messages.dashboard_detail'),
       ], 200);
}

    public function getTrandingData(Request $request){


        if ($request->has('is_ajax') && $request->is_ajax == 1) {

            $popularMovieIds = MobileSetting::getValueBySlug(slug: 'popular-movies');
            $movieList = Entertainment::whereIn('id',json_decode($popularMovieIds))->where('status',1)->get();

            $html = '';
            if($request->has('section')&& $request->section == 'tranding_movie'){
                $movieData = (isenablemodule('movie') == 1) ? MoviesResource::collection($movieList) : [];
                if(!empty( $movieData)){

                    foreach( $movieData->toArray(request()) as $index => $movie){
                        $html .= view('frontend::components.card.card_entertainment',['value' => $movie])->render();
                    }
                }
            }


        return response()->json([
                'status' => true,
                'html' => $html,
                'message' => __('movie.tvshow_list'),
            ], 200);
        }



    }
}
