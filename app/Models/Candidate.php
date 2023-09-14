<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Candidate extends Model
{
    protected $table = 'candidate';
    protected $primaryKey = 'candidate_id';
    public $timestamps = false;

    public function candidateApplys()
    {
        return $this->hasMany(candidateApply::class, 'candidate_id');
    }
}
