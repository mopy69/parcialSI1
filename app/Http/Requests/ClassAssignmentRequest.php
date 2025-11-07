<?php

namespace App\Http\Requests;

use App\Models\ClassAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClassAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentTermId = data_get(session('current_term'), 'id');

        $rules = [
            'coordinador_id' => 'required',
            'docente_id' => 'required',
            'classroom_id' => 'required',
            'course_offering_id' => [
                'required',
                function ($attribute, $value, $fail) use ($currentTermId) {
                    // Validar que el curso no esté asignado a otro docente
                    if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
                        $existingCourseOffering = ClassAssignment::where('course_offering_id', $value)
                            ->where('id', '!=', $this->route('class_assignment')->id)
                            ->when($currentTermId, function ($query) use ($currentTermId) {
                                $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                            })
                            ->exists();
                    } else {
                        $existingCourseOffering = ClassAssignment::where('course_offering_id', $value)
                            ->when($currentTermId, function ($query) use ($currentTermId) {
                                $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                            })
                            ->exists();
                    }
                    
                    if ($existingCourseOffering) {
                        $fail('Esta materia y grupo ya están asignados a otro docente.');
                    }
                }
            ],
        ];

        // Si es una actualización (PUT/PATCH), espera un solo timeslot_id
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['timeslot_id'] = [
                'required',
                'exists:timeslots,id',
                function ($attribute, $value, $fail) use ($currentTermId) {
                    // Validar que no haya conflicto con el mismo docente
                    $existingAssignment = ClassAssignment::where('docente_id', $this->docente_id)
                        ->where('timeslot_id', $value)
                        ->where('id', '!=', $this->route('class_assignment')->id)
                        ->when($currentTermId, function ($query) use ($currentTermId) {
                            $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                        })
                        ->exists();
                    
                    if ($existingAssignment) {
                        $fail('El docente ya tiene una clase asignada en este horario.');
                    }

                    // Validar que el aula esté disponible
                    $existingClassroom = ClassAssignment::where('classroom_id', $this->classroom_id)
                        ->where('timeslot_id', $value)
                        ->where('id', '!=', $this->route('class_assignment')->id)
                        ->when($currentTermId, function ($query) use ($currentTermId) {
                            $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                        })
                        ->exists();
                    
                    if ($existingClassroom) {
                        $fail('El aula ya está ocupada en este horario.');
                    }
                }
            ];
        } else {
            // Si es creación (POST), espera un array de timeslot_ids
            $rules['timeslot_ids'] = 'required|array';
            $rules['timeslot_ids.*'] = [
                'required',
                'exists:timeslots,id',
                function ($attribute, $value, $fail) use ($currentTermId) {
                    // Validar que no haya conflicto con el mismo docente
                    $existingAssignment = ClassAssignment::where('docente_id', $this->docente_id)
                        ->where('timeslot_id', $value)
                        ->when($currentTermId, function ($query) use ($currentTermId) {
                            $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                        })
                        ->exists();
                    
                    if ($existingAssignment) {
                        $fail('El docente ya tiene una clase asignada en este horario.');
                    }

                    // Validar que el aula esté disponible
                    $existingClassroom = ClassAssignment::where('classroom_id', $this->classroom_id)
                        ->where('timeslot_id', $value)
                        ->when($currentTermId, function ($query) use ($currentTermId) {
                            $query->whereHas('courseOffering', fn($q) => $q->where('term_id', $currentTermId));
                        })
                        ->exists();
                    
                    if ($existingClassroom) {
                        $fail('El aula ya está ocupada en este horario.');
                    }
                }
            ];
        }

        return $rules;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Si es una creación (POST), agregar flag para abrir el modal
        if ($this->isMethod('POST')) {
            throw new HttpResponseException(
                redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('openModal', true)
            );
        }

        parent::failedValidation($validator);
    }
}
