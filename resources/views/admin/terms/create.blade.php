<x-layouts.admin>
    {{-- Contenedor principal del formulario --}}
    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Crear Nuevo Término Académico</h1>
        </div>

        {{-- Contenedor para limitar el ancho del formulario --}}
        <div class="max-w-xl">
            <form action="{{ route('admin.terms.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    {{-- Campo Nombre --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="name" :value="__('Nombre')" />
                        <x-inicio.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required placeholder="Ej: 2024-1" />
                        <x-inicio.input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Campo Fecha de Inicio --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="start_date" :value="__('Fecha de Inicio')" />
                        <x-inicio.text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                        <x-inicio.input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>

                    {{-- Campo Fecha de Fin --}}
                    <div class="mb-4">
                        <x-inicio.input-label for="end_date" :value="__('Fecha de Fin')" />
                        <x-inicio.text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" required />
                        <x-inicio.input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>

                    {{-- Botones de Acción --}}
                    <div class="flex items-center justify-start gap-4">
                        <x-inicio.primary-button type="submit">
                            Crear Término
                        </x-inicio.primary-button>

                        <x-inicio.secondary-button href="{{ route('admin.terms.index') }}">
                            Cancelar
                        </x-inicio.secondary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>