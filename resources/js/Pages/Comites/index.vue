<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Swal from 'sweetalert2';
import { Head, useForm } from '@inertiajs/vue3';
import { mostrarAlerta,mensaje } from '@/Pages/funciones.js';
import { ref, onMounted, watch } from 'vue';

DataTable.use(DataTableLib);

const form = useForm({});
const props = defineProps({
    comites: { type: Object },
    compromisos: { type: Object },
    permisos: { type: Object }
});

const eliminar = (id, name) => {
    if (props.permisos.eliminar == 1) {
        const swalWithBoottstrapButtons = Swal.mixin({
            buttonsStyling: true
        })
        swalWithBoottstrapButtons.fire({
            title: 'Seguro que desea inactivar el comite ' + name,
            text: 'Se inactivara el comite',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inactivar',
            cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                form.delete(route('comites.destroy', id));
                mensaje("Comite Inactivado con exito", 'success', 'Éxito')
            }
        })
    } else {
        mostrarAlerta('Eliminar', 'Comites');
    }
}


const editar = (id) => {
    if (props.permisos.editar == 1) {
        form.get(route('comites.edit', id));
    } else {
        mostrarAlerta('Editar', 'Comites');
    }
};

function redirectToCreate() {
    if (props.permisos.agregar == 1) {
        form.get(route('comites.create'));
    } else {
        mostrarAlerta('Agregar', 'Comites');
    }
}

</script>

<template>
    <Head title="Comites"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <div class="center">
                <h1>Comités</h1>
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
                            <dataTable id="comitesTable" class="table table-striped table-bordered" :options="{
                                responsive: true, autoWidth: false,
                                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                            }">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Lider</th>
                                        <th>Descripcion</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(comite, i) in props.comites" :key="comite.id">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ comite.codigo }}</td>
                                        <td>{{ comite.nombre }}</td>
                                        <td>{{ comite.usuario.name }}</td>
                                        <td>{{ comite.descripcion }}</td>
                                        <td>{{ comite.estado == 1 ? "Activo" : "Inactivo" }}</td>
                                        <td>
                                            <button class="btn btn-warning" @click="editar(comite.id)"><i
                                                    class="fa-solid fa-edit"></i></button>
                                            <button class="btn btn-danger" @click="eliminar(comite.id, comite.nombre)"><i
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

.center {
    text-align: center;
}
</style>
