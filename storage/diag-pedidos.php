<?php
/**
 * Diagnóstico de la corrupción de la tabla pedidos (ERROR 1932).
 * SOLO LECTURA: no modifica nada.
 */
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

function linea($etiqueta, $valor = '') {
    echo $etiqueta.': '.$valor."\n";
}

linea('== SERVIDOR ==');
linea('version', DB::selectOne('SELECT VERSION() as v')->v);
linea('datadir', DB::selectOne('SELECT @@datadir as d')->d);
linea('innodb_file_per_table', DB::selectOne('SELECT @@innodb_file_per_table as v')->v);
linea('innodb_data_file_path', DB::selectOne('SELECT @@innodb_data_file_path as v')->v);

linea('== TABLAS DE usuarios ==');
$tablas = DB::select('SHOW TABLES');
foreach ($tablas as $t) {
    $nombre = array_values((array) $t)[0];
    try {
        DB::select("SELECT 1 FROM `$nombre` LIMIT 1");
        linea("tabla $nombre", 'ACCESIBLE');
    } catch (Throwable $e) {
        linea("tabla $nombre", 'ERROR: '.substr($e->getMessage(), 0, 120));
    }
}

linea('== ESTADO DE pedidos ==');
try {
    $filas = DB::select('SELECT * FROM pedidos');
    linea('SELECT * FROM pedidos', 'OK, filas='.count($filas));
    foreach ($filas as $f) { echo '   '.json_encode($f)."\n"; }
} catch (Throwable $e) {
    linea('SELECT * FROM pedidos', 'ERROR: '.$e->getMessage());
    try {
        foreach (DB::select('SHOW WARNINGS') as $w) {
            echo '   warning: '.$w->Level.' '.$w->Code.' '.$w->Message."\n";
        }
    } catch (Throwable $e2) {}
}

linea('== detalle_pedido (referencias) ==');
try {
    $refs = DB::select('SELECT id_pedido, COUNT(*) as n FROM detalle_pedido GROUP BY id_pedido');
    foreach ($refs as $r) { echo "   pedido {$r->id_pedido}: {$r->n} lineas\n"; }
} catch (Throwable $e) {
    linea('detalle_pedido', 'ERROR: '.$e->getMessage());
}

linea('== historial_pedidos ==');
try {
    linea('filas', DB::selectOne('SELECT COUNT(*) as n FROM historial_pedidos')->n);
} catch (Throwable $e) {
    linea('historial_pedidos', 'ERROR: '.$e->getMessage());
}

linea('== FKs que apuntan a pedidos ==');
try {
    $fks = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME, DELETE_RULE FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE REFERENCED_TABLE_NAME = 'pedidos' AND CONSTRAINT_SCHEMA = 'usuarios'");
    foreach ($fks as $f) { echo "   {$f->TABLE_NAME} ({$f->CONSTRAINT_NAME}) ON DELETE {$f->DELETE_RULE}\n"; }
} catch (Throwable $e) {
    linea('FKs', 'ERROR: '.$e->getMessage());
}

linea('== AUTO_INCREMENT actual de pedidos ==');
try {
    $ai = DB::selectOne("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='usuarios' AND TABLE_NAME='pedidos'");
    linea('AUTO_INCREMENT', $ai ? $ai->AUTO_INCREMENT : '(no disponible)');
} catch (Throwable $e) {}
