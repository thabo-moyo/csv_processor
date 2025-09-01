<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\PersonParser\HomeownerCsvProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function __construct(private HomeownerCsvProcessor $csvProcessor) {}

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

        $content = file_get_contents($file->getRealPath());

        $persons = $this->csvProcessor->processContent($content);

        return response()->json([
        'data' => [
            'persons' => $persons,
        ]
        ]);
    }
}
