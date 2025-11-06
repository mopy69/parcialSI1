<div class="fixed top-24 right-6 w-full max-w-sm z-50 space-y-3">
    
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-x-10"
             class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-md" 
             role="alert">
            
            <span class="block sm:inline pr-6">{{ session('success') }}</span>
            
            <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 text-green-700" aria-label="Cerrar">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 8.586L14.293 4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586z"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-x-10"
             class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-md" 
             role="alert">
            
            <span class="block sm:inline pr-6">{{ session('error') }}</span>
            
            <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 text-red-700" aria-label="Cerrar">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 8.586L14.293 4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586z"/></svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-x-10"
             class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-md" 
             role="alert">
            
            <strong class="font-bold">¡Han ocurrido errores!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            
            <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3 text-red-700" aria-label="Cerrar">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 8.586L14.293 4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586z"/></svg>
            </button>
        </div>
    @endif
</div>