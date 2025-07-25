<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('vh.frontend.blogsystem') }}" class="text-xl font-bold text-gray-900">My Blog</a>
                    </div>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Discover Our Blogs</h1>
            <p class="mt-3 text-xl text-gray-500">Read the latest articles and insights</p>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-12 p-6 bg-gray-100 rounded-lg">
            <div class="md:col-span-5">
                <input type="text" name="search" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search blogs..." value="{{ request('search') }}">
            </div>
            <div class="md:col-span-2">
                <select name="category" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <select name="tag" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Tags</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag')==$tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('vh.frontend.blogsystem') }}" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-150 ease-in-out flex items-center justify-center">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>

        {{-- Blog List --}}
        @if($blogs->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($blogs as $blog)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $blog->category->name ?? 'Uncategorized' }}</span>
                                @foreach($blog->tags as $tag)
                                    <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="{{ route('vh.frontend.blogsystem.detail', $blog->slug) }}" class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                    {{ $blog->name }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit($blog->content, 120) }}</p>
                            <div class="text-right text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($blog->created_at)->format('F j, Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-12 flex justify-center">
                {{ $blogs->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-white border border-gray-200 rounded-lg p-6 max-w-md mx-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No blogs found</h3>
                    <p class="text-gray-500">Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            </div>
        @endif
    </div>

    <footer class="bg-white mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">&copy; {{ date('Y') }} My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 