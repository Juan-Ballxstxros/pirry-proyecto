@extends('layouts.store')

@section('title', 'Mis pedidos - El Pirry')

@section('content')
<section class="seccion-pedidos">
    <div class="container" id="vue-mis-pedidos">
        <!-- MisPedidos.vue pinta el listado y la línea de tiempo aquí.
             Refresca con polling cada 15s para reflejar los cambios que
             hace el administrador sin recargar la página. -->
        <noscript>
            <p>Habilita JavaScript para ver el seguimiento de tus pedidos.</p>
        </noscript>
    </div>
</section>
@endsection
