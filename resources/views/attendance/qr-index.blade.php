<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Asistencia Docente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($currentTerm)
                <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900">Gestión Actual:</span>
                        <span class="text-gray-700">{{ $currentTerm->name }}</span>
                        <span class="text-sm text-gray-500">({{ $currentTerm->start_date }} - {{ $currentTerm->end_date }})</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Mis Clases de Hoy</h3>
                        <p class="text-sm text-gray-600">Escanea el código QR para registrar tu asistencia como docente al inicio de cada clase</p>
                    </div>

                    @if($clasesHoy->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes clases programadas hoy</h3>
                            <p class="mt-1 text-sm text-gray-500">Vuelve cuando tengas clases asignadas</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($clasesHoy as $clase)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" 
                                     x-data="qrSession({{ $clase->id }})">
                                    
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-gray-900">{{ $clase->courseOffering->subject->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $clase->courseOffering->group->name }}</p>
                                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ \Carbon\Carbon::parse($clase->timeslot->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->timeslot->end)->format('H:i') }}</span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span>Aula {{ $clase->classroom->nro }}</span>
                                        </div>
                                    </div>

                                    <!-- Botón para activar QR -->
                                    <button 
                                        x-show="!sessionActive"
                                        @click="startSession()"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                        Activar QR
                                    </button>

                                    <!-- QR Code Display -->
                                    <div x-show="sessionActive" x-cloak class="space-y-4">
                                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col items-center">
                                            <div id="qr-{{ $clase->id }}" class="mb-3"></div>
                                            <div class="text-center">
                                                <p class="text-xs text-gray-600 mb-1">Expira en:</p>
                                                <p class="text-2xl font-bold text-indigo-600" x-text="timeLeft + 's'"></p>
                                            </div>
                                        </div>

                                        <button 
                                            @click="closeSession()"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                            Cerrar Sesión
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        function qrSession(classAssignmentId) {
            return {
                sessionActive: false,
                timeLeft: 45,
                qrCode: null,
                intervalId: null,
                refreshIntervalId: null,

                startSession() {
                    // Obtener ubicación si está disponible
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => this.generateQr(position.coords.latitude, position.coords.longitude),
                            () => this.generateQr(null, null)
                        );
                    } else {
                        this.generateQr(null, null);
                    }
                },

                generateQr(latitude, longitude) {
                    fetch('{{ route("attendance.qr.generate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            class_assignment_id: classAssignmentId,
                            latitude: latitude,
                            longitude: longitude
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.sessionActive = true;
                            this.displayQr(data.qr_data);
                            this.startCountdown();
                            this.startAutoRefresh();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                },

                displayQr(qrData) {
                    const container = document.getElementById(`qr-${classAssignmentId}`);
                    container.innerHTML = '';
                    
                    this.qrCode = new QRCode(container, {
                        text: qrData,
                        width: 200,
                        height: 200,
                        colorDark: "#4f46e5",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                },

                startCountdown() {
                    this.timeLeft = 45;
                    this.intervalId = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            this.timeLeft = 45;
                        }
                    }, 1000);
                },

                startAutoRefresh() {
                    this.refreshIntervalId = setInterval(() => {
                        this.refreshToken();
                    }, 45000); // Cada 45 segundos
                },

                refreshToken() {
                    fetch('{{ route("attendance.qr.refresh") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            class_assignment_id: classAssignmentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.displayQr(data.qr_data);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                },

                closeSession() {
                    fetch('{{ route("attendance.qr.close") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            class_assignment_id: classAssignmentId
                        })
                    })
                    .then(() => {
                        this.sessionActive = false;
                        clearInterval(this.intervalId);
                        clearInterval(this.refreshIntervalId);
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
