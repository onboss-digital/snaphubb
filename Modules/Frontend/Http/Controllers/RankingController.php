<?php
namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileSetting;
use Modules\Constant\Models\Constant;
use Modules\Entertainment\Models\Entertainment;
use Modules\Entertainment\Transformers\MoviesResource;
use Illuminate\Support\Facades\Cache;
use Modules\LiveTV\Models\LiveTvChannel;
use Modules\LiveTV\Transformers\LiveTvChannelResource;
use Modules\Entertainment\Transformers\TvshowResource;
use Modules\CastCrew\Models\CastCrew;
use Modules\Genres\Transformers\GenresResource;
use Modules\Genres\Models\Genres;
use Modules\Video\Transformers\VideoResource;
use Modules\Video\Models\Video;
use App\Services\RecommendationService;
use Modules\Entertainment\Models\ContinueWatch;
use Modules\Entertainment\Transformers\ContinueWatchResource;
use Auth;
use Modules\CastCrew\Transformers\CastCrewListResource;

class RankingController extends Controller
{
}
