<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $reportType;

    public function __construct($data, $reportType)
    {
        $this->data = $data;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->getHeadings($this->reportType);
    }

    public function map($row): array
    {
        return $this->mapRow($row, $this->reportType);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    private function getHeadings($type)
    {
        $headings = [
            'terms' => ['ID', 'Nombre', 'Fecha Inicio', 'Fecha Fin', 'Estado'],
            'subjects' => ['ID', 'Código', 'Nombre'],
            'groups' => ['ID', 'Nombre', 'Descripción'],
            'classrooms' => ['ID', 'Número', 'Tipo', 'Capacidad'],
            'users' => ['ID', 'Nombre', 'Email', 'CI', 'Registro', 'Título', 'Creado'],
            'course_offerings' => ['ID', 'Materia', 'Grupo', 'Gestión', 'Docente'],
            'class_assignments' => ['ID', 'Materia', 'Grupo', 'Docente', 'Aula', 'Día', 'Hora Inicio', 'Hora Fin'],
            'teacher_attendance' => ['ID', 'Docente', 'Materia', 'Grupo', 'Fecha', 'Tipo', 'Estado', 'Creado'],
        ];

        return $headings[$type] ?? ['Dato'];
    }

    private function mapRow($row, $type)
    {
        switch ($type) {
            case 'terms':
                return [
                    $row->id,
                    $row->name,
                    $row->start_date,
                    $row->end_date,
                    $row->is_active ? 'Activo' : 'Inactivo'
                ];

            case 'subjects':
                return [
                    $row->id,
                    $row->code,
                    $row->name
                ];

            case 'groups':
                return [
                    $row->id,
                    $row->name,
                    $row->description ?? 'N/A'
                ];

            case 'classrooms':
                return [
                    $row->id,
                    $row->nro,
                    $row->type,
                    $row->capacity
                ];

            case 'users':
                return [
                    $row->id,
                    $row->name,
                    $row->email,
                    $row->ci ?? 'N/A',
                    $row->registration_code ?? 'N/A',
                    $row->title ?? 'N/A',
                    $row->created_at ? $row->created_at->format('Y-m-d H:i') : 'N/A'
                ];

            case 'course_offerings':
                // Obtener el primer docente asignado (puede haber varios)
                $teacher = $row->classAssignments->first()?->userDocente;
                return [
                    $row->id,
                    $row->subject->name ?? 'N/A',
                    $row->group->name ?? 'N/A',
                    $row->term->name ?? 'N/A',
                    $teacher?->name ?? 'N/A'
                ];

            case 'class_assignments':
                return [
                    $row->id,
                    $row->courseOffering->subject->name ?? 'N/A',
                    $row->courseOffering->group->name ?? 'N/A',
                    $row->userDocente->name ?? 'N/A',
                    $row->classroom->nro ?? 'N/A',
                    $row->timeslot->day ?? 'N/A',
                    $row->timeslot ? \Carbon\Carbon::parse($row->timeslot->start)->format('H:i') : 'N/A',
                    $row->timeslot ? \Carbon\Carbon::parse($row->timeslot->end)->format('H:i') : 'N/A'
                ];

            case 'teacher_attendance':
                return [
                    $row->id,
                    $row->classAssignment->userDocente->name ?? 'N/A',
                    $row->classAssignment->courseOffering->subject->name ?? 'N/A',
                    $row->classAssignment->courseOffering->group->name ?? 'N/A',
                    $row->date,
                    ucfirst($row->type),
                    ucfirst($row->state),
                    $row->created_at ? $row->created_at->format('Y-m-d H:i') : 'N/A'
                ];

            default:
                return [$row];
        }
    }
}
