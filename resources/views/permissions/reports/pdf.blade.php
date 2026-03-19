<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Permisos por Funcionario</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #1e40af; }
        .header p { font-size: 10px; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1e40af; color: white; padding: 6px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Permisos por Funcionario</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Cargo</th><th>Roles</th><th>Permisos Directos</th></tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->employee->full_name ?? 'N/A' }}</td>
                    <td>{{ $user->employee->job->name ?? '-' }}</td>
                    <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                    <td>{{ $user->permissions->pluck('name')->implode(', ') ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Sistema de Gestión de Permisos &mdash; {{ config('app.name') }}</div>
</body>
</html>
