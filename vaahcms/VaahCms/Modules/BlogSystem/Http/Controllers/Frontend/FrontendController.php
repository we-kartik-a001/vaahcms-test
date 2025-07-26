<?php

namespace VaahCms\Modules\BlogSystem\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VaahCms\Modules\BlogSystem\Mails\NewsLetterEmailMail;
use VaahCms\Modules\BlogSystem\Models\Blog;
use VaahCms\Modules\BlogSystem\Models\Category;
use VaahCms\Modules\BlogSystem\Models\NewsLetter;
use VaahCms\Modules\BlogSystem\Models\Tag;
use WebReinvent\VaahCms\Libraries\VaahMail;

class FrontendController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $query = Blog::with(['category', 'tags']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tag_id', $request->tag);
            });
        }

        $blogs = $query->paginate(6);
        $categories = Category::all();
        $tags = Tag::all();

        // Add this for detail page support
        $detail = null;
        if ($request->filled('blog')) {
            $detail = Blog::with(['category', 'tags'])
                ->where('slug', $request->blog)
                ->first();
        }

        return view('blogsystem::frontend.pages.index', compact('blogs', 'categories', 'tags', 'detail'));
    }

    public function show($slug)
    {
        $blog = Blog::with(['category', 'tags'])->where('slug', $slug)->firstOrFail();
        return view('blogsystem::frontend.pages.show', compact('blog'));
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:' . NewsLetter::class . ',email'],
        ]);

        $subscriber = NewsLetter::create([
            'email' => $validated['email'],
        ]);

        VaahMail::addInQueue(
            new NewsLetterEmailMail($subscriber),
            $subscriber->email
        );

        return back()->with('message', 'Thank you for subscribing!');
    }
}
