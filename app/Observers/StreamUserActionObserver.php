<?php
namespace App\Observers;

use App\Helpers\Helper;
use App\Models\StreamUserAction;
use App\Models\UserStream;
use Illuminate\Support\Facades\DB;

class StreamUserActionObserver
{
    public function created(StreamUserAction $model)
    {
        // Increase values of likes / dislikes / views / shares if type is respective.
        if(in_array($model->type, [StreamUserAction::TYPE_VIEW,StreamUserAction::TYPE_SHARE,StreamUserAction::TYPE_LIKE,StreamUserAction::TYPE_DISLIKE])){
            switch($model->type) {
                case StreamUserAction::TYPE_VIEW:
                    $model->stream->total_viewers++;
                    break;
                case StreamUserAction::TYPE_SHARE:
                    $model->stream->total_shares++;
                    break;
                case StreamUserAction::TYPE_LIKE:
                    $model->stream->total_likes++;
                    break;
                case StreamUserAction::TYPE_DISLIKE:
                    $model->stream->total_dislikes++;
                    break;

            }
            $model->stream->save();
        }
    }

    public function deleting(StreamUserAction $model)
    {
        // check if type is in [watch later / favorite / save ] then allow deleting else return false.
        if(!in_array($model->type, [StreamUserAction::TYPE_FAVORITE,StreamUserAction::TYPE_WATCH_LATER,StreamUserAction::TYPE_SAVE])){
            return false;
        }
        return true;
    }
}