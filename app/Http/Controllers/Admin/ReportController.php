<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Group;
use App\Models\Classroom;
use App\Models\User;
use App\Models\CourseOffering;
use App\Models\ClassAssignment;
use App\Models\TeacherAttendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $sessionTerm = session('current_term');
        
        // Si session contiene el objeto Term directamente, usarlo
        // Si contiene el ID, buscarlo
        if ($sessionTerm instanceof Term) {
            $currentTerm = $sessionTerm;
        } elseif ($sessionTerm) {
            $currentTerm = Term::find($sessionTerm);
        } else {
            $currentTerm = null;
        }
        
        return view('admin.reports.index', compact('currentTerm'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'term_id' => 'nullable|exists:terms,id'
        ]);

        $reportType = $request->report_type;
        $termId = $request->term_id;

        $data = $this->getReportData($reportType, $termId);
        
        $fileName = $this->getFileName($reportType, $termId);

        return Excel::download(new ReportExport($data, $reportType), $fileName);
    }

    private function getReportData($reportType, $termId = null)
    {
        switch ($reportType) {
            // Datos Base
            case 'terms':
                return Term::orderBy('start_date', 'desc')->get();
            
            case 'subjects':
                return Subject::orderBy('name')->get();
            
            case 'groups':
                return Group::orderBy('name')->get();
            
            case 'classrooms':
                return Classroom::orderBy('nro')->get();
            
            case 'users':
                return User::with('role')->orderBy('name')->get();

            // Programación
            case 'course_offerings':
                $query = CourseOffering::with(['subject', 'group', 'term', 'classAssignments.userDocente']);
                if ($termId) {
                    $query->where('term_id', $termId);
                }
                return $query->get();
            
            case 'class_assignments':
                $query = ClassAssignment::with(['courseOffering.subject', 'courseOffering.group', 'classroom', 'timeslot', 'userDocente']);
                if ($termId) {
                    $query->whereHas('courseOffering', function($q) use ($termId) {
                        $q->where('term_id', $termId);
                    });
                }
                return $query->get();

            // Asistencia
            case 'teacher_attendance':
                $query = TeacherAttendance::with(['classAssignment.courseOffering.subject', 'classAssignment.courseOffering.group', 'classAssignment.userDocente']);
                if ($termId) {
                    $query->whereHas('classAssignment.courseOffering', function($q) use ($termId) {
                        $q->where('term_id', $termId);
                    });
                }
                return $query->orderBy('date', 'desc')->get();
            
            default:
                return collect();
        }
    }

    private function getFileName($reportType, $termId = null)
    {
        $term = $termId ? Term::find($termId) : null;
        $termName = $term ? '_' . str_replace(' ', '_', $term->name) : '';
        $date = now()->format('Y-m-d');

        $names = [
            'terms' => 'Terminos_Academicos',
            'subjects' => 'Materias',
            'groups' => 'Grupos',
            'classrooms' => 'Aulas',
            'users' => 'Usuarios',
            'course_offerings' => 'Ofertas_de_Cursos',
            'class_assignments' => 'Asignacion_de_Clases',
            'teacher_attendance' => 'Asistencia_Docente',
        ];

        return ($names[$reportType] ?? 'Reporte') . $termName . '_' . $date . '.xlsx';
    }
}
