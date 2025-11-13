<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

use App\Models\Role as ModelsRole;

class UsersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $teacherRoleId;
    public function __construct()
    {
        $this->teacherRoleId = ModelsRole::where('name', 'Docente')->value('id');
    }

    public function model(array $row)
    {
        return new User([
            'name'  => $row['nombre'],
            'email' => $row['email'],
            'password'    => Hash::make($row['ci']),
            'registration_code' => $row['registro'],
            'title' => $row['titulo'],
            'role_id' => $this->teacherRoleId,
            'ci' => $row['ci'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.email' => [
                'email',
                'unique:users,email',
                'required'
            ],
            '*.nombre' => [
                'required',
                'string'
            ],
            '*.registro' => [
                'required',
            ],
            '*.titulo' => [
                'required',
                'string'
            ],
            '*.ci' => [
                'required',
                'min:7'
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
