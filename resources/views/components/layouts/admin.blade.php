<x-app-layout>
    <div class="py-9 admin-page" style="min-height:60vh;">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-1">
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 admin-content">

                    <div class="flex flex-col lg:flex-row mb-4">
                        
                        <div class="
                            hidden      sm:block    sm:w-full   lg:w-64     flex-shrink-0 
                            sm:mb-6     lg:mb-0     ">
                            <x-admin.sidebar />
                        </div>
                        
                        <div class="
                            flex-1 
                            mt-6        sm:mt-0     lg:ml-8     ">
                            {{ $slot }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>