<?php

namespace App\Http\Controllers;

use App\Models\ManualScoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManualScoringController extends Controller
{
    public function index()
    {
        try {
            $manualScorings = ManualScoring::all();
            $data = ['manual_scorings' => $manualScorings];
            return $this->returnJsonResponse(true, 'Manual Scorings successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|in:Hot,Cold,Warm',
        ]);

        try {
            $manualScoring = ManualScoring::create($request->all());
            $data = ['manual_scoring' => $manualScoring];
            return $this->returnJsonResponse(true, 'Manual Scoring successfully created', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $manualScoring = ManualScoring::findOrFail($id);
            $data = ['manual_scoring' => $manualScoring];
            return $this->returnJsonResponse(true, 'Manual Scoring successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|in:Hot,Cold,Warm',
        ]);

        try {
            $manualScoring = ManualScoring::findOrFail($id);
            $manualScoring->update($request->all());
            $data = ['manual_scoring' => $manualScoring];
            return $this->returnJsonResponse(true, 'Manual Scoring successfully updated', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        try {
            $manualScoring = ManualScoring::findOrFail($id);
            $manualScoring->delete();
            $data = [
                'manual_scoring' => $manualScoring,
            ];
            return $this->returnJsonResponse(true, 'Manual Scoring successfully deleted', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }
}
