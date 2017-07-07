<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Jobs\FetchTwitterInfo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Friend;

class FetchTwitterInfoCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:fetch-info {--sync-all : Fetch twitter info for all friends}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Twitter info for conference friends.';

    /**
     * Twitter OAuth client.
     *
     * @var \Abraham\TwitterOAuth\TwitterOAuth
     */
    private $twitter;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TwitterOAuth $twitter)
    {
        parent::__construct();

        $this->twitter = $twitter;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $friends = Friend::get();

        if (! $this->option('sync-all')) {
            // @todo: Diff based on something else; this fails now :/
            // Fetch only the avatars that are missing from disk
            // $friends = $friends->filter(function ($friend) {
            //     return ! file_exists(public_path($friend->avatar));
            // });
        }

        // @todo: seperate twitter info from twitter pic so we can not re-pull
        // pic from everyone, and the test above can work again
        $friends->each(function ($friend) {
            $this->dispatch(new FetchTwitterInfo($friend));
        });
    }
}
