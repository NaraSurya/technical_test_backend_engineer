<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
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
            return Vacancy::all();
        } else {
            return response()->json("please add Accept:application/json in Headers", 406);
        }
    }

    
    private function checkVacancyExist($request)
    {
        return Vacancy::where('vacancy_name', $request->vacancy_name)
                        ->where('min_age',$request->min_age)
                        ->where('max_age',$request->max_age)
                        ->where('requirement_gender',$request->requirement_gender)
                        ->where('expired_date',$request->expired_date)
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
            'vacancy_name' => 'required|string',
            'min_age' => 'required|integer',
            'max_age' => 'required|integer',
            'requirement_gender'=>'required|in:Male,Female,All',
            'expired_date' => 'required|date'
        ]);
        
        if($this->checkVacancyExist($request)){
            return response()->json("The vacancy already exist", 406);
        }

        $vacancy = new Vacancy;
        $vacancy->vacancy_name = $request->vacancy_name;
        $vacancy->min_age = $request->min_age;
        $vacancy->max_age = $request->max_age;
        $vacancy->requirement_gender = $request->requirement_gender;
        $vacancy->expired_date = $request->expired_date;
        $vacancy->save();
        return $vacancy;
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        if($request->search){
            $request->search = trim( preg_replace( "/[^0-9a-z-]+/i", " ", $request->search ) );
            $vacancy = Vacancy::where('vacancy_name','LIKE','%'.$request->search.'%')
                                ->orWhere('min_age',$request->search)
                                ->orWhere('max_age',$request->search)
                                ->orWhere('requirement_gender',$request->search)
                                ->orWhere('expired_date',$request->search)
                                ->get();
        } else {
            $vacancy = Vacancy::all();
        }
        $sortBy = $this->collectOrderBy($request);
        $sortedVacancy = $vacancy->sortBy($sortBy);
        return $sortedVacancy->values()->all();
    }

    private function collectOrderBy(Request $request)
    {
        $orderByCollection = [];
        if($request->orderByName) array_push($orderByCollection, ['vacancy_name',$request->orderByName]);
        if($request->orderByMinAge) Array_push($orderByCollection, ['min_age',$request->orderByMinAge]);
        if($request->orderByMaxAge) Array_push($orderByCollection, ['max_age',$request->orderByMaxAge]);
        if($request->orderByRequirementGender) Array_push($orderByCollection, ['requirement_gender',$request->orderByRequirementGender]);
        if($request->orderByExpiredDate) Array_push($orderByCollection, ['expired_date',$request->orderByExpiredDate]);
        return $orderByCollection;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 

        $validated = $request->validate([
            'vacancy_name' => 'string',
            'min_age' => 'integer',
            'max_age' => 'integer',
            'requirment_gender'=>'in:Male,Female,All',
            'expired_date' => 'date'
        ]);

        $vacancy = Vacancy::find($request->vacancy_id);
        if($request->vacancy_name) $vacancy->vacancy_name = $request->vacancy_name;
        if($request->min_age) $vacancy->min_age = $request->min_age;
        if($request->max_age) $vacancy->max_age = $request->max_age;
        if($request->gender_requirement) $vacancy->requirement_gender = $request->gender_requirement;
        if($request->expired_date) $vacancy->expired_date = $request->expired_date;
        if($this->checkVacancyExist($vacancy)){
            return response()->json("The vacancy already exist or Your data is the same as before the update , You are not allowed to change it to avoid duplicate data", 406);
        }
        $vacancy->save();
        return $vacancy;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vacancy  $vacancy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!$request->wantsJson()){
            return response()->json("please add Accept:application/json in Headers", 406);
        } 
        $vacancy = Vacancy::find($request->vacancy_id);
        $vacancy->candidateApplys->each(function($apply){
            $apply->delete();
        });
        $vacancy->delete();
        return response()->json("Vacancy Successfuly Deteled", 200);
    }
}
