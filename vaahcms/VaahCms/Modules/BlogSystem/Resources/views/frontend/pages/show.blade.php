<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $blog->name }} | My Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .prose {
            max-width: 100%;
        }
        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1.5rem auto;
        }
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.7;
        }
        .prose a {
            color: #4f46e5;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #4338ca;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <article class="bg-white rounded-xl shadow-md overflow-hidden p-8">
            <header class="mb-8">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full">
                        {{ $blog->category->name ?? 'Uncategorized' }}
                    </span>
                    @foreach($blog->tags as $tag)
                        <span class="bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
                
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $blog->name }}</h1>
                
                @if($blog->description)
                    <p class="text-xl text-gray-600 mb-6">{{ $blog->description }}</p>
                @endif
                
                <div class="flex items-center text-gray-500">
                    <span class="text-sm">
                        Published on {{ \Carbon\Carbon::parse($blog->created_at)->format('F j, Y') }}
                    </span>
                </div>
            </header>

            <div class="prose max-w-none">
                {!! $blog->content !!}
            </div>

            <footer class="mt-12 pt-6 border-t border-gray-200">
                <a href="{{ route('vh.frontend.blogsystem') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to All Articles
                </a>
            </footer>
        </article>
    </div>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} My Blog. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>