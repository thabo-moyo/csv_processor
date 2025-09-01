<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
        {
            //Basic response for now
            $persons = Person::paginate(100);
            return response()->json($persons);
        }
    public function processCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv'
        ]);

        $file = $request->file('file');

        return response()->json([
        'data' => [
        ]
        ]);
    }
}
