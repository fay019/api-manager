@extends('layouts.app')

@section('title', $title ?? 'Documentation')

@section('styles')
    <style>
        body {
            background: #f5f5f5;
            line-height: 1.6;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .navbar a:hover {
            color: #764ba2;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .doc-content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        /* Markdown Styling */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-weight: 600;
            line-height: 1.25;
        }

        h1 {
            font-size: 2em;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
        }

        h2 {
            font-size: 1.5em;
            color: #764ba2;
            margin-top: 32px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        h3 {
            font-size: 1.25em;
            color: #333;
        }

        p {
            margin-bottom: 16px;
        }

        ul, ol {
            margin-bottom: 16px;
            margin-left: 24px;
        }

        li {
            margin-bottom: 8px;
        }

        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9em;
            color: #c7254e;
        }

        pre {
            background: #282c34;
            color: #abb2bf;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 16px 0;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9em;
            line-height: 1.5;
        }

        pre code {
            background: none;
            padding: 0;
            color: inherit;
        }

        blockquote {
            border-left: 4px solid #667eea;
            padding-left: 16px;
            margin-left: 0;
            margin: 16px 0;
            color: #666;
            font-style: italic;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 16px 0;
        }

        table th {
            background: #f0f0f0;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        table tr:hover {
            background: #f9f9f9;
        }

        a {
            color: #667eea;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        hr {
            border: none;
            border-top: 2px solid #eee;
            margin: 32px 0;
        }

        .toc {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .toc h4 {
            margin-top: 0;
            color: #667eea;
        }

        .toc ul {
            list-style-type: none;
            margin-left: 0;
        }

        .toc ul li {
            margin-bottom: 4px;
        }

        .toc a {
            color: #667eea;
        }

        strong {
            font-weight: 600;
            color: #333;
        }

        em {
            font-style: italic;
            color: #666;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
    <div class="navbar">
        <a href="/">← API Manager</a>
        <a href="{{ route('docs.index') }}">← All Docs</a>
    </div>

    <div class="container">
        <div class="doc-content">
            <a href="{{ route('docs.index') }}" class="back-link">← Back to Documentation</a>

            <div class="markdown-content">
                {!! $content !!}
            </div>

            <hr>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 0.9em;">
                <p>
                    <strong>Last Updated:</strong> 2026-01-18<br>
                    <strong>Documentation for:</strong> API Manager v1.0<br>
                    <a href="{{ route('docs.index') }}" style="color: #667eea;">← Back to all docs</a>
                </p>
            </div>
        </div>
    </div>
@endsection
