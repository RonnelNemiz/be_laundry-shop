<?php

namespace App\Http\Controllers;
use Exception;
use Carbon\Carbon; 
use App\Models\Order;
use Illuminate\Http\Request;


class SalesController extends Controller
{
    public function totalsales(){

        try {
            // Calculate total sales for today
            $today = Carbon::now()->format('Y-m-d');
            $totalTodaySales = Order::whereDate('created_at', $today)->sum('total');
    
            // Calculate total sales for the current week
            $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
            $totalWeekSales = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total');
    
            // Calculate total sales for the current month
            $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
            $totalMonthSales = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');
    
            return response()->json([
                'status' => 200,
                'sales' => [
                    'today' => $totalTodaySales,
                    'week' => $totalWeekSales,
                    'month' => $totalMonthSales,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to calculate sales.',
            ]);
        }
    }
}
