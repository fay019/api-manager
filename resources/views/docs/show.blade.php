@extends('layouts.app')

@section('title', $title ?? __('app.docs.page_title'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <a href="{{ route('docs.index') }}" class="inline-flex items-center gap-2 mb-8 px-4 py-2 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 rounded-lg font-semibold transition-all hover:bg-indigo-100 dark:hover:bg-indigo-950/50 hover:-translate-x-1">
        {{ __('app.docs.back_to_docs') }}
    </a>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 sm:p-12 shadow-sm">
        <div class="prose prose-sm dark:prose-invert max-w-none">
            <div class="markdown-content space-y-6">
                {!! $content !!}
            </div>
        </div>

        <hr class="my-12 border-gray-200 dark:border-gray-700">

        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400">
            <p class="mb-3">
                <strong class="text-gray-900 dark:text-white">{{ __('app.docs.last_updated') }}</strong> 2026-01-18
            </p>
            <p class="mb-4">
                <strong class="text-gray-900 dark:text-white">{{ __('app.docs.documentation_for') }}</strong> API Manager v1.0
            </p>
            <a href="{{ route('docs.index') }}" class="inline-flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                {{ __('app.docs.back_to_docs') }}
            </a>
        </div>
    </div>
</div>

<style>
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        @apply scroll-mt-8;
    }

    .markdown-content h1 {
        @apply text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mt-8 mb-6 pb-4 border-b-2 border-indigo-300 dark:border-indigo-700;
    }

    .markdown-content h1::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 4px;
        background-color: #4f46e5;
        border-radius: 50%;
        margin-right: 8px;
    }

    .markdown-content h2 {
        @apply text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-8 mb-4 pb-3 border-b-2 border-indigo-200 dark:border-indigo-800;
    }

    .markdown-content h2::before {
        content: '';
        display: inline-block;
        width: 3px;
        height: 3px;
        background-color: #4f46e5;
        border-radius: 50%;
        margin-right: 8px;
    }

    .markdown-content h3 {
        @apply text-xl font-bold text-gray-900 dark:text-white mt-6 mb-3 text-indigo-900 dark:text-indigo-100;
    }

    .markdown-content h4, .markdown-content h5, .markdown-content h6 {
        @apply text-lg font-semibold text-gray-900 dark:text-white mt-4 mb-2;
    }

    .markdown-content p {
        @apply text-gray-700 dark:text-gray-300 leading-relaxed mb-4;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .markdown-content ul {
        @apply text-gray-700 dark:text-gray-300 mb-6 ml-6 space-y-2;
        list-style-type: disc;
    }

    .markdown-content ol {
        @apply text-gray-700 dark:text-gray-300 mb-6 ml-6 space-y-2;
        list-style-type: decimal;
    }

    .markdown-content ul li {
        @apply mb-0 leading-relaxed;
    }

    .markdown-content ol li {
        @apply mb-0 leading-relaxed;
    }

    .markdown-content ul li::marker {
        @apply text-indigo-600 dark:text-indigo-400 font-bold;
    }

    .markdown-content ol li::marker {
        @apply text-indigo-600 dark:text-indigo-400 font-bold;
    }

    .markdown-content code {
        @apply bg-gray-100 dark:bg-gray-900 text-rose-600 dark:text-rose-400 px-1.5 py-0.5 rounded font-mono text-sm;
    }

    .markdown-content pre {
        @apply p-6 rounded-lg my-6 border shadow-lg;
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #1f2937;
    }

    .dark .markdown-content pre {
        background: linear-gradient(135deg, #111827 0%, #0f172a 100%);
        border-color: #374151;
        color: #f3f4f6;
    }

    .markdown-content pre code {
        @apply bg-none px-0 py-0 rounded-none font-mono;
        display: block;
        word-break: break-word;
        overflow-wrap: break-word;
        white-space: pre-wrap;
        line-height: 1.6;
        letter-spacing: 0.01em;
        color: inherit;
    }

    .markdown-content blockquote {
        @apply border-l-4 border-indigo-500 pl-6 py-4 italic text-gray-700 dark:text-gray-300 bg-indigo-50 dark:bg-indigo-950/30 rounded-r-lg my-6 shadow-sm;
        background: linear-gradient(90deg, #eef2ff 0%, transparent 100%);
    }

    .markdown-content blockquote p {
        @apply my-0;
    }

    .markdown-content table {
        @apply w-full border-collapse my-8 rounded-lg overflow-hidden shadow-md;
        border: 1px solid #d1d5db;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .dark .markdown-content table {
        border-color: #4b5563;
    }

    .markdown-content table th {
        @apply py-4 text-left font-bold text-gray-900 dark:text-white text-sm bg-indigo-50 dark:bg-indigo-950/50;
        padding-left: 1rem;
        padding-right: 1rem;
        border-bottom: 2px solid #818cf8;
        border-right: 1.5px solid #9ca3af;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .dark .markdown-content table th {
        border-right-color: #6b7280;
    }

    .markdown-content table th:last-child {
        border-right: none;
        padding-right: 1rem;
    }

    .markdown-content table td {
        @apply py-4 text-gray-700 dark:text-gray-300 text-sm border-b;
        padding-left: 1rem;
        padding-right: 1rem;
        border-right: 1.5px solid #9ca3af;
        border-bottom-color: #d1d5db;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .dark .markdown-content table td {
        border-right-color: #6b7280;
        border-bottom-color: #4b5563;
    }

    .markdown-content table td:last-child {
        border-right: none;
        padding-right: 1rem;
    }

    .markdown-content table tr:last-child td {
        @apply border-b-0;
    }

    .markdown-content table tbody tr:nth-child(odd) {
        @apply bg-white dark:bg-gray-800;
    }

    .markdown-content table tbody tr:nth-child(even) {
        @apply bg-gray-50 dark:bg-gray-800/50;
    }

    .markdown-content table tbody tr:hover {
        @apply bg-indigo-50 dark:bg-indigo-950/30;
    }

    .markdown-content table strong {
        @apply font-bold text-indigo-700 dark:text-indigo-300;
    }

    .markdown-content table em {
        @apply italic text-gray-600 dark:text-gray-400;
    }

    .markdown-content a {
        @apply text-indigo-600 dark:text-indigo-400 font-medium hover:underline;
        word-break: break-word;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .markdown-content img {
        @apply max-w-full h-auto rounded-lg my-6 shadow-md;
    }

    .markdown-content strong {
        @apply font-bold text-gray-900 dark:text-white;
    }

    .markdown-content em {
        @apply italic text-gray-700 dark:text-gray-300;
    }

    .markdown-content hr {
        @apply my-8 border-t-2 border-gray-200 dark:border-gray-700;
        border: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    }

    .markdown-content hr::after {
        content: '';
        display: block;
        width: 2px;
        height: 2px;
        background-color: #6366f1;
        border-radius: 50%;
        position: absolute;
        left: 50%;
        transform: translateX(-50%) translateY(-7px);
    }

    .markdown-content input[type="checkbox"] {
        @apply w-5 h-5 mr-2 accent-indigo-600 dark:accent-indigo-400 cursor-pointer;
    }

    /* Prevent content overflow */
    .markdown-content {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .markdown-content ul,
    .markdown-content ol {
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .markdown-content li {
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
</style>
@endsection
