<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *http://127.0.0.1:8000/install/database#context
     * @return void
     */
    public function run()
    {
        \Artisan::call('cache:clear');
        Schema::disableForeignKeyConstraints();
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public');
        $this->call(AuthTableSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(UserProfilesSeeder::class);
        $this->call(UserSearchHistoriesSeeder::class);
        $this->call(UserWatchHistoriesSeeder::class);
         $this->call(\Modules\World\database\seeders\WorldDatabaseSeeder::class);
        $this->call(\Modules\Banner\database\seeders\BannerDatabaseSeeder::class);
        $this->call(\Modules\Constant\database\seeders\ConstantDatabaseSeeder::class);
        // $this->call(\Modules\Genres\database\seeders\GenresDatabaseSeeder::class);
        // $this->call(\Modules\CastCrew\database\seeders\CastCrewDatabaseSeeder::class);
        // $this->call(\Modules\Entertainment\database\seeders\EntertainmentDatabaseSeeder::class);
        // $this->call(\Modules\Entertainment\database\seeders\ReviewDatabaseSeeder::class);
        // $this->call(\Modules\Season\database\seeders\SeasonDatabaseSeeder::class);
        // $this->call(\Modules\Episode\database\seeders\EpisodeDatabaseSeeder::class);
        $this->call(\Modules\Page\database\seeders\PageDatabaseSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanlimitationTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\SubscriptionsTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\PlanlimitationMappingTableSeeder::class);
        $this->call(\Modules\Subscriptions\database\seeders\SubscriptionsTransactionsTableSeeder::class);
        // $this->call(\Modules\Video\database\seeders\VideoDatabaseSeeder::class);
        $this->call(\Modules\NotificationTemplate\database\seeders\NotificationTemplateSeeder::class);
        // $this->call(\Modules\Entertainment\database\seeders\EntertainmentViewsTableSeeder::class);
        $this->call(\Modules\Entertainment\database\seeders\WatchlistDatabaseSeeder::class);
        $this->call(\Modules\Entertainment\database\seeders\LikesTableSeeder::class);
        $this->call(\Modules\FAQ\database\seeders\FAQDatabaseSeeder::class);
        $this->call(\Modules\Tax\database\seeders\TaxDatabaseSeeder::class);
        $this->call(\Modules\Entertainment\database\seeders\ContinueWatchTableSeeder::class);
        $this->call(\Modules\Currency\database\seeders\CurrencyDatabaseSeeder::class);
        Schema::enableForeignKeyConstraints();
        \Artisan::call('cache:clear');
        $this->call(LiveTvCategoryTableSeeder::class);
        $this->call(LiveTvChannelTableSeeder::class);
        $this->call(MobileSettingsTableSeeder::class);

        $this->call(EntertainmentsTableSeeder::class);
        $this->call(EntertainmentCountryMappingTableSeeder::class);
        $this->call(EntertainmentDownloadsTableSeeder::class);
        $this->call(EntertainmentGenerMappingTableSeeder::class);
        $this->call(EntertainmentStreamContentMappingTableSeeder::class);
        $this->call(EntertainmentDownloadMappingTableSeeder::class);
        $this->call(EntertainmentTalentMappingTableSeeder::class);
        $this->call(EntertainmentViewsTableSeeder::class);
        $this->call(EpisodesTableSeeder::class);
        $this->call(EpisodeDownloadMappingTableSeeder::class);
        $this->call(EpisodeStreamContentMappingTableSeeder::class);
        $this->call(GenresTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(LiveTvStreamContentMappingTableSeeder::class);
        $this->call(MediaTableSeeder::class);
        $this->call(ModelHasPermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(ReviewsTableSeeder::class);
        $this->call(SeasonsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UserMultiProfilesTableSeeder::class);
        $this->call(UserReminderTableSeeder::class);
        $this->call(VideosTableSeeder::class);
        $this->call(VideoDownloadMappingsTableSeeder::class);
        $this->call(VideoStreamContentMappingTableSeeder::class);
        $this->call(CastCrewTableSeeder::class);

        \Artisan::call('cache:clear');
        \Artisan::call('storage:link');

    }
}

