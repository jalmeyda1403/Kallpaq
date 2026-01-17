<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['nombre'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'user_cod_personal' => $row['codigo_personal'] ?? null,
            'user_iniciales' => $row['iniciales'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'codigo_personal' => 'nullable|unique:users,user_cod_personal',
            'iniciales' => 'nullable|unique:users,user_iniciales',
        ];
    }
}
