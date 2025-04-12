<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@carshop.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol de administrador al usuario
//        $adminRole = Role::where('name', 'Super-Admin')->first();
//        if ($adminRole) {
//            $admin->assignRole($adminRole);
//        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('email', 'admin@carshop.com')->delete();
    }
};
