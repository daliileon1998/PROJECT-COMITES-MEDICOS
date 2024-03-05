<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { mostrarAlerta, mensaje } from '@/Pages/funciones.js';
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Select from 'datatables.net-select';
import Swal from 'sweetalert2';
import { Head, useForm } from '@inertiajs/vue3';

DataTable.use(DataTableLib);

const form = useForm({});
const props = defineProps({
    tipoUsuarios: { type: Array },
    permisos: { type: Object },
    compromisos: { type: Object }
});

const eliminar = (id, name) => {
    if (props.permisos.eliminar == 1) {
        const swalWithBoottstrapButtons = Swal.mixin({
            buttonsStyling: true
        });
        swalWithBoottstrapButtons.fire({
            title: 'Seguro que desea inactivar el tipo de Usuario ' + name,
            text: 'Se inactivará el tipo de Usuario',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inactivar',
            cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                form.delete(route('tipoUsuarios.destroy', id));
                mensaje('Tipo de Usuario Inactivado con exito', 'success', 'Éxito');
            }
        });
    } else {
        mostrarAlerta('Eliminar', 'Tipo Usuario');
    }
};

const editar = (id) => {
    if (props.permisos.editar == 1) {
        form.get(route('tipoUsuarios.edit', id));
    } else {
        mostrarAlerta('Editar', 'Tipo Usuario');
    }
};

function redirectToCreate() {
    if (props.permisos.agregar == 1) {
        form.get(route('tipoUsuarios.create'));
    } else {
        mostrarAlerta('Agregar', 'Tipo Usuario');
    }
}

</script>

<template>
    <Head title="tipoUsuarios"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <div class="center">
                <h1>Tipos de Usuario</h1>
            </div>
            <div class="container-fluid mt-3 bg-white border rounded p-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-success" @click="redirectToCreate">
                                <i class="fa-solid fa-circle-plus"></i> Añadir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div>
                            <dataTable id="tipoUsuariosTable" class="table table-striped table-bordered" :options="{
                                responsive: true, autoWidth: false,
                                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                            }">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tipoUsuario, i) in props.tipoUsuarios" :key="tipoUsuario.id">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ tipoUsuario.codigo }}</td>
                                        <td>{{ tipoUsuario.nombre }}</td>
                                        <td>{{ tipoUsuario.estado == 1 ? "Activo" : "Inactivo" }}</td>
                                        <td>
                                            <button class="btn btn-warning" @click="editar(tipoUsuario.id)"><i
                                                    class="fa-solid fa-edit"></i></button>
                                            <button class="btn btn-danger"
                                                @click="eliminar(tipoUsuario.id, tipoUsuario.nombre)">
                                                <i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </dataTable>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<style>
@import 'datatables.net-bs5';

.border {
    border: 1px solid #e0e0e0;
    /* Ajusta el color del borde según tu diseño */
}

.center {
    text-align: center;
}
</style>
