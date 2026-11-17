<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingrediente;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Proveedor;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedUsers();
        $this->seedCategoriesAndProducts();
        $this->seedProveedoresEIngredientes();
    }

    private function seedRoles(): void
    {
        if (Role::count() > 0) {
            return;
        }

        Role::insert([
            ['id_roles' => 1, 'nombre' => 'Administrador', 'descripcion' => 'usuario con acceso total al sistema', 'estado' => 'Activo'],
            ['id_roles' => 2, 'nombre' => 'empleado', 'descripcion' => 'usuario operativo con algunos permisos', 'estado' => 'Activo'],
            ['id_roles' => 3, 'nombre' => 'Cliente', 'descripcion' => 'cliente de la tienda', 'estado' => 'Activo'],
        ]);
    }

    private function seedPermissions(): void
    {
        if (Permission::count() > 0) {
            return;
        }

        Permission::create([
            'id_permisos' => 1,
            'nombre' => 'crear_usuarios',
            'descripcion' => 'permite registrar nuevos usuarios',
        ]);

        DB::table('rol_permiso')->insertOrIgnore([
            'id_rol_permiso' => 3,
            'id_roles' => 1,
            'id_permisos' => 1,
        ]);
    }

    private function seedUsers(): void
    {
        if (Usuario::count() > 0) {
            return;
        }

        Usuario::insert([
            [
                'id' => 1,
                'nombre' => 'catalina',
                'documento' => '1028141208',
                'correo' => 'catarojasvaz@gmail.com',
                'usuario' => 'catalina',
                'contrasena' => '123',
                'id_rol' => 1,
                'estado' => 'Activo',
            ],
            [
                'id' => 2,
                'nombre' => 'juan',
                'documento' => '12345678',
                'correo' => 'ramirezballesterosjuanpablo@gmail.com',
                'usuario' => 'rr',
                'contrasena' => '$2y$10$BOWqWn7JJsK5GU.jEjJlLujUaUXavcCS2zX5XjpDFsCEN1l7eRPVu',
                'id_rol' => 1,
                'estado' => 'activo',
            ],
        ]);
    }

    /**
     * Categorías oficiales del menú (las 8 del restaurante) + Acompañamientos.
     * firstOrCreate es idempotente: se puede ejecutar `php artisan db:seed`
     * en una base de datos existente sin duplicar categorías ni tocar productos.
     */
    private function seedCategories(): void
    {
        $categorias = [
            'Salchipapas', 'Tociqueso', 'Picadas', 'Hamburguesas',
            'Perros', 'Perras', 'Bebidas', 'Pirrypollo', 'Adiciones',
            'Acompañamientos',
        ];

        foreach ($categorias as $nombre) {
            Category::firstOrCreate(['nombre' => $nombre]);
        }
    }

    private function seedCategoriesAndProducts(): void
    {
        $this->seedCategories();

        if (Product::count() > 0) {
            return;
        }

        $salchipapas = Category::where('nombre', 'Salchipapas')->first()->id;
        $hamburguesas = Category::where('nombre', 'Hamburguesas')->first()->id;
        $bebidas = Category::where('nombre', 'Bebidas')->first()->id;
        $acompanamientos = Category::where('nombre', 'Acompañamientos')->first()->id;
        $adiciones = Category::where('nombre', 'Adiciones')->first()->id;

        $imgSalchi = 'https://images.unsplash.com/photo-1585238342024-78d3877844da?w=400';
        $imgHamb = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400';
        $imgBebida = 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=400';
        $imgPapas = 'https://images.unsplash.com/photo-1573080496219-b0800814c710?w=400';

        $productos = [
            ['nombre' => 'Salchipapa Clásica', 'descripcion' => 'Papas fritas con salchicha americana y salsas', 'precio' => 12000, 'imagen' => $imgSalchi, 'id_categoria' => $salchipapas],
            ['nombre' => 'Salchipapa Especial', 'descripcion' => 'Salchipapa con queso, huevo y tocineta', 'precio' => 15000, 'imagen' => $imgSalchi, 'id_categoria' => $salchipapas],
            ['nombre' => 'Salchipapa Mega', 'descripcion' => 'Porción grande con todos los toppings', 'precio' => 18000, 'imagen' => $imgSalchi, 'id_categoria' => $salchipapas],
            ['nombre' => 'Hamburguesa Sencilla', 'descripcion' => 'Carne, lechuga, tomate y salsas', 'precio' => 10000, 'imagen' => $imgHamb, 'id_categoria' => $hamburguesas],
            ['nombre' => 'Hamburguesa Doble Carne', 'descripcion' => 'Doble carne de res con queso cheddar', 'precio' => 15000, 'imagen' => $imgHamb, 'id_categoria' => $hamburguesas],
            ['nombre' => 'Hamburguesa El Pirry', 'descripcion' => 'Nuestra especialidad con ingredientes premium', 'precio' => 18000, 'imagen' => $imgHamb, 'id_categoria' => $hamburguesas],
            ['nombre' => 'Perro Caliente', 'descripcion' => 'Salchicha con salsas y papas ripio', 'precio' => 8000, 'imagen' => $imgHamb, 'id_categoria' => $hamburguesas],
            ['nombre' => 'Gaseosa Personal', 'descripcion' => 'Coca-Cola, Sprite o Colombiana 400ml', 'precio' => 3000, 'imagen' => $imgBebida, 'id_categoria' => $bebidas],
            ['nombre' => 'Jugo Natural', 'descripcion' => 'Jugo de fruta natural del día', 'precio' => 5000, 'imagen' => $imgBebida, 'id_categoria' => $bebidas],
            ['nombre' => 'Papas Fritas', 'descripcion' => 'Porción de papas crujientes', 'precio' => 6000, 'imagen' => $imgPapas, 'id_categoria' => $acompanamientos],
            // Adiciones (precio 0 = sin precio; nunca se muestra al cliente)
            ['nombre' => 'Tocineta', 'descripcion' => 'Adición de tocineta crujiente para tus platos', 'precio' => 0, 'imagen' => null, 'id_categoria' => $adiciones],
            ['nombre' => 'Queso', 'descripcion' => 'Adición de queso para tus platos', 'precio' => 0, 'imagen' => null, 'id_categoria' => $adiciones],
            ['nombre' => 'Chorizo', 'descripcion' => 'Adición de chorizo para tus platos', 'precio' => 0, 'imagen' => null, 'id_categoria' => $adiciones],
            ['nombre' => 'Huevos', 'descripcion' => 'Adición de huevos para tus platos', 'precio' => 0, 'imagen' => null, 'id_categoria' => $adiciones],
            ['nombre' => 'Butifarras', 'descripcion' => 'Adición de butifarra para tus platos', 'precio' => 0, 'imagen' => null, 'id_categoria' => $adiciones],
        ];

        foreach ($productos as $p) {
            Product::create(array_merge($p, [
                'disponible' => true,
                'estado' => 'activo',
            ]));
        }
    }

    /**
     * Datos de ejemplo para los nuevos módulos de Proveedores e Ingredientes,
     * incluyendo sus relaciones con productos (receta) y una compra inicial.
     */
    private function seedProveedoresEIngredientes(): void
    {
        if (Proveedor::count() > 0 || Ingrediente::count() > 0) {
            return;
        }

        $carnesYA = Proveedor::create([
            'nombre' => 'Carnes y Algo Más S.A.S',
            'identificacion' => '900123456-1',
            'telefono' => '3001112233',
            'direccion' => 'Cra 50 #10-20, Guayabal',
            'email' => 'ventas@carnesyalgomas.com',
            'estado' => 'activo',
        ]);

        $distribuidoraPapa = Proveedor::create([
            'nombre' => 'Distribuidora La Papa Dorada',
            'identificacion' => '900987654-2',
            'telefono' => '3007778899',
            'direccion' => 'Calle 30 #45-12, Itagüí',
            'email' => 'contacto@lapapadorada.com',
            'estado' => 'activo',
        ]);

        $panDelBarrio = Ingrediente::create(['nombre' => 'Pan de hamburguesa', 'unidad_medida' => 'unidad', 'stock_actual' => 150, 'stock_minimo' => 30, 'estado' => 'activo']);
        $carneRes = Ingrediente::create(['nombre' => 'Carne de res molida', 'unidad_medida' => 'kg', 'stock_actual' => 25, 'stock_minimo' => 10, 'estado' => 'activo']);
        $papaFresca = Ingrediente::create(['nombre' => 'Papa fresca', 'unidad_medida' => 'kg', 'stock_actual' => 40, 'stock_minimo' => 15, 'estado' => 'activo']);
        $quesoMozzarella = Ingrediente::create(['nombre' => 'Queso mozzarella', 'unidad_medida' => 'kg', 'stock_actual' => 8, 'stock_minimo' => 10, 'estado' => 'activo']);

        // RI-032: qué proveedor surte cada ingrediente.
        $carnesYA->ingredientes()->attach([
            $panDelBarrio->id => ['disponible' => true, 'precio_referencia' => 400, 'fecha_actualizacion' => now()],
            $carneRes->id => ['disponible' => true, 'precio_referencia' => 18000, 'fecha_actualizacion' => now()],
            $quesoMozzarella->id => ['disponible' => true, 'precio_referencia' => 22000, 'fecha_actualizacion' => now()],
        ]);
        $distribuidoraPapa->ingredientes()->attach([
            $papaFresca->id => ['disponible' => true, 'precio_referencia' => 2500, 'fecha_actualizacion' => now()],
        ]);

        // Receta: qué ingredientes usa cada producto del menú (si existen).
        $hamburguesaSencilla = Product::where('nombre', 'Hamburguesa Sencilla')->first();
        $salchipapaClasica = Product::where('nombre', 'Salchipapa Clásica')->first();

        if ($hamburguesaSencilla) {
            $hamburguesaSencilla->ingredientes()->attach([
                $panDelBarrio->id => ['cantidad_requerida' => 1],
                $carneRes->id => ['cantidad_requerida' => 0.15],
            ]);
        }

        if ($salchipapaClasica) {
            $salchipapaClasica->ingredientes()->attach([
                $papaFresca->id => ['cantidad_requerida' => 0.3],
            ]);
        }

        // RF-031: una compra inicial de ejemplo (también deja historial para el módulo de compras).
        DB::table('compras_ingredientes')->insert([
            'id_ingrediente' => $carneRes->id,
            'id_proveedor' => $carnesYA->id,
            'cantidad' => 25,
            'precio_unitario' => 18000,
            'total' => 25 * 18000,
            'fecha' => now()->subDays(2),
        ]);
    }
}
