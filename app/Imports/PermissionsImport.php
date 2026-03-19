<?php

namespace App\Imports;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class PermissionsImport implements ToModel, WithHeadingRow, SkipsOnError, WithValidation
{
    private int $imported = 0;
    private array $errors = [];

    public function model(array $row)
    {
        $user = User::find($row['user_id']);

        if (!$user) {
            $this->errors[] = "Usuario {$row['user_id']} no encontrado.";
            return null;
        }

        if (!empty($row['roles'])) {
            $roleNames = array_map('trim', explode(',', $row['roles']));
            $roles = Role::whereIn('name', $roleNames)->where('guard_name', 'web')->get();
            $user->syncRoles($roles);
        }

        if (!empty($row['permissions'])) {
            $permNames = array_map('trim', explode(',', $row['permissions']));
            $perms = Permission::whereIn('name', $permNames)->where('guard_name', 'web')->get();
            $user->syncPermissions($perms);
        }

        $this->imported++;
        return null;
    }

    public function rules(): array
    {
        return ['user_id' => 'required|integer'];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
