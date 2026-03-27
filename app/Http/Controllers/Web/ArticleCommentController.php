<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleCommentRequest;
use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\RedirectResponse;

class ArticleCommentController extends Controller
{
    public function store(ArticleCommentRequest $request, Article $article): RedirectResponse
    {
        abort_unless($article->is_published && $article->allow_comments, 404);

        $data = $request->validated();

        $article->comments()->create([
            'user_id' => $request->user()?->id,
            'guest_name' => $request->user()?->name ? null : $data['guest_name'],
            'guest_email' => $request->user()?->email ? null : $data['guest_email'],
            'body' => $data['body'],
            'is_approved' => true,
            'approved_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('blog.show', $article)->with('success', 'Comentario publicado correctamente.');
    }

    public function destroy(Article $article, ArticleComment $comment): RedirectResponse
    {
        abort_unless($comment->article_id === $article->id, 404);

        $comment->delete();

        return redirect()->route('articles.show', $article)->with('success', 'Comentario eliminado correctamente.');
    }
}
