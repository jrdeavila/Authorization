<?php

namespace Tests\Feature\Permissions;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

abstract class PermissionsTestCase extends TestCase
{
    /**
     * Do NOT use RefreshDatabase — it triggers Spatie migrations
     * with cross-DB prefixed table names that break on SQLite.
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureDatabases();
        $this->overridePermissionTableNames();
        $this->createSpatieTablesOnDefault();
        $this->createTimeitTables();
        $this->seedTimeitData();
        $this->resetSpatieCache();
        $this->seedGatePermissions();
    }

    /**
     * Make 'mysql' connection share the same SQLite PDO as the default connection,
     * and configure 'timeit' as a separate SQLite :memory: database.
     */
    protected function configureDatabases(): void
    {
        // Default connection is already sqlite :memory: via phpunit.xml.
        // Force the default connection to initialize its PDO now.
        $defaultPdo = DB::connection()->getPdo();

        // Make 'mysql' share the same in-memory PDO as default sqlite.
        // Setting database to empty string prevents SQLite from prefixing
        // table names with the database schema (e.g., ':memory:.table').
        DB::purge('mysql');
        config([
            'database.connections.mysql' => [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
                'foreign_key_constraints' => true,
            ],
        ]);
        // Resolve the connection and inject the shared PDO
        $mysqlConn = DB::connection('mysql');
        $mysqlConn->setPdo($defaultPdo);
        $mysqlConn->setReadPdo($defaultPdo);

        // Configure 'timeit' as a separate SQLite :memory: database
        DB::purge('timeit');
        config([
            'database.connections.timeit' => [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
                'foreign_key_constraints' => true,
            ],
        ]);
    }

    /**
     * Override Spatie permission table names to remove the cross-DB prefix
     * (e.g., "mydb.model_has_permissions" -> "model_has_permissions").
     */
    protected function overridePermissionTableNames(): void
    {
        config([
            'permission.table_names.model_has_permissions' => 'model_has_permissions',
            'permission.table_names.model_has_roles'       => 'model_has_roles',
            'permission.table_names.role_has_permissions'   => 'role_has_permissions',
            'permission.table_names.roles'                 => 'roles',
            'permission.table_names.permissions'            => 'permissions',
        ]);
    }

    /**
     * Create Spatie tables and activity_log on the default (sqlite) connection.
     */
    protected function createSpatieTablesOnDefault(): void
    {
        $db = DB::connection();

        $db->statement('CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');

        $db->statement('CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');

        $db->statement('CREATE TABLE IF NOT EXISTS model_has_permissions (
            permission_id INTEGER NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id INTEGER NOT NULL,
            PRIMARY KEY (permission_id, model_id, model_type),
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        )');

        $db->statement('CREATE TABLE IF NOT EXISTS model_has_roles (
            role_id INTEGER NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id INTEGER NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        )');

        $db->statement('CREATE TABLE IF NOT EXISTS role_has_permissions (
            permission_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            PRIMARY KEY (permission_id, role_id),
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        )');

        // Create usuarios table on default connection too, because
        // Role->users()->count() in views queries through model_has_roles
        // pivot which lives on the default connection and joins to usuarios.
        $db->statement('CREATE TABLE IF NOT EXISTS usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            correo VARCHAR(255) NOT NULL,
            clave VARCHAR(255) NOT NULL,
            rol VARCHAR(50) NULL,
            estado VARCHAR(50) DEFAULT "Activo",
            Empleados_id INTEGER NULL
        )');

        $db->statement('CREATE TABLE IF NOT EXISTS activity_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            log_name VARCHAR(255) NULL,
            description TEXT NOT NULL,
            subject_type VARCHAR(255) NULL,
            event VARCHAR(255) NULL,
            subject_id INTEGER NULL,
            causer_type VARCHAR(255) NULL,
            causer_id INTEGER NULL,
            properties TEXT NULL,
            batch_uuid VARCHAR(36) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');
    }

    /**
     * Create timeit tables: usuarios, empleados, cargos, hojasdevidas,
     * plus model_has_roles and model_has_permissions for Spatie pivot queries.
     */
    protected function createTimeitTables(): void
    {
        $timeit = DB::connection('timeit');

        $timeit->statement('CREATE TABLE IF NOT EXISTS cargos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(255) NOT NULL,
            Areas_id INTEGER NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');

        $timeit->statement('CREATE TABLE IF NOT EXISTS empleados (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombres VARCHAR(255) NOT NULL,
            apellidos VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            estado VARCHAR(50) DEFAULT "Activo",
            Cargos_id INTEGER NULL,
            tipodocumento VARCHAR(50) NULL,
            noDocumento VARCHAR(50) NULL,
            sexo VARCHAR(10) NULL,
            fechanacimiento DATE NULL,
            foto VARCHAR(255) NULL,
            tipofuncionario VARCHAR(50) NULL,
            FOREIGN KEY (Cargos_id) REFERENCES cargos(id)
        )');

        $timeit->statement('CREATE TABLE IF NOT EXISTS usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            correo VARCHAR(255) NOT NULL,
            clave VARCHAR(255) NOT NULL,
            rol VARCHAR(50) NULL,
            estado VARCHAR(50) DEFAULT "Activo",
            Empleados_id INTEGER NULL,
            FOREIGN KEY (Empleados_id) REFERENCES empleados(id)
        )');

        $timeit->statement('CREATE TABLE IF NOT EXISTS hojasdevidas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empleados_id INTEGER NULL,
            foto VARCHAR(255) NULL,
            nivelacademico VARCHAR(255) NULL,
            correo VARCHAR(255) NULL,
            telefono VARCHAR(50) NULL,
            ext VARCHAR(20) NULL,
            municipio_id INTEGER NULL,
            valorcontrato DECIMAL(15,2) NULL,
            objeto TEXT NULL,
            fondo_pensiones VARCHAR(255) NULL,
            FOREIGN KEY (empleados_id) REFERENCES empleados(id)
        )');

        // Spatie pivot tables on the timeit connection are NOT needed —
        // the permission config now points to the default connection's tables.
    }

    /**
     * Insert test employee, job, curriculum, and user records into timeit.
     */
    protected function seedTimeitData(): void
    {
        $timeit = DB::connection('timeit');

        $timeit->table('cargos')->insert([
            'id'     => 1,
            'nombre' => 'Analista de Sistemas',
        ]);

        $timeit->table('empleados')->insert([
            'id'              => 1,
            'nombres'         => 'Admin',
            'apellidos'       => 'Tester',
            'email'           => 'admin@test.com',
            'estado'          => 'Activo',
            'Cargos_id'       => 1,
            'tipodocumento'   => 'CC',
            'noDocumento'     => '12345678',
            'sexo'            => 'M',
            'fechanacimiento' => '1990-01-01',
            'foto'            => 'photo.jpg',
            'tipofuncionario' => 'Planta',
        ]);

        $timeit->table('hojasdevidas')->insert([
            'id'            => 1,
            'empleados_id'  => 1,
            'foto'          => 'photo.jpg',
        ]);

        $timeit->table('usuarios')->insert([
            'id'           => 1,
            'correo'       => 'admin@test.com',
            'clave'        => Hash::make('password'),
            'rol'          => 'admin',
            'estado'       => 'Activo',
            'Empleados_id' => 1,
        ]);

        // A second user for list tests
        $timeit->table('empleados')->insert([
            'id'              => 2,
            'nombres'         => 'Regular',
            'apellidos'       => 'User',
            'email'           => 'user@test.com',
            'estado'          => 'Activo',
            'Cargos_id'       => 1,
            'tipodocumento'   => 'CC',
            'noDocumento'     => '87654321',
            'sexo'            => 'F',
            'fechanacimiento' => '1995-06-15',
            'foto'            => 'photo2.jpg',
            'tipofuncionario' => 'Contratista',
        ]);

        $timeit->table('hojasdevidas')->insert([
            'id'            => 2,
            'empleados_id'  => 2,
            'foto'          => 'photo2.jpg',
        ]);

        $timeit->table('usuarios')->insert([
            'id'           => 2,
            'correo'       => 'user@test.com',
            'clave'        => Hash::make('password'),
            'rol'          => 'user',
            'estado'       => 'Activo',
            'Empleados_id' => 2,
        ]);
    }

    /**
     * Reset Spatie's cached permissions so each test starts fresh.
     */
    protected function resetSpatieCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Seed the permissions and role required by Gates defined in AppServiceProvider.
     * The views use @can('manage-roles') etc., which call hasPermissionTo()
     * and will throw if the permission doesn't exist in the DB.
     */
    protected function seedGatePermissions(): void
    {
        $permissionNames = [
            'roles.manage',
            'permissions.manage',
            'users.assign',
            'audit.view',
            'reports.export',
            'reports.import',
        ];

        foreach ($permissionNames as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $adminRole = Role::findOrCreate('super-admin', 'web');
        $adminRole->syncPermissions($permissionNames);

        // Reset cache after seeding
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Get an authenticated admin user with the super-admin role.
     */
    protected function authenticatedAdmin(): User
    {
        $user = User::find(1);
        $user->assignRole('super-admin');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($user);

        return $user;
    }
}
