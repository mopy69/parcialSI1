<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-gray-900 mb-3">📱 Registrar Asistencia</h1>
                <p class="text-lg text-gray-600">Escanea el QR y selecciona tu clase</p>
            </div>

            <div x-data="qrScanner()" x-init="init()">
                
                <!-- PASO 1: Escanear QR -->
                <div x-show="step === 'scan'" class="bg-white rounded-2xl shadow-2xl p-8">
                    <div class="text-center">
                        <div x-show="!scanning" class="space-y-6">
                            <div class="w-48 h-48 mx-auto bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                            <button 
                                @click="startScanning()"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                                📷 Activar Cámara
                            </button>
                        </div>

                        <div x-show="scanning" class="space-y-4">
                            <div id="qr-reader" class="w-full rounded-xl overflow-hidden"></div>
                            <button 
                                @click="stopScanning()"
                                class="w-full bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition-all">
                                ❌ Detener Cámara
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Seleccionar Clase -->
                <div x-show="step === 'select'" class="bg-white rounded-2xl shadow-2xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Selecciona tu clase</h2>

                    <div class="space-y-4">
                        <template x-for="clase in clases" :key="clase.id">
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-500 transition-all">
                                <div class="mb-4">
                                    <h3 class="text-lg font-bold text-gray-900" x-text="clase.materia"></h3>
                                    <div class="grid grid-cols-3 gap-2 mt-2 text-sm text-gray-600">
                                        <div><span class="font-semibold">Grupo:</span> <span x-text="clase.grupo"></span></div>
                                        <div><span class="font-semibold">Aula:</span> <span x-text="clase.aula"></span></div>
                                        <div><span class="font-semibold">Horario:</span> <span x-text="clase.horario"></span></div>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <template x-for="opcion in clase.opciones" :key="opcion.type">
                                        <button
                                            @click="confirmAttendance(clase.id, clase.ids, opcion.type)"
                                            :class="opcion.type === 'entrada' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                                            class="flex-1 text-white px-4 py-3 rounded-lg font-semibold transition-all shadow-md">
                                            <span x-text="opcion.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button 
                        @click="reset()"
                        class="w-full mt-6 bg-gray-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-600 transition-all">
                        ← Volver a escanear
                    </button>
                </div>

                <!-- PASO 3: Resultado -->
                <div x-show="step === 'result'" class="bg-white rounded-2xl shadow-2xl p-8">
                    <div class="text-center">
                        <div x-show="result && result.success" class="space-y-6">
                            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100">
                                <svg class="h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Registrado Exitosamente!</h3>
                                <p class="text-lg text-gray-600" x-text="result?.message"></p>
                            </div>
                            
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 max-w-md mx-auto">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="text-right text-gray-700 font-medium">Tipo:</div>
                                    <div class="text-left">
                                        <span x-text="result?.type" class="px-3 py-1 bg-white rounded-full font-semibold text-blue-700"></span>
                                    </div>
                                    
                                    <div class="text-right text-gray-700 font-medium">Clase:</div>
                                    <div class="text-left font-bold text-gray-900" x-text="result?.class"></div>
                                    
                                    <div class="text-right text-gray-700 font-medium">Grupo:</div>
                                    <div class="text-left font-semibold" x-text="result?.group"></div>
                                    
                                    <div class="text-right text-gray-700 font-medium">Estado:</div>
                                    <div class="text-left">
                                        <span :class="{
                                            'bg-green-200 text-green-800': result?.state === 'a tiempo',
                                            'bg-yellow-200 text-yellow-800': result?.state === 'tarde',
                                            'bg-orange-200 text-orange-800': result?.state === 'temprano'
                                        }" class="px-3 py-1 rounded-full text-xs font-bold uppercase" x-text="result?.state"></span>
                                    </div>
                                    
                                    <div class="text-right text-gray-700 font-medium">Hora:</div>
                                    <div class="text-left font-bold text-indigo-600" x-text="result?.time"></div>
                                    
                                    <div class="text-right text-gray-700 font-medium">Aula:</div>
                                    <div class="text-left font-semibold" x-text="result?.classroom"></div>
                                </div>
                            </div>
                        </div>

                        <div x-show="result && !result.success" class="space-y-6">
                            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100">
                                <svg class="h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Error</h3>
                                <p class="text-lg text-red-600" x-text="result?.error"></p>
                                <p x-show="result?.mensaje" class="text-sm text-gray-600 mt-2" x-text="result?.mensaje"></p>
                            </div>
                        </div>

                        <button 
                            @click="reset()"
                            class="mt-8 w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all">
                            Registrar Otra Asistencia
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Historial de Asistencias por Materia -->
        @if(isset($currentTerm) && $currentTerm && count($historialPorMateria) > 0)
            <div class="mt-12">
                <div class="bg-white rounded-2xl shadow-2xl p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">
                        📊 Mi Historial de Asistencias
                    </h2>
                    <p class="text-center text-gray-600 mb-8">Gestión: {{ $currentTerm->name }}</p>

                    <div class="space-y-6">
                        @foreach($historialPorMateria as $datos)
                            <div class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-indigo-400 transition-all" x-data="{ open: false }">
                                <!-- Header de la Materia -->
                                <div @click="open = !open" class="bg-gradient-to-r from-indigo-50 to-blue-50 p-6 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $datos['materia'] }}</h3>
                                            <p class="text-sm text-gray-600">Grupo: {{ $datos['grupo'] }}</p>
                                        </div>
                                        
                                        <!-- Estadísticas Resumidas -->
                                        <div class="flex items-center gap-4 mr-4">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-gray-900">{{ $datos['estadisticas']['total'] }}</div>
                                                <div class="text-xs text-gray-600">Total</div>
                                            </div>
                                            @if($datos['estadisticas']['a_tiempo'] > 0)
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-green-600">{{ $datos['estadisticas']['a_tiempo'] }}</div>
                                                    <div class="text-xs text-gray-600">A Tiempo</div>
                                                </div>
                                            @endif
                                            @if($datos['estadisticas']['tarde'] > 0)
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-yellow-600">{{ $datos['estadisticas']['tarde'] }}</div>
                                                    <div class="text-xs text-gray-600">Tarde</div>
                                                </div>
                                            @endif
                                            @if($datos['estadisticas']['falta'] > 0)
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-red-600">{{ $datos['estadisticas']['falta'] }}</div>
                                                    <div class="text-xs text-gray-600">Faltas</div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <svg :class="{ 'rotate-180': open }" class="w-6 h-6 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Detalle de Asistencias -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="p-6 bg-white">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Registro</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario Clase</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($datos['asistencias'] as $asistencia)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 text-sm text-gray-900">
                                                            {{ \Carbon\Carbon::parse($asistencia->date)->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                                {{ $asistencia->type === 'entrada' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                                {{ ucfirst($asistencia->type) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm font-mono text-gray-900">
                                                            {{ $asistencia->time ?? '-' }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                                                @if($asistencia->state === 'a tiempo' || $asistencia->state === 'puntual') bg-green-100 text-green-800
                                                                @elseif($asistencia->state === 'tarde') bg-yellow-100 text-yellow-800
                                                                @elseif($asistencia->state === 'falta') bg-red-100 text-red-800
                                                                @elseif($asistencia->state === 'temprano') bg-orange-100 text-orange-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ $asistencia->state }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-600">
                                                            {{ \Carbon\Carbon::parse($asistencia->classAssignment->timeslot->start)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($asistencia->classAssignment->timeslot->end)->format('H:i') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(isset($currentTerm) && $currentTerm)
            <div class="mt-12">
                <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700">No hay registros de asistencia</h3>
                    <p class="text-gray-500 mt-2">Comienza registrando tu asistencia con el código QR</p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        function qrScanner() {
            return {
                step: 'scan',
                scanning: false,
                html5QrCode: null,
                clases: [],
                result: null,

                init() {
                    this.html5QrCode = new Html5Qrcode("qr-reader");
                },

                async startScanning() {
                    try {
                        this.scanning = true;
                        await this.html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decodedText) => this.processQrCode(decodedText)
                        );
                    } catch (err) {
                        alert("No se pudo acceder a la cámara");
                        this.scanning = false;
                    }
                },

                stopScanning() {
                    this.html5QrCode.stop().then(() => {
                        this.scanning = false;
                    });
                },

                processQrCode(qrData) {
                    this.stopScanning();
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => this.sendQrData(qrData, position.coords.latitude, position.coords.longitude),
                            () => this.sendQrData(qrData, null, null)
                        );
                    } else {
                        this.sendQrData(qrData, null, null);
                    }
                },

                sendQrData(qrData, latitude, longitude) {
                    fetch('{{ route("attendance.qr.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ qr_data: qrData, latitude, longitude })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.clases) {
                            this.clases = data.clases;
                            this.step = 'select';
                        } else {
                            this.result = data;
                            this.step = 'result';
                        }
                    })
                    .catch(() => {
                        this.result = { success: false, error: 'Error de conexión' };
                        this.step = 'result';
                    });
                },

                confirmAttendance(classId, classIds, type) {
                    fetch('{{ route("attendance.qr.confirm") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            class_assignment_id: classId,
                            class_assignment_ids: classIds,
                            type: type 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.result = data;
                        this.step = 'result';
                    })
                    .catch(() => {
                        this.result = { success: false, error: 'Error al confirmar asistencia' };
                        this.step = 'result';
                    });
                },

                reset() {
                    this.step = 'scan';
                    this.scanning = false;
                    this.clases = [];
                    this.result = null;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
