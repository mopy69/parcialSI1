<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-3">
                    🔐 Generar QR de Asistencia
                </h1>
                <p class="text-lg text-gray-600">
                    QR Dinámico Global - Se regenera cada 30 segundos
                </p>
                @if ($currentTerm)
                    <span class="inline-block mt-3 px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                        Gestión: {{ $currentTerm->name }}
                    </span>
                @endif
            </div>

            <!-- QR Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div x-data="qrGlobalSession()" class="text-center">
                    
                    <!-- Estado Inicial -->
                    <div x-show="!sessionActive" class="space-y-6">
                        <div class="w-48 h-48 mx-auto bg-gray-100 rounded-xl flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-4.01M12 12v4m6-8h2m-6 0h-2V5m0 11v3m0 0h.01M5 7v.01M5 17v.01M5 12v.01"/>
                            </svg>
                        </div>
                        <button 
                            @click="startSession()"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
                            🚀 Activar QR Global
                        </button>
                        <p class="text-sm text-gray-500 mt-3">
                            Permite que todos los docentes registren su asistencia escaneando este QR
                        </p>
                    </div>

                    <!-- QR Activo -->
                    <div x-show="sessionActive" class="space-y-6">
                        
                        <!-- QR Code -->
                        <div class="relative">
                            <div id="qr-container" class="w-80 h-80 mx-auto bg-white p-4 rounded-2xl shadow-inner border-4 border-indigo-200"></div>
                            
                            <!-- Indicador de Actualización -->
                            <div x-show="isRefreshing" class="absolute inset-0 bg-white bg-opacity-90 rounded-2xl flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600">Regenerando QR...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contador -->
                        <div class="bg-gradient-to-r from-indigo-100 to-purple-100 rounded-xl p-6">
                            <div class="flex items-center justify-center space-x-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-700 font-medium">Tiempo restante:</span>
                                <span x-text="countdown" class="text-3xl font-bold text-indigo-600"></span>
                                <span class="text-gray-600">segundos</span>
                            </div>
                            
                            <!-- Barra de progreso -->
                            <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 h-2 rounded-full transition-all duration-1000"
                                    :style="'width: ' + ((countdown / 30) * 100) + '%'">
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-green-800">QR Activo</p>
                                    <p class="text-xs text-green-700 mt-1">
                                        Los docentes pueden escanear este QR para registrar su asistencia.
                                        El sistema detectará automáticamente su clase actual.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Cerrar -->
                        <button 
                            @click="closeSession()"
                            class="w-full bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition-all shadow-md">
                            ❌ Desactivar QR
                        </button>
                    </div>

                </div>
            </div>

            <!-- Instrucciones -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="font-bold text-blue-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    ¿Cómo funciona?
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <span>Activa el QR global - Este QR es válido para TODOS los docentes</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>El QR se regenera automáticamente cada 30 segundos por seguridad</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">3.</span>
                        <span>Los docentes escanean el QR desde "Registrar Asistencia"</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">4.</span>
                        <span>El sistema detecta automáticamente la clase que tiene el docente en ese momento</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">5.</span>
                        <span>Cuando un docente escanea, el QR se regenera inmediatamente</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        function qrGlobalSession() {
            return {
                sessionActive: false,
                countdown: 30,
                countdownInterval: null,
                refreshInterval: null,
                qrCode: null,
                isRefreshing: false,

                async startSession() {
                    try {
                        // Obtener geolocalización (opcional)
                        let latitude = null;
                        let longitude = null;

                        if (navigator.geolocation) {
                            try {
                                const position = await new Promise((resolve, reject) => {
                                    navigator.geolocation.getCurrentPosition(
                                        resolve, 
                                        reject,
                                        { timeout: 5000 }
                                    );
                                });
                                latitude = position.coords.latitude;
                                longitude = position.coords.longitude;
                            } catch (geoError) {
                                // Continuar sin geolocalización
                            }
                        }

                        // Generar QR
                        const response = await fetch('{{ route('attendance.qr.generate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ latitude, longitude })
                        });

                        const data = await response.json();
                        console.log('Respuesta del servidor:', data);

                        if (data.success) {
                            this.sessionActive = true;
                            this.displayQr(data.qr_data);
                            this.startCountdown();
                            this.startAutoRefresh();
                        } else {
                            alert('Error: ' + (data.error || 'No se pudo generar el QR'));
                        }
                    } catch (error) {
                        alert('Error al generar QR');
                    }
                },

                displayQr(qrData) {
                    const container = document.getElementById('qr-container');
                    if (!container) return;
                    
                    container.innerHTML = '';

                    try {
                        this.qrCode = new QRCode(container, {
                            text: qrData,
                            width: 280,
                            height: 280,
                            colorDark: "#4f46e5",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        alert('Error al mostrar el QR');
                    }
                },

                startCountdown() {
                    this.countdown = 30;
                    
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                    }

                    this.countdownInterval = setInterval(() => {
                        this.countdown--;
                        
                        // Cuando llega a 0, regenerar inmediatamente
                        if (this.countdown <= 0) {
                            this.refreshToken();
                        }
                    }, 1000);
                },

                startAutoRefresh() {
                    // Ya no necesitamos el intervalo automático
                    // El refresh se dispara cuando el countdown llega a 0
                },

                async refreshToken() {
                    try {
                        this.isRefreshing = true;

                        const response = await fetch('{{ route('attendance.qr.refresh') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.displayQr(data.qr_data);
                            this.countdown = 30; // Reiniciar contador
                        }

                        setTimeout(() => {
                            this.isRefreshing = false;
                        }, 500);
                    } catch (error) {
                        this.isRefreshing = false;
                        console.error('Error al refrescar token:', error);
                    }
                },

                async closeSession() {
                    try {
                        await fetch('{{ route('attendance.qr.close') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        this.sessionActive = false;
                        
                        if (this.countdownInterval) {
                            clearInterval(this.countdownInterval);
                        }
                        
                        if (this.refreshInterval) {
                            clearInterval(this.refreshInterval);
                        }
                    } catch (error) {
                        // Error al cerrar
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
