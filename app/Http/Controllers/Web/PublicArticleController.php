<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicArticleController extends Controller
{
    public function index(Request $request): View
    {
        return view('blog.index', [
            'articles' => Article::query()
                ->with('author')
                ->withCount([
                    'approvedComments as approved_comments_count',
                ])
                ->published()
                ->search($request->string('search')->toString())
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString(),
            'featuredArticle' => Article::query()
                ->with('author')
                ->withCount([
                    'approvedComments as approved_comments_count',
                ])
                ->published()
                ->latest('published_at')
                ->first(),
        ]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->is_published && (! $article->published_at || $article->published_at->lte(now())), 404);

        $article->load([
            'author',
            'approvedComments.user',
        ]);

        return view('blog.show', [
            'article' => $article,
            'relatedArticles' => Article::query()
                ->with('author')
                ->published()
                ->where('id', '!=', $article->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
