<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;

use App\Models\Role as ModelsRole;

class UsersImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    private $role;
    public function __construct()
    {
        $this->role = ModelsRole::pluck('id', 'name');
    }

    public function model(array $row)
    {
        return new User([
            'name'  => $row['name'],
            'email' => $row['email'],
            'password'    => $row['password'],
            'registration_code' => $row['registro'],
            'title' => $row['titulo'],
            'role_id' => $this->role[$row['rol']],
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
            '*.name' => [
                'required',
                'string'
            ],
            '*.password' => [
                'required',
                'string',
                'min:8'
            ],
            '*.rol' => [
                'required'
            ],
        ];
    }

    public function customValidationMessages()
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
