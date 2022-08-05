<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function votingTopics()
    {
        $topics = VotingTopic::all()->where("enable", "=", 1);

        $topics_Array = [];

        foreach ($topics as $topic){
            array_push($topics_Array, [
                'id' => $topic->id,
                'creation_date' => $topic->created_at,
                'subject' => $topic->subject,
                'description' => $topic->description,
                'vote' => Vote::all()->where("user_id", "=", Auth::user()->user_id)->where("topic_id", "=", $topic->id),
                'up_vote' => count(Vote::all()->where("topic_id", "=", $topic->id)->where("isDownVote", "=", 0)),
                'down_vote' => count(Vote::all()->where("topic_id", "=", $topic->id)->where("isDownVote", "=", 1)),
            ]);
        }


        return $topics_Array;
    }

    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::user()->user_id);
            if($user->role->slug == "ADM"){

                $topic = new VotingTopic();
                $topic->subject = $request->input('subject');
                $topic->description = $request->input('description');
                $topic->enable = 1;
                $topic->save();

                return response("Ok", 200);
            }
            return response('Bad request: no permission', 400);
        } catch (\Exception $e) {
            return response('Bad request:' . $e->getMessage(), 400);
        }
    }

    public function vote(Request $request, $id)
    {
        try {
            $topic = VotingTopic::find($id);
            $user = User::find(Auth::user()->user_id);

            $vote = new Vote();
            $vote->user_id = $user->id;
            $vote->topic_id = $topic->id;
            if($request->input("vote") == 0){
                $vote->isDownVote = true;
            } elseif ($request->input("vote") == 1) {
                $vote->isDownVote = false;
            }
            $vote->save();

            return response("Ok", 200);
        } catch (\Exception $e) {
            return response('Bad request:' . $e->getMessage(), 400);
        }
    }

    public function disableTopic($id){
        try{
            $user = User::find(Auth::user()->user_id);
            if($user->role->slug == "ADM") {
                $topic = VotingTopic::find($id);
                if ($topic->enable) {
                    $topic->enable = 0;
                    $topic->save();
                    return response("Ok", 200);
                }
                return response("Bad request: topic already disable", 400);
            }
            return response("Bad request: no permission", 400);
        } catch (\Exception $e) {
            return response('Bad request:' . $e->getMessage(), 400);
        }
    }
}
