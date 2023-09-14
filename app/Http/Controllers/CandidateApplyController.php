<?php

namespace App\Http\Controllers;

use App\Models\CandidateApply;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\Vacancy;

class CandidateApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->wantsJson()){
            return CandidateApply::with(['candidate','vacancy'])->get();
        } else {
            return response()->json("please add Accept:application/json in Headers", 406);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   

        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidate',
            'vacancy_id' => 'required|exists:vacancy',
        ]);
        $candidate = Candidate::find($request->candidate_id);
        $vacancy = Vacancy::find($request->vacancy_id);
        if($vacancy->requirement_gender != 'All'&& $candidate->gender != $vacancy->requirement_gender){
            return response()->json("Sorry, you cannot apply for this vacancy due to different gender requirements", 406);
        }
        $candidateAge = Carbon::parse($candidate->dob)->age;
        if($vacancy->min_age != 0 && $vacancy->min_age > $candidateAge){
            return response()->json("Sorry, you cannot apply for this vacancy because you are not old enough to apply ", 406);
        }
        if($vacancy->max_age != 0 && $vacancy->max_age < $candidateAge){
            return response()->json("Sorry, you cannot apply for this vacancy because you are too old to apply ", 406);
        }
        $dateTime = Carbon::now();
        $apply = new CandidateApply;
        $apply->candidate_id = $request->candidate_id;
        $apply->vacancy_id = $request->vacancy_id;
        $apply->apply_date = $dateTime->toDateString();
        $apply->save(); 
        $apply->candidate;
        $apply->vacancy;
        return $apply;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CandidateApply  $candidateApply
     * @return \Illuminate\Http\Response
     */
    public function show(CandidateApply $candidateApply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CandidateApply  $candidateApply
     * @return \Illuminate\Http\Response
     */
    public function edit(CandidateApply $candidateApply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CandidateApply  $candidateApply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CandidateApply $candidateApply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CandidateApply  $candidateApply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $apply = CandidateApply::find($request->apply_id);
        $apply->delete();
        return response()->json("Apply Successfuly Deteled", 200);
    }
}
