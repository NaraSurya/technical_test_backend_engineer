<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateApply extends Model
{
    protected $table = 'candidate_apply';
    protected $primaryKey = 'apply_id';
    public $timestamps = false;

    public function candidate()
    {
        return $this->belongsTo(Candidate::class,"candidate_id");
    }
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class,"vacancy_id");
    }

}
