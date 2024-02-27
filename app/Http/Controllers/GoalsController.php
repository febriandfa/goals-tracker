<?php

namespace App\Http\Controllers;

use App\Models\Goals;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class GoalsController extends Controller
{
    public function index(Request $request)
    {
        try{
            if ($request->id) 
                $goals = Goals::with(['progress','users'])->where('id', $request->id)->get();
            else if ($request->keyword)
                $goals = Goals::with(['progress','users'])->where('name','like', '%'.$request->keyword.'%')->get();
            else if ($request->user_id)
                $goals = Goals::with(['progress','users'])->where('user_id', $request->user_id)->get();
            else
                $goals = Goals::with(['progress','users'])->get();

            foreach ($goals as $goal) {
                $shortage = $goal->price - $goal->current_value;
                if ($shortage < 0) {
                    $goal["shortage"] = 0;
                } else {
                    $goal["shortage"] = $shortage;
                }
            }

            foreach ($goals as $goal) {
                $totalPercentage = ($goal->current_value / $goal->price) * 100;
                if($totalPercentage > 100){
                    $goal["total_percentage"] = number_format(100, 2);
                }else{
                    $goal["total_percentage"] = number_format($totalPercentage, 2);
                }
            }
            
            return response()->json(['success' => true, 'data' => $goals, 'message' => "Berhasil Mendapatkan Data Goals"]);
        }
        catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->only(['user_id', 'name', 'dateline','price']),[
                'user_id' => 'required|numeric',
                'name' => 'required|string|max:255',
                'dateline' => 'required|max:255',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $goals = Goals::create([
                'user_id' => $request->input('user_id'),
                'name' => $request->input('name'),
                'dateline' => $request->input('dateline'),
                'price' => $request->input('price'),
            ]);

            return response()->json([
                'success' => true, 'data' => $goals, 'message' => 'Create Goals Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $goals = Goals::find($id);

            $validator = Validator::make($request->only(['name', 'dateline','price']),[
                'name' => 'required|string|max:255',
                'dateline' => 'required|max:255',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $goalsUpdate = $request->only(['name', 'dateline','price']);

            $goals->update($goalsUpdate);
            
            return response()->json([
                'success' => true, 'data' => $goals, 'message' => 'Update Goals Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $goals = Goals::find($id);
        $goals->progress()->delete();
        $goals->delete();

        return response()->json([
            'success' => true, 'message' => 'Goals Dihapus'
        ]);
    }

    public function totalPercentProgress(){
        // 
    }
}
