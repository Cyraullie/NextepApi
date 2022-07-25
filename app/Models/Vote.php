<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;



    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function voting_topic()
    {
        return $this->hasOne(VotingTopic::class);
    }
}
