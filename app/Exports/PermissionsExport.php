<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PermissionsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return User::with(['employee.job', 'roles.permissions', 'permissions'])
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'nombre' => $user->employee->full_name ?? 'N/A',
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->implode(', '),
                    'permisos_directos' => $user->getDirectPermissions()->pluck('name')->implode(', '),
                    'permisos_efectivos' => $user->getAllPermissions()->pluck('name')->implode(', '),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID Usuario', 'Nombre', 'Email', 'Roles', 'Permisos Directos', 'Permisos Efectivos'];
    }

    public function title(): string
    {
        return 'Permisos por Funcionario';
    }
}
