<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;



class AdminController extends Controller
{
   public function index()
  {
    return view('dashboard.dashboard');
  }
}
