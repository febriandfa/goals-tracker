<?php

namespace App\Http\Controllers;

use App\Models\Goals;
use App\Models\Progress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{

            if ($request->id) {
                $progress = Progress::with(['goals', 'goals.users'])->where('id', $request->id)->get();
            } else if ($request->goal_id) {
                $progress = Progress::with(['goals', 'goals.users'])->where('goal_id', $request->goal_id)->get();
            } else {
                $progress = Progress::with(['goals', 'goals.users'])->get();
            }
    
            return response()->json(['succes' => true, 'data' => $progress, 'message' => "Berhasil Mendapatkan Data Progress"]);
        }
        catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->only(['goal_id', 'name', 'value']),[
                'goal_id' => 'required|numeric',
                'name' => 'required|string|max:255',
                'value' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $goals = Goals::find($request->input('goal_id'));

            if (!$goals) {
                return response()->json(['success' => false, 'message' => 'Goal Tidak Ditemukan']);
            }

            $progress = Progress::create([
                'goal_id' => $request->input('goal_id'),
                'name' => $request->input('name'),
                'value' => $request->input('value'),
            ]);

            $goals->current_value += $request->input('value');
            $goals->save();

            return response()->json([
                'success' => true, 'data' => $progress, 'message' => 'Create Progress Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $progress = Progress::find($id);

            $goals = Goals::find($progress->goal_id);

            $validator = Validator::make($request->only(['name', 'value']),[
                'name' => 'required|string|max:255',
                'value' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $oldValue = $progress->value;

            $progressUpdate = $request->only(['name', 'value']);

            $progress->update($progressUpdate);

            $goals->current_value += ($request->input('value') - $oldValue);
            $goals->save();

            return response()->json([
                'success' => true, 'data' => $progress, 'message' => 'Update Progress Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $progress = Progress::find($id);

            $oldValue = $progress->value;

            $goals = Goals::find($progress->goal_id);
            $goals->current_value -= $oldValue;
            $goals->save();

            $progress->delete();

            return response()->json([
                'success' => true, 'data' => $progress, 'message' => 'Delete Progress Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
