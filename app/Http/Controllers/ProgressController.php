<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    public function index()
    {
        try{
            $progress = Progress::all();
    
            return response()->json(['succes' => true, 'data' => $progress, 'message' => "Berhasil Mendapatkan Data Progress"]);
        }
        catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->only(['goal_id', 'name', 'value']),[
                'goal_id' => 'required|numeric',
                'name' => 'required|string|max:255',
                'value' => 'required|numeric|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $progress = Progress::create([
                'goal_id' => $request->input('goal_id'),
                'name' => $request->input('name'),
                'value' => $request->input('value'),
            ]);

            return response()->json([
                'success' => true, 'data' => $progress, 'message' => 'Create Progress Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $progress = Progress::find($id);

            $validator = Validator::make($request->only(['goal_id', 'name', 'value']),[
                'goal_id' => 'required|numeric',
                'name' => 'required|string|max:255',
                'value' => 'required|numeric|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $progressUpdate = $request->only(['goal_id', 'name', 'value']);

            $progress->update($progressUpdate);
            
            return response()->json([
                'success' => true, 'data' => $progress, 'message' => 'Update Progress Berhasil' 
            ]);
        }
        catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $progress = Progress::find($id);

        $progress->delete();

        return response()->json([
            'success' => true, 'message' => 'Progress Dihapus'
        ]);
    }
}
