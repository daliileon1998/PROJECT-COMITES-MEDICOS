<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    gestionComite: { type: Object, default: () => ({}) },
});



const cancelar = () => {
    window.location.href = route('gestionComites.index');
}

const getEstadoTexto = (estado) => {
    switch (estado) {
        case 0:
            return 'Inactivo';
        case 1:
            return 'Activo';
        case 2:
            return 'En ejecución';
        case 3:
            return 'Finalizado';
        default:
            return 'Desconocido';
    }
};

</script>

<template>
    <div class="modal fade" id="modalVisualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gestión Comité</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fa-solid fa-calendar-days"></i> Fecha Comite : </h5>
                            <p>{{ props.gestionComite.fecha }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fa-solid fa-check-double"></i> Comité : </h5>
                            <p>{{ props.gestionComite.nombre }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fa-solid fa-user"></i> Lider Comité :</h5>
                            <p>{{ props.gestionComite.name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fa-solid fa-file"></i> Adjunto:</h5>
                            <a v-if="props.gestionComite.adjunto !== ''" :href="props.gestionComite.adjunto_url"
                                target="_blank"> Adjunto</a>
                        </div>
                    </div>
                    <br>
                    <h3>Compromisos</h3>
                    <br>
                    <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Descripción</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <!-- Agrega más columnas según sea necesario -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(compromiso, index) in props.gestionComite.compromisos" :key="index">
                        <td>{{ compromiso.fecha_inicio }}</td>
                        <td>{{ compromiso.fecha_fin }}</td>
                        <td>{{ compromiso.descripcion }}</td>
                        <td>{{ compromiso.usuario_name }}</td>
                        <td>{{ getEstadoTexto(compromiso.estado) }}</td>
                        <!-- Agrega más celdas según sea necesario -->
                    </tr>
                </tbody>
            </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cerrarModalV">Close</button>
                </div>

            </div>
        </div>
    </div>
</template>