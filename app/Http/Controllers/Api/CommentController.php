<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Report $report)
    {
        return response()->json([
            'status' => true,
            'data' => $report->comments()->with('user')->latest()->get(),
        ]);
    }

    public function store(Request $request, Report $report)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $report->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => $comment->load('user'),
        ], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update(['content' => $request->content]);

        return response()->json([
            'message' => 'Komentar berhasil diperbarui',
            'data' => $comment
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
