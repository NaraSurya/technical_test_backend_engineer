<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancy';
    protected $primaryKey = 'vacancy_id';
    public $timestamps = false;

    public function candidateApplys()
    {
        return $this->hasMany(candidateApply::class, 'vacancy_id');
    }
}
