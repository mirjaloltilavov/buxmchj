<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\InstagramPost;
use App\Models\Service;
use App\Models\ServicePlan;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $articles = Article::published()->with('category')->take(5)->get();

        return view('pages.home', [
            'slides' => HeroSlide::active()->get(),
            'courses' => Course::active()->get(),
            'services' => Service::active()->where('on_home', true)->take(6)->get(),
            'faqs' => Faq::for('home')->get(),
            'featured' => $articles->firstWhere('is_featured', true) ?? $articles->first(),
            'articles' => $articles,
            'instagram' => InstagramPost::active()->get(),
        ]);
    }

    public function courses()
    {
        return view('pages.courses', [
            'courses' => Course::active()->get(),
            'teachers' => Teacher::active()->get(),
            'faqs' => Faq::for('courses')->get(),
        ]);
    }

    public function course(string $slug)
    {
        $course = Course::where('slug', $this->slugParam())->where('is_active', true)->firstOrFail();

        return view('pages.course', [
            'course' => $course,
            'teachers' => Teacher::active()->get(),
            'faqs' => Faq::for('courses')->get(),
        ]);
    }

    public function services()
    {
        return view('pages.services', [
            'services' => Service::active()->get(),
            'plans' => ServicePlan::active()->get(),
            'faqs' => Faq::for('services')->get(),
        ]);
    }

    public function blog(Request $request)
    {
        $categories = ArticleCategory::orderBy('sort')->withCount(['articles' => fn ($q) => $q->published()])->get();
        $active = $request->query('category');
        $q = trim((string) $request->query('q'));

        $query = Article::published()->with('category');
        if ($active) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $active));
        }
        if ($q !== '') {
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")->orWhere('excerpt', 'like', "%{$q}%"));
        }

        // Tanlangan maqola faqat filtrsiz ko'rinishda alohida chiqadi va gridda takrorlanmaydi
        $featured = (! $active && $q === '') ? Article::published()->with('category')->where('is_featured', true)->latest('published_at')->first() : null;
        if ($featured) {
            $query->whereKeyNot($featured->id);
        }

        return view('pages.blog', [
            'categories' => $categories,
            'activeCategory' => $active,
            'search' => $q,
            'featured' => $featured,
            'articles' => $query->paginate(12)->withQueryString(),
            'total' => Article::published()->count(),
        ]);
    }

    public function article(string $slug)
    {
        $article = Article::published()->with('category')->where('slug', $this->slugParam('article'))->firstOrFail();
        $related = Article::published()->whereKeyNot($article->id)
            ->when($article->article_category_id, fn ($q) => $q->orderByRaw('article_category_id = ? desc', [$article->article_category_id]))
            ->take(3)->get();

        return view('pages.article', compact('article', 'related'));
    }

    public function contact()
    {
        return view('pages.contact', [
            'branches' => Branch::active()->get(),
            'faqs' => Faq::for('contact')->get(),
            'instagram' => InstagramPost::active()->get(),
        ]);
    }

    public function privacy()
    {
        return view('pages.legal', ['key' => 'privacy']);
    }

    public function offer()
    {
        return view('pages.legal', ['key' => 'offer']);
    }

    /** Route parametri (uz va ru route'larda bir xil nom). */
    private function slugParam(string $name = 'course'): string
    {
        $value = request()->route($name);

        return is_object($value) ? $value->slug : (string) $value;
    }
}
