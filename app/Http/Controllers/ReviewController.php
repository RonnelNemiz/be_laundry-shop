<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
   
    public function index()
    {
        $reviews = Review::with('user')->get();

        return ReviewResource::collection($reviews);
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'rating' => 'required|numeric',
        'comment' => 'required|string',
    ]);

    $review = new Review([
        'user_id' => auth()->user()->id,
        'ratings' => $validatedData['rating'],
        'comments' => $validatedData['comment'],
    ]);

    $review->save();

    $review->load('user.profile'); // Eager load the 'user' relationship with 'profile'

    // Optionally, you can return a response or redirect the user
        return response()->json([
            $review,
            'status' => 200,
        ]);
    }

    public function reply(Request $request, $reviewId)
    {
        $review = Review::find($reviewId);
        if (!$review) {
            return response()->json([
                'status' => 500,
                'message' => "Reply Not Found!",
            ]);
        }
    
        $reply = $request->input('reply');
        $reply_at = date('Y-m-d H:i:s', strtotime($request->input('reply_at')));
    
        $review->reply = $reply;
        $review->reply_at = $reply_at; // Assign the converted value to $review->reply_at
    
        $review->save();
    
        return response()->json([
            'status' => 200,
            'message' => "Successfully Added!",
        ]);
    }

        public function getUserComments()
        {
            $userId = auth()->id(); // Get the authenticated user's ID
        
            $reviews = Review::where('user_id', $userId)->get();
        
            if ($reviews->isEmpty()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'User comments not found.',
                ]);
            }
        
            $comments = $reviews->pluck('comments')->toArray();
            $replies = $reviews->pluck('reply')->toArray();
        
            return response()->json([
                'status' => 200,
                'data' => [
                    'comments' => $comments,
                    'replies' => $replies,
                ],
            ]);
        }

    public function getAdminReply()
    {
        $review = Review::whereNotNull('reply')->first();

        if (!$review) {
            return response()->json([
                'status' => 500,
                'message' => 'Admin reply not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'reply' => $review->reply,
            ],
        ]);
    }

    public function destroy(Review $review)
    {
        try {
            $review->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete review'], 500);
        }
    
        return response()->json(['message' => 'Successfully deleted!']);
    }
    

}
