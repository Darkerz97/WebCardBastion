<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\AdminArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        return view('articles.index', [
            'articles' => Article::query()
                ->with('author')
                ->withCount('comments')
                ->search($request->string('search')->toString())
                ->when($request->filled('published'), function ($query) use ($request) {
                    $query->where('is_published', $request->boolean('published'));
                })
                ->latest('published_at')
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('articles.create', [
            'article' => new Article([
                'is_published' => true,
                'allow_comments' => true,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(AdminArticleRequest $request): RedirectResponse
    {
        $article = Article::create([
            ...$this->payload($request),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('articles.show', $article)->with('success', 'Entrada creada correctamente.');
    }

    public function show(Article $article): View
    {
        $article->load(['author', 'comments.user']);

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        return view('articles.edit', compact('article'));
    }

    public function update(AdminArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($this->payload($request, $article));

        return redirect()->route('articles.show', $article)->with('success', 'Entrada actualizada correctamente.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->cover_image_path) {
            Storage::disk('public')->delete($article->cover_image_path);
        }

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Entrada eliminada correctamente.');
    }

    private function payload(AdminArticleRequest $request, ?Article $article = null): array
    {
        $data = $request->validated();
        $coverImagePath = $article?->cover_image_path;

        if (! empty($data['remove_cover_image']) && $coverImagePath) {
            Storage::disk('public')->delete($coverImagePath);
            $coverImagePath = null;
        }

        if ($request->hasFile('cover_image')) {
            if ($coverImagePath) {
                Storage::disk('public')->delete($coverImagePath);
            }

            $coverImagePath = $request->file('cover_image')->store('articles', 'public');
        }

        $isPublished = (bool) $data['is_published'];
        $publishedAt = $isPublished
            ? (! empty($data['published_at']) ? $data['published_at'] : ($article?->published_at ?? now()))
            : null;

        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?: Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?: null,
            'content' => $data['content'],
            'cover_image_path' => $coverImagePath,
            'is_published' => $isPublished,
            'allow_comments' => (bool) $data['allow_comments'],
            'published_at' => $publishedAt,
        ];
    }
}
