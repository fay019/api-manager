@extends('layouts.app')

@section('title', $title ?? __('app.docs.page_title'))

@section('styles')
    <style>
        body {
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .doc-content {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            backdrop-blur: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .dark .doc-content {
            background: rgba(24, 24, 27, 0.8);
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }

        /* Markdown Styling */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-weight: 700;
            line-height: 1.25;
            color: #18181b;
        }

        .dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6 {
            color: #f4f4f5;
        }

        h1 {
            font-size: 2.25em;
            color: #4f46e5;
            border-bottom: 2px solid rgba(79, 70, 229, 0.1);
            padding-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .dark h1 {
            color: #818cf8;
            border-bottom-color: rgba(129, 140, 248, 0.1);
        }

        h2 {
            font-size: 1.75em;
            color: #7c3aed;
            margin-top: 40px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding-bottom: 8px;
        }

        .dark h2 {
            color: #a78bfa;
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        h3 {
            font-size: 1.5em;
        }

        p {
            margin-bottom: 16px;
            color: #3f3f46;
        }

        .dark p {
            color: #a1a1aa;
        }

        ul, ol {
            margin-bottom: 16px;
            margin-left: 24px;
            color: #3f3f46;
        }

        .dark ul, .dark ol {
            color: #a1a1aa;
        }

        li {
            margin-bottom: 8px;
        }

        code {
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 6px;
            border-radius: 6px;
            font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace;
            font-size: 0.9em;
            color: #be185d;
        }

        .dark code {
            background: rgba(255, 255, 255, 0.1);
            color: #f472b6;
        }

        pre {
            background: #18181b;
            color: #e4e4e7;
            padding: 20px;
            border-radius: 12px;
            overflow-x: auto;
            margin: 24px 0;
            font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace;
            font-size: 0.875em;
            line-height: 1.7;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        pre code {
            background: none;
            padding: 0;
            color: inherit;
            border-radius: 0;
        }

        blockquote {
            border-left: 4px solid #4f46e5;
            padding: 8px 20px;
            margin: 24px 0;
            background: rgba(79, 70, 229, 0.03);
            color: #4b5563;
            font-style: italic;
            border-radius: 0 8px 8px 0;
        }

        .dark blockquote {
            background: rgba(129, 140, 248, 0.03);
            color: #9ca3af;
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin: 24px 0;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        .dark table {
            border-color: rgba(255, 255, 255, 0.05);
        }

        table th {
            background: rgba(0, 0, 0, 0.02);
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #18181b;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark table th {
            background: rgba(255, 255, 255, 0.02);
            color: #f4f4f5;
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #3f3f46;
        }

        .dark table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
            color: #a1a1aa;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background: rgba(0, 0, 0, 0.01);
        }

        .dark table tr:hover {
            background: rgba(255, 255, 255, 0.01);
        }

        a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .dark a {
            color: #818cf8;
        }

        a:hover {
            text-decoration: underline;
        }

        hr {
            border: none;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin: 48px 0;
        }

        .dark hr {
            border-top-color: rgba(255, 255, 255, 0.05);
        }

        strong {
            font-weight: 700;
            color: #18181b;
        }

        .dark strong {
            color: #f4f4f5;
        }

        em {
            font-style: italic;
            color: #4b5563;
        }

        .dark em {
            color: #9ca3af;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            margin-bottom: 24px;
            color: #4f46e5;
            font-weight: 600;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(79, 70, 229, 0.05);
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .dark .back-link {
            color: #818cf8;
            background: rgba(129, 140, 248, 0.1);
        }

        .back-link:hover {
            background: rgba(79, 70, 229, 0.1);
            text-decoration: none;
            transform: translateX(-4px);
        }

        .dark .back-link:hover {
            background: rgba(129, 140, 248, 0.15);
        }

        .doc-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            color: #999;
            font-size: 0.9em;
        }

        .markdown-content img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 16px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .markdown-content > *:first-child {
            margin-top: 0;
        }

        .markdown-content ul ul,
        .markdown-content ol ol,
        .markdown-content ul ol,
        .markdown-content ol ul {
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .markdown-content input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
            width: 18px;
            height: 18px;
            vertical-align: middle;
            accent-color: #667eea;
        }

        .markdown-content li input[type="checkbox"] {
            margin-left: -24px;
            margin-right: 12px;
        }

        .markdown-content input[type="checkbox"]:disabled {
            opacity: 1;
            cursor: default;
        }

        .markdown-content input[type="checkbox"]:disabled:checked {
            accent-color: #667eea;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="doc-content">
            <a href="{{ route('docs.index') }}" class="back-link">{{ __('app.docs.back_to_docs') }}</a>

            <div class="markdown-content">
                {!! $content !!}
            </div>

            <hr>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); color: #71717a; font-size: 0.9em;">
                <p>
                    <strong class="dark:text-white">{{ __('app.docs.last_updated') }}</strong> 2026-01-18<br>
                    <strong class="dark:text-white">{{ __('app.docs.documentation_for') }}</strong> API Manager v1.0<br>
                    <a href="{{ route('docs.index') }}" style="color: #4f46e5;" class="dark:text-indigo-400">{{ __('app.docs.back_to_docs') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
