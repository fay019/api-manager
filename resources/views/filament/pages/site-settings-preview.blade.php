<x-filament::section
    heading="Favicons Générés"
    collapsible
>
    @php
        $files = File::exists(public_path('favicon')) ? File::files(public_path('favicon')) : [];
        $faviconFiles = collect($files)->filter(fn($file) => in_array($file->getExtension(), ['png', 'webmanifest']));
    @endphp

    @if($faviconFiles->isEmpty())
        <div class="p-4 text-center text-gray-500">
            Aucun favicon n'a été généré pour le moment.
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($faviconFiles as $file)
                <div class="p-3 border rounded-lg bg-gray-50 flex flex-col items-center gap-2">
                    @if($file->getExtension() === 'png')
                        <div class="flex items-center justify-center bg-white border rounded shadow-sm overflow-hidden" style="width: 4rem; height: 4rem;">
                            <img src="/favicon/{{ $file->getFilename() }}?v={{ $file->getMTime() }}" alt="{{ $file->getFilename() }}" class="object-contain" style="max-width: 100%; max-height: 100%;">
                        </div>
                    @else
                        <div class="flex items-center justify-center bg-white border rounded shadow-sm" style="width: 4rem; height: 4rem;">
                            <x-heroicon-o-document-text class="fi-icon fi-size-lg text-gray-400" style="width: 2rem; height: 2rem;" />
                        </div>
                    @endif
                    <span class="text-[10px] font-mono text-gray-600 truncate w-full text-center" title="{{ $file->getFilename() }}">
                        {{ $file->getFilename() }}
                    </span>
                    <span class="text-[9px] text-gray-400">
                        {{ round($file->getSize() / 1024, 1) }} KB
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</x-filament::section>
