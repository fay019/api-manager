@extends('layouts.app')

@section('title', $title ?? __('app.docs.page_title'))

@section('styles')
    <style>
        .docs-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .doc-content {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .dark .doc-content {
            box-shadow: none;
        }

        /* Markdown Styling */
        .doc-content h1, .doc-content h2, .doc-content h3, .doc-content h4, .doc-content h5, .doc-content h6 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-weight: 700;
            line-height: 1.25;
            color: var(--text-main);
        }

        .doc-content h1 {
            font-size: 2.25em;
            color: var(--primary);
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
            letter-spacing: -0.025em;
        }

        .doc-content h2 {
            font-size: 1.75em;
            color: var(--primary);
            margin-top: 40px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
        }

        .doc-content h3 {
            font-size: 1.5em;
        }

        .doc-content p {
            margin-bottom: 16px;
            color: var(--text-muted);
        }

        .doc-content ul, .doc-content ol {
            margin-bottom: 16px;
            margin-left: 24px;
            color: var(--text-muted);
        }

        .doc-content li {
            margin-bottom: 8px;
        }

        .doc-content code {
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 6px;
            border-radius: 6px;
            font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', monospace;
            font-size: 0.9em;
            color: #be185d;
        }

        .dark .doc-content code {
            background: rgba(255, 255, 255, 0.1);
            color: #fda4af;
        }

        .doc-content pre {
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

        .doc-content pre code {
            background: none;
            padding: 0;
            color: inherit;
            border-radius: 0;
        }

        .doc-content blockquote {
            border-left: 4px solid var(--primary);
            padding: 8px 20px;
            margin: 24px 0;
            background: rgba(79, 70, 229, 0.03);
            color: var(--text-muted);
            font-style: italic;
            border-radius: 0 8px 8px 0;
        }

        .doc-content table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin: 24px 0;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .doc-content table th {
            background: rgba(0, 0, 0, 0.02);
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
        }

        .doc-content table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-muted);
        }

        .doc-content table tr:last-child td {
            border-bottom: none;
        }

        .doc-content table tr:hover {
            background: rgba(0, 0, 0, 0.01);
        }

        .dark .doc-content table tr:hover {
            background: rgba(255, 255, 255, 0.01);
        }

        .doc-content a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .doc-content a:hover {
            text-decoration: underline;
        }

        hr {
            border: none;
            border-top: 1px solid var(--border);
            margin: 48px 0;
        }

        strong {
            font-weight: 700;
            color: var(--text-main);
        }

        em {
            font-style: italic;
            color: var(--text-muted);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            margin-bottom: 24px;
            color: var(--primary);
            font-weight: 600;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(79, 70, 229, 0.05);
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            background: rgba(79, 70, 229, 0.1);
            text-decoration: none;
            transform: translateX(-4px);
        }

        .doc-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            color: var(--text-muted);
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
            accent-color: var(--primary);
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
            accent-color: var(--primary);
        }
    </style>
@endsection

@section('content')
<div class="docs-container">
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
