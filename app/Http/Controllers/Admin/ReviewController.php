<?php

namespace App\Http\Controllers\Admin;

use App\Model\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index() {
        $reviews = Review::orderBy('created_at', 'desc')
            ->with('user', 'order')
            ->paginate(10);

        return view('admin.dashboard.feedback.index', compact('reviews'))->with('page_title', 'View & Leave Feedback');
    }

    public function saveFeedback(Request $request) {
        if (isset($request->ids)) {
            for ($i=0; $i < sizeof($request->ids); $i++) {
                Review::where('id', $request->ids[$i])
                    ->update([
                        'reply' => $request->feedback[$i]
                    ]);
            }
        }

        return redirect()->back()->with('message', 'Save Feedback');
    }
}
