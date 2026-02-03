<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dalolatnoma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestInlineEditController extends Controller
{
    public function saveField(Dalolatnoma $test, Request $request)
    {
        // $this->authorize('update', $test);

        $data = $request->validate([
            'field' => ['required', 'string', 'in:invoice_number,vehicle_number'],
            'value' => ['nullable', 'string', 'max:255'],
        ]);

        $field = $data['field'];
        $value = $data['value'];

        // Ensure final results record exists and update it.
        $final = $test->final_conclusion_result;
        if (!$final) {
            return response()->json([
                'success' => false,
                'message' => 'Final result not found for this test.',
            ], 404);
        }

        $final->update([$field => $value]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function saveConclusion(Dalolatnoma $test, Request $request)
    {
        // $this->authorize('update', $test);

        $data = $request->validate([
            'field' => ['required', 'string', 'in:conclusion_part_1,conclusion_part_2,conclusion_part_3'],
            'value' => ['nullable', 'string', 'max:1025'],
        ]);

        $field = $data['field'];
        $value = $data['value'];

        // Ensure final results record exists and update it.
        $final = $test->final_conclusion_result;
        if (!$final) {
            return response()->json([
                'success' => false,
                'message' => 'Final result not found for this test.',
            ], 404);
        }

        $final->update([$field => $value]);

        return response()->json([
            'success' => true,
        ]);
    }
}
