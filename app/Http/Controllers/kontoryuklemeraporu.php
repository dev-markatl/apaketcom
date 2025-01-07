<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class kontoryuklemeraporu extends Controller
{
    public function getIndex()
    {

    	return view("raporlar-kontoryuklemeraporu");
    }	 
}
