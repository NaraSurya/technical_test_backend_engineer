<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->wantsJson()){
            return Candidate::all();
        } else {
            return response()->json("please add Accept:application/json in Headers", 406);
        }
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function checkCandidateExist($request)
    {
        return Candidate::where('full_name',$request->full_name)
                        ->where('dob',$request->dob)
                        ->where('gender',$request->gender)
                        ->exists();
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
            'full_name' => 'required|string',
            'dob' => 'required|date',
            'gender'=>'required|in:Male,Female'
        ]);

        
        if($this->checkCandidateExist($request)){
            return response()->json("The candidate already exist ", 406);
        }

        $candidate = new Candidate;
        $candidate->full_name = $request->full_name;
        $candidate->dob = $request->dob;
        $candidate->gender = $request->gender;
        $candidate->save();
        return $candidate;
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        if($request->search){
            $request->search = trim( preg_replace( "/[^0-9a-z-]+/i", " ", $request->search ) );
            $candidate = Candidate::where('full_name','LIKE','%'.$request->search.'%')
                                ->orWhere('dob',$request->search)
                                ->orWhere('gender',$request->search)
                                ->get();
        } else {
            $candidate = Candidate::all();
        }
        $sortBy = $this->collectOrderBy($request);
        $sortedCandidate = $candidate->sortBy($sortBy);
        return $sortedCandidate->values()->all();
    }

   
    private function collectOrderBy(Request $request)
    {
        $orderByCollection = [];
        if($request->orderByName) array_push($orderByCollection, ['full_name',$request->orderByName]);
        if($request->orderByDob) Array_push($orderByCollection, ['dob',$request->orderByDob]);
        if($request->orderByGender) Array_push($orderByCollection, ['gender',$request->orderByGender]);
        return $orderByCollection;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        $validated = $request->validate([
            'full_name' => 'string',
            'dob' => 'date',
            'gender'=>'in:Male,Female'
        ]);

        $candidate = Candidate::find($request->candidate_id);
        if($request->full_name) $candidate->full_name = $request->full_name;
        if($request->dob) $candidate->dob = $request->dob;
        if($request->gender) $candidate->gender = $request->gender;
        if($this->checkCandidateExist($candidate)){
            return response()->json("The candidate already exist or Your data is the same as before the update , You are not allowed to change it to avoid duplicate data", 406);
        }

        $candidate->save();
        return $candidate;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        $candidate = Candidate::find($request->candidate_id);
        $candidate->candidateApplys->each(function($apply){
            $apply->delete();
        });
        $candidate->delete();
        return response()->json("Candidate Successfuly Deteled", 200);
    }
}
