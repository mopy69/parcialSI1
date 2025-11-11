<x-layouts.admin>
@push('styles')
<style>
    .attendance-grid {
        width: 100%;
        table-layout: fixed;
    }
    
    .attendance-grid th,
    .attendance-grid td {
        padding: 0;
        text-align: center;
        vertical-align: middle;
    }
    
    .attendance-grid thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .attendance-grid .time-col {
        width: 10%;
        min-width: 80px;
    }
    
    .attendance-grid .day-col {
        width: 15%;
        min-width: 180px;
    }
    
    .attendance-block {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 12px;
        min-height: 80px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        border: 2px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .attendance-block::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.7) 50%, rgba(255,255,255,0.3) 100%);
    }
    
    .attendance-block:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.6);
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    .attendance-block:active {
        transform: translateY(-2px);
    }
    
    .attendance-block .subject-name {
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 6px;
        line-height: 1.3;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    
    .attendance-block .info-row {
        font-size: 0.75rem;
        opacity: 0.95;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .attendance-block .duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .attendance-badges {
        display: flex;
        gap: 4px;
        margin-top: 6px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .attendance-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.625rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(12px);
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .attendance-badge:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(1.05);
    }
    
    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        transition: all 0.2s;
    }
    
    .status-pendiente { background-color: #9ca3af; }
    .status-a-tiempo { background-color: #10b981; box-shadow: 0 0 6px rgba(16, 185, 129, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-puntual { background-color: #10b981; box-shadow: 0 0 6px rgba(16, 185, 129, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-temprano { background-color: #eab308; box-shadow: 0 0 6px rgba(234, 179, 8, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-tarde { background-color: #f59e0b; box-shadow: 0 0 6px rgba(245, 158, 11, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-falta { background-color: #ef4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-no-llego { background-color: #ef4444; box-shadow: 0 0 6px rgba(239, 68, 68, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    .status-justificado { background-color: #3b82f6; box-shadow: 0 0 6px rgba(59, 130, 246, 0.6), 0 0 0 2px rgba(255, 255, 255, 0.3); }
    
    .modal-attendance {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
    }
    
    .modal-content-attendance {
        background-color: #fefefe;
        margin: 3% auto;
        padding: 28px;
        border-radius: 16px;
        width: 90%;
        max-width: 750px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .date-attendance-item {
        padding: 18px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 14px;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .date-attendance-item:hover {
        background: white;
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        transform: translateY(-2px);
    }
    
    .date-header {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1f2937;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .attendance-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 12px;
    }
    
    .attendance-type-section {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
        transition: all 0.2s;
    }
    
    .attendance-type-section:hover {
        border-color: #c7d2fe;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.1);
    }
    
    .attendance-type-section h4 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 10px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.05em;
    }
</style>
@endpush

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <a href="{{ route('admin.teacher-attendance.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a la lista
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">
                Asistencias - {{ $docente->name }}
            </h1>
        </div>
    </div>

    @if($currentTerm)
    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium text-indigo-800">
                Gestión: <strong>{{ $currentTerm->name }}</strong>
                <span class="text-gray-600 ml-2">({{ $currentTerm->start_date }} al {{ $currentTerm->end_date }})</span>
            </span>
        </div>
    </div>
    @endif
</div>

<div class="bg-white overflow-hidden shadow-lg rounded-xl p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Control de Asistencias
    </h1>

    <div class="overflow-x-auto -mx-6 sm:mx-0 rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700 sticky top-0 z-10">
                <tr>
                    <th class="time-col px-3 sm:px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider sticky left-0 bg-indigo-600 shadow-md z-20">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Hora
                        </div>
                    </th>
                    @foreach($dias as $dia)
                        <th class="day-col px-4 py-4 text-center text-sm font-bold text-white uppercase tracking-wider">
                            {{ ucfirst($dia) }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($franjasHorarias as $franja)
                    <tr class="hover:bg-indigo-50/30 transition-colors" style="height: 70px;">
                        <td class="time-col px-3 sm:px-4 py-3 whitespace-nowrap text-xs font-bold text-gray-700 align-middle sticky left-0 bg-gray-50 shadow-sm z-10 border-r border-gray-200">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-indigo-700">{{ $franja }}</span>
                                <span class="text-gray-400 text-[10px]">-</span>
                                @php
                                    $horaFin = \Carbon\Carbon::parse($franja)->addMinutes(15)->format('H:i');
                                @endphp
                                <span class="text-indigo-700">{{ $horaFin }}</span>
                            </div>
                        </td>
                        @foreach($dias as $dia)
                            @php
                                $clave = $dia . '_' . $franja;
                                $claseEncontrada = $clasesAsignadasAgrupadas->first(function($grupo) use ($dia, $franja) {
                                    return $grupo['class']->timeslot->day === $dia 
                                        && \Carbon\Carbon::parse($grupo['class']->timeslot->start)->format('H:i') === $franja;
                                });
                                
                                $esCeldaOcupada = $clasesRenderedSet->contains(function($key) use ($dia, $franja) {
                                    return str_starts_with($key, $dia . '_' . $franja);
                                });
                            @endphp
                            
                            @if($claseEncontrada)
                                <td rowspan="{{ $claseEncontrada['rowspan'] }}" class="day-col px-0 py-0 align-top relative" style="height: {{ $claseEncontrada['rowspan'] * 70 }}px;">
                                    <div class="absolute inset-0 p-2">
                                        <div class="attendance-block" 
                                             onclick="openAttendanceModal({{ json_encode($claseEncontrada['class_ids']) }}, '{{ $claseEncontrada['class']->courseOffering->subject->name }}', '{{ $claseEncontrada['class']->courseOffering->group->name }}', '{{ $claseEncontrada['class']->classroom->name }}')">
                                            @php
                                                // Obtener la primera asistencia de entrada y salida para mostrar estados
                                                $attendances = $claseEncontrada['class']->teacherAttendances;
                                                $entradaAtt = $attendances->where('type', 'entrada')->first();
                                                $salidaAtt = $attendances->where('type', 'salida')->first();
                                            @endphp
                                            
                                            <div>
                                                <div class="subject-name">{{ $claseEncontrada['class']->courseOffering->subject->name }}</div>
                                                <div class="info-row">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                                    </svg>
                                                    {{ $claseEncontrada['class']->courseOffering->group->name }}
                                                </div>
                                                <div class="info-row">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Aula {{ $claseEncontrada['class']->classroom->name }}
                                                </div>
                                                <div class="duration-badge mt-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ $claseEncontrada['duration'] }} min
                                                </div>
                                            </div>
                                            
                                            <div class="attendance-badges">
                                                @if($entradaAtt)
                                                    <span class="attendance-badge">
                                                        <span class="status-dot status-{{ str_replace(' ', '-', $entradaAtt->state) }}"></span>
                                                        Entrada
                                                    </span>
                                                @endif
                                                @if($salidaAtt)
                                                    <span class="attendance-badge">
                                                        <span class="status-dot status-{{ str_replace(' ', '-', $salidaAtt->state) }}"></span>
                                                        Salida
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @elseif(!$esCeldaOcupada)
                                <td class="day-col px-2 py-2 align-top bg-gray-50/30">
                                    <div class="w-full h-full min-h-[60px] bg-gray-100/30 rounded-lg"></div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para gestionar asistencias -->
<div id="attendanceModal" class="modal-attendance">
    <div class="modal-content-attendance">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Gestionar Asistencias</h3>
                <p class="text-sm text-gray-600 mt-1" id="modalSubtitle"></p>
            </div>
            <button type="button" onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="attendancesList" class="space-y-4">
            <!-- Se llenará dinámicamente -->
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentClassIds = [];
let attendancesData = @json($clasesAsignadasAgrupadas->mapWithKeys(function($grupo) {
    return [$grupo['class']->id => $grupo['class']->teacherAttendances];
}));

function openAttendanceModal(classIds, subjectName, groupName, classroomName) {
    currentClassIds = classIds;
    document.getElementById('modalTitle').textContent = `${subjectName} - ${groupName}`;
    document.getElementById('modalSubtitle').textContent = `Aula ${classroomName}`;
    
    // Obtener todas las asistencias de estas clases
    let allAttendances = [];
    classIds.forEach(classId => {
        if (attendancesData[classId]) {
            allAttendances = allAttendances.concat(attendancesData[classId]);
        }
    });
    
    // Agrupar por fecha y tipo
    let attendancesByDate = {};
    allAttendances.forEach(att => {
        if (!attendancesByDate[att.date]) {
            attendancesByDate[att.date] = {
                entrada: null,
                salida: null
            };
        }
        attendancesByDate[att.date][att.type] = att;
    });
    
    // Ordenar fechas (más recientes primero)
    let sortedDates = Object.keys(attendancesByDate).sort().reverse();
    
    // Generar HTML
    let html = '';
    if (sortedDates.length === 0) {
        html = '<p class="text-center text-gray-500 py-8">No hay asistencias registradas</p>';
    } else {
        sortedDates.forEach(date => {
            let entrada = attendancesByDate[date].entrada;
            let salida = attendancesByDate[date].salida;
            let dateObj = new Date(date + 'T00:00:00');
            let formattedDate = dateObj.toLocaleDateString('es-ES', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            html += `
                <div class="date-attendance-item">
                    <div class="date-header">
                        <span>${formattedDate}</span>
                        <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full">${date}</span>
                    </div>
                    <div class="attendance-form-grid">
                        ${entrada ? `
                        <div class="attendance-type-section">
                            <h4>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Entrada
                            </h4>
                            <form method="POST" action="{{ route('admin.teacher-attendance.update') }}">
                                @csrf
                                <input type="hidden" name="attendance_id" value="${entrada.id}">
                                <input type="hidden" name="type" value="entrada">
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Estado</label>
                                    <select name="state" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pendiente" ${entrada.state === 'pendiente' ? 'selected' : ''}>⚪ Pendiente</option>
                                        <option value="a tiempo" ${entrada.state === 'a tiempo' ? 'selected' : ''}>🟢 A tiempo</option>
                                        <option value="tarde" ${entrada.state === 'tarde' ? 'selected' : ''}>🟠 Tarde</option>
                                        <option value="falta" ${entrada.state === 'falta' ? 'selected' : ''}>🔴 Falta</option>
                                        <option value="justificado" ${entrada.state === 'justificado' ? 'selected' : ''}>🔵 Justificado</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                    Actualizar
                                </button>
                            </form>
                        </div>
                        ` : '<div class="attendance-type-section"><p class="text-center text-gray-500 text-sm">Sin registro</p></div>'}
                        
                        ${salida ? `
                        <div class="attendance-type-section">
                            <h4>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Salida
                            </h4>
                            <form method="POST" action="{{ route('admin.teacher-attendance.update') }}">
                                @csrf
                                <input type="hidden" name="attendance_id" value="${salida.id}">
                                <input type="hidden" name="type" value="salida">
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Estado</label>
                                    <select name="state" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pendiente" ${salida.state === 'pendiente' ? 'selected' : ''}>⚪ Pendiente</option>
                                        <option value="a tiempo" ${salida.state === 'a tiempo' ? 'selected' : ''}>🟢 A tiempo</option>
                                        <option value="puntual" ${salida.state === 'puntual' ? 'selected' : ''}>🟢 Puntual</option>
                                        <option value="temprano" ${salida.state === 'temprano' ? 'selected' : ''}>🟡 Temprano</option>
                                        <option value="tarde" ${salida.state === 'tarde' ? 'selected' : ''}>🟠 Tarde</option>
                                        <option value="falta" ${salida.state === 'falta' ? 'selected' : ''}>🔴 Falta</option>
                                        <option value="justificado" ${salida.state === 'justificado' ? 'selected' : ''}>🔵 Justificado</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                    Actualizar
                                </button>
                            </form>
                        </div>
                        ` : '<div class="attendance-type-section"><p class="text-center text-gray-500 text-sm">Sin registro</p></div>'}
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('attendancesList').innerHTML = html;
    document.getElementById('attendanceModal').style.display = 'block';
}

function closeAttendanceModal() {
    document.getElementById('attendanceModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    let modal = document.getElementById('attendanceModal');
    if (event.target == modal) {
        closeAttendanceModal();
    }
}
</script>
@endpush
</x-layouts.admin>
