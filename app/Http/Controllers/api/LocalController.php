<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LocalController extends Controller
{
   public function getLocal(){
   try{
       $user = User::all();
         return response()->json([
        'status' => true,
        'message' => "All Locals Retrieved Successfully",
        'data' => $user,
    ]); 
   } catch(\Exception $e){
    return response()->json([
        'status' => false,
        'message' => $e->getMessage(),
    ]);
   }
   }
}
