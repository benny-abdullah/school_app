<?php

namespace App\Http\Controllers;

use App\Services\AiStudentAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AiCopilotController extends Controller
{
    /**
     * Interaksi Chat / Quick Prompt dengan AI Copilot Kesiswaan
     */
    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'type'   => 'nullable|string|in:summary,risk,draft_sp,draft_wa,pedagogic,custom',
        ]);

        $prompt = trim($request->input('prompt'));
        $type = $request->input('type', 'custom');

        $reply = AiStudentAnalysisService::askGemini($prompt, $type);

        return response()->json([
            'success'   => true,
            'reply'     => $reply,
            'time'      => Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB',
            'model'     => env('GEMINI_API_KEY') ? 'Gemini 1.5 Flash' : 'STAR-KIDS Rule Engine',
        ]);
    }
}
