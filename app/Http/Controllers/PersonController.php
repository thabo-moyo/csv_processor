<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
        {
            //Basic response for now - would add pagination
            $persons = Person::paginate(10);
            return response()->json($persons);
        }
}
