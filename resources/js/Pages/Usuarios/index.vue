<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Select from 'datatables.net-select';
import Swal from 'sweetalert2';
import { Head, useForm } from '@inertiajs/vue3';
import { mostrarAlerta,mensaje } from '@/Pages/funciones.js';

DataTable.use(DataTableLib);

const form = useForm({});
const props = defineProps({
    Usuarios: { type: Object },
    permisos: { type: Object },
    compromisos : {type:Object}
});

const eliminar = (id, name) => {
    if (props.permisos.eliminar == 1) {
        const swalWithBoottstrapButtons = Swal.mixin({
            buttonsStyling: true
        })
        swalWithBoottstrapButtons.fire({
            title: 'Seguro que desea inactivar al Usuario ' + name,
            text: 'Se inactivara el Usuario',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inactivar',
            cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                form.delete(route('usuarios.destroy', id));
                mensaje("Usuario Inactivado con exito", 'success', 'Éxito')
            }
        })
    } else {
        mostrarAlerta('Eliminar', 'Usuario');
    }
}

const editar = (id) => {
    if (props.permisos.editar == 1) {
        form.get(route('usuarios.edit', id));
    } else {
        mostrarAlerta('Editar', 'Usuario');
    }
};

function redirectToCreate() {
    // Redirige a la ruta de creación
    if (props.permisos.agregar == 1) {
        form.get(route('usuarios.create'));
    } else {
        mostrarAlerta('Editar', 'Usuario');
    }
}

</script>

<template>
    <Head title="Usuarios"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <div class="d-flex justify-content-center text-center">
                <h1>Usuarios</h1>
            </div>
            <div class="container-fluid mt-3 bg-white border rounded p-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-success" @click="redirectToCreate"><i
                                    class="fa-solid fa-circle-plus"></i> Añadir</button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div>
                            <dataTable id="UsuariosTable" class="table table-striped table-bordered" :options="{
                                responsive: true, autoWidth: false,
                                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                            }">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Grupo Usuario</th>
                                        <th>Telefono</th>
                                        <th>Correo</th>
                                        <th>Cargo</th>
                                        <th>Firma</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(Usuario, i) in props.Usuarios" :key="Usuario.id">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ Usuario.cc }}</td>
                                        <td>{{ Usuario.name }}</td>
                                        <td>{{ Usuario.tipo_usuario.nombre }}</td>
                                        <td>{{ Usuario.phone }}</td>
                                        <td>{{ Usuario.email }}</td>
                                        <td>{{ Usuario.cargo }}</td>
                                        <td><a v-if="Usuario.firma !== '' && Usuario.firma !== null"
                                                :href="Usuario.firma_url" target="_blank">Firma</a></td>
                                        <td>{{ Usuario.estado == 1 ? "Activo" : "Inactivo" }}</td>
                                        <td>
                                            <button class="btn btn-warning" @click="editar(Usuario.id)"><i
                                                    class="fa-solid fa-edit"></i></button>
                                            <button class="btn btn-danger" @click="eliminar(Usuario.id, Usuario.name)"><i
                                                    class="fa-solid fa-trash"></i></button>
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
</style>
