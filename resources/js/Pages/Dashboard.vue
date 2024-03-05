<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Select from 'datatables.net-select';
import Swal from 'sweetalert2';
import { ref, getCurrentInstance } from 'vue';
import { differenceInDays, parseISO } from 'date-fns';
import { Head, useForm } from '@inertiajs/vue3';

DataTable.use(DataTableLib);

const props = defineProps({
    compromisos: { type: Object }
});

const getBackgroundColor = (endDate) => {
    const currentDate = new Date();
    const parsedEndDate = parseISO(endDate);

    if (differenceInDays(parsedEndDate, currentDate) <= 2) {
        return '#f56565'; // Rojo
    } else if (differenceInDays(parsedEndDate, currentDate) >= 3 && differenceInDays(parsedEndDate, currentDate) <= 8) {
        return '#f6e05e'; // Amarillo
    } else if (differenceInDays(parsedEndDate, currentDate) >= 9) {
        return '#38a169'; // Verde
    }
    return ''; // Por defecto, no aplicar ningún color
};

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout :compromisos="props.compromisos" >
        <template #header>
            <div class="d-flex justify-content-center text-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bienvenido {{ $page.props.auth.user.name }}
                </h2>
            </div>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <dataTable id="compromisosTable" class="table table-striped table-bordered" :options="{
                            responsive: true, autoWidth: false,
                            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                        }">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Comite</th>
                                    <th>Compromiso</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(compromiso, i) in props.compromisos" :key="compromiso.id">
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ i + 1 }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ compromiso.nombre_comite }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ compromiso.compromiso_descripcion }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ compromiso.fecha_inicio }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ compromiso.fecha_fin }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }">{{ compromiso.nombre_usuario }}</td>
                                    <td :style="{ 'background-color': getBackgroundColor(compromiso.fecha_fin) }"> 
                                        <span v-if="compromiso.estado === 1">Activo</span>
                                        <span v-else-if="compromiso.estado === 2">En ejecución</span>
                                        <span v-else>Inactivo</span>
                                    </td>
                                </tr>

                            </tbody>
                        </dataTable>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>


<style>
@import 'datatables.net-bs5';
</style>
