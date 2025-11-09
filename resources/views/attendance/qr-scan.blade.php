<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Escanear Asistencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div x-data="qrScanner()" x-init="init()">
                        
                        <!-- Estado: Esperando escaneo -->
                        <div x-show="!scanning && !result" class="text-center py-12">
                            <svg class="mx-auto h-24 w-24 text-indigo-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Escanea el código QR de tu clase</h3>
                            <p class="text-sm text-gray-600 mb-6">Coloca el QR frente a la cámara para registrar tu asistencia</p>
                            
                            <button 
                                @click="startScanning()"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Activar Cámara
                            </button>
                        </div>

                        <!-- Video Preview -->
                        <div x-show="scanning" x-cloak class="space-y-4">
                            <div class="relative bg-black rounded-lg overflow-hidden">
                                <video id="qr-video" class="w-full h-96 object-cover"></video>
                                <div class="absolute inset-0 border-4 border-indigo-500 opacity-50 pointer-events-none"></div>
                            </div>
                            
                            <div class="flex justify-center">
                                <button 
                                    @click="stopScanning()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                    Detener Cámara
                                </button>
                            </div>
                        </div>

                        <!-- Resultado: Éxito -->
                        <div x-show="result && result.success" x-cloak class="text-center py-12">
                            <div class="mb-6">
                                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100">
                                    <svg class="h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Asistencia Registrada!</h3>
                            <p class="text-lg text-gray-600 mb-1" x-text="result?.message"></p>
                            
                            <div class="mt-6 bg-gray-50 rounded-lg p-4 max-w-sm mx-auto">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="text-right text-gray-600">Clase:</div>
                                    <div class="text-left font-semibold" x-text="result?.class"></div>
                                    
                                    <div class="text-right text-gray-600">Estado:</div>
                                    <div class="text-left">
                                        <span :class="{
                                            'bg-green-100 text-green-800': result?.state === 'a tiempo',
                                            'bg-yellow-100 text-yellow-800': result?.state === 'tarde'
                                        }" class="px-2 py-1 rounded-full text-xs font-medium" x-text="result?.state"></span>
                                    </div>
                                    
                                    <div class="text-right text-gray-600">Hora:</div>
                                    <div class="text-left font-semibold" x-text="result?.time"></div>
                                </div>
                            </div>

                            <button 
                                @click="reset()"
                                class="mt-8 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                Escanear Otro QR
                            </button>
                        </div>

                        <!-- Resultado: Error -->
                        <div x-show="result && !result.success" x-cloak class="text-center py-12">
                            <div class="mb-6">
                                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100">
                                    <svg class="h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Error al Registrar</h3>
                            <p class="text-lg text-red-600" x-text="result?.error"></p>

                            <button 
                                @click="reset()"
                                class="mt-8 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                Intentar Nuevamente
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        function qrScanner() {
            return {
                scanning: false,
                result: null,
                html5QrCode: null,

                init() {
                    this.html5QrCode = new Html5Qrcode("qr-video");
                },

                startScanning() {
                    this.scanning = true;
                    this.result = null;

                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        (decodedText) => {
                            this.processQrCode(decodedText);
                        },
                        (errorMessage) => {
                            // Error de escaneo, ignorar
                        }
                    ).catch(err => {
                        console.error("Error al iniciar cámara:", err);
                        alert("No se pudo acceder a la cámara. Verifica los permisos.");
                        this.scanning = false;
                    });
                },

                stopScanning() {
                    this.html5QrCode.stop().then(() => {
                        this.scanning = false;
                    }).catch(err => {
                        console.error("Error al detener:", err);
                    });
                },

                processQrCode(qrData) {
                    this.stopScanning();

                    // Obtener ubicación
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => this.sendAttendance(qrData, position.coords.latitude, position.coords.longitude),
                            () => this.sendAttendance(qrData, null, null)
                        );
                    } else {
                        this.sendAttendance(qrData, null, null);
                    }
                },

                sendAttendance(qrData, latitude, longitude) {
                    fetch('{{ route("attendance.qr.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            qr_data: qrData,
                            latitude: latitude,
                            longitude: longitude
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.result = data;
                    })
                    .catch(error => {
                        this.result = {
                            success: false,
                            error: 'Error de conexión'
                        };
                    });
                },

                reset() {
                    this.result = null;
                    this.scanning = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
