<div class="space-y-4">
    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <p class="text-blue-800 dark:text-blue-200 font-semibold text-sm mb-2">Model</p>
        <code class="text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 px-2 py-1 rounded font-mono text-sm block">
            {{ $result['model'] ?? 'N/A' }}
        </code>
    </div>

    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
        <p class="text-purple-800 dark:text-purple-200 font-semibold text-sm mb-2">Prompt</p>
        <p class="text-purple-700 dark:text-purple-300 font-mono text-sm mb-4 italic">Bonjour</p>
        <p class="text-purple-800 dark:text-purple-200 font-semibold text-sm mb-2">Response</p>
        <p class="text-purple-700 dark:text-purple-300 text-sm whitespace-pre-wrap break-words">
            {{ $result['response'] ?? 'N/A' }}
        </p>
    </div>

    <div class="grid grid-cols-3 gap-3">
        <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-900/10 rounded-lg border border-orange-200 dark:border-orange-800">
            <p class="text-orange-600 dark:text-orange-400 text-xs font-semibold mb-1">Duration</p>
            <p class="text-2xl font-bold text-orange-900 dark:text-orange-200">
                {{ $result['duration_ms'] ?? 0 }}<span class="text-sm">ms</span>
            </p>
        </div>
        <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10 rounded-lg border border-green-200 dark:border-green-800">
            <p class="text-green-600 dark:text-green-400 text-xs font-semibold mb-1">Prompt Tokens</p>
            <p class="text-2xl font-bold text-green-900 dark:text-green-200">
                {{ $result['prompt_eval_count'] ?? 0 }}
            </p>
        </div>
        <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-blue-600 dark:text-blue-400 text-xs font-semibold mb-1">Response Tokens</p>
            <p class="text-2xl font-bold text-blue-900 dark:text-blue-200">
                {{ $result['eval_count'] ?? 0 }}
            </p>
        </div>
    </div>
</div>
