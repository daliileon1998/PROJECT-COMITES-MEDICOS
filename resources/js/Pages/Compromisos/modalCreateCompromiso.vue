<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Swal from 'sweetalert2';
import axios from 'axios';
import { ref, onMounted, defineProps, defineEmits, getCurrentInstance } from 'vue';

const instancia = getCurrentInstance();
const emits = defineEmits(['compromisoGuardado']);

const props = defineProps({
    compromisos: { type: Object, default: () => ({}) },
    idGestion: String,
    fechaComite: Date,
    usuarios: { type: Object }
});

onMounted(() => {
    const modalElement = document.getElementById('modalAgregar');
    modalElement.addEventListener('hidden.bs.modal', () => {
        form.reset(); // Esto debería resetear el formulario
    });
});


const form = useForm({
    fecha_inicio: props.compromisos.fecha_inicio, fecha_fin: props.compromisos.fecha_fin,
    usuario_id: props.compromisos.usuario_id, estado: props.compromisos.estado, idGestion: props.idGestion,
})



const save = () => {
    form.idGestion = props.idGestion;
    if (form.idGestion > 0) {
        /*if (form.fecha_inicio < props.fechaComite || form.fecha_fin < props.fechaComite) {
            mensaje('La fecha de inicio y de fin deben ser mayores o iguales a la fecha del comite', 'error', 'Error')
        } else if (form.fecha_fin < form.fecha_inicio) {
            mensaje('La fecha fin debe ser mayor a la fecha inicio del compromiso', 'error', 'Error')
        } else {*/
            const fileInput = document.getElementById('adjuntocompromiso');
            const fileName = fileInput.value;

            const formData = new FormData();
            formData.append('fecha_inicio', form.fecha_inicio);
            formData.append('fecha_fin', form.fecha_fin);
            formData.append('estado', form.estado);
            formData.append('descripcion', form.descripcion);
            formData.append('gestion_comite_id', form.idGestion);
            formData.append('usuario_id', form.usuario_id);


            if (fileName != "") {
                const fileExtension = fileName.split('.').pop();
                formData.append('adjunto', document.getElementById('adjuntocompromiso').files[0]);
            } else {
                formData.append('adjunto', '');
            }
            axios.post(route('compromisos.store'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then(response => {

                if (response.data.error == 0) {
                    document.querySelector("#cerrarModal").click();
                    mensaje(response.data.mensaje, 'success', 'Éxito')
                    form.errors = {};
                    emits('compromisoGuardado', response.data.compromisoId);
                } else {
                    mensaje(response.data.mensaje, 'error', 'Error');
                }
            }).catch(error => {
                if (error.response && error.response.status === 422) {
                    console.log('Errores de validación:', error.response.data.errors);
                    form.errors = error.response.data.errors;
                } else {
                    console.error('Error en la solicitud:', error);
                }
            });
        //}
    } else {
        mensaje("primero debe crear la gestion de comite", 'error', 'Error')
    }
};

const mensaje = (text, icon, title) => {
    Swal.fire({
        icon: icon,
        title: title,
        text: text
    });
}

const cancelar = () => {
    window.location.href = route('gestionComites.index');

}

</script>

<template>
    <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Compromiso</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <form @submit.prevent="save()" enctype="multipart/form-data" method="POST">
                        <TextInput id="id" type="hidden" name="id" v-model="props.idGestion"></TextInput>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigogestionComite">Fecha Inicio</label>
                                    <TextInput id="fecha_inicio" class="form-control" name="fecha_inicio" type="date"
                                        v-model="form.fecha_inicio" maxlength="10" required></TextInput>
                                    <div v-if="form.errors.fecha_inicio" class="text-sm text-danger">
                                        {{ form.errors.fecha_inicio }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usuarioId">Fecha Fin</label>
                                    <TextInput id="fecha_fin" class="form-control" name="fecha_fin" type="date"
                                        v-model="form.fecha_fin" maxlength="10" required></TextInput>
                                    <div v-if="form.errors.fecha_fin" class="text-sm text-danger">
                                        {{ form.errors.fecha_fin }}
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigogestionComite">Responsable</label>
                                    <SelectInput class="form-control" id="comite_id" name="comite_id"
                                        v-model="form.usuario_id" :options="props.usuarios"></SelectInput>
                                    <div v-if="form.errors.usuario_id" class="text-sm text-danger">
                                        {{ form.errors.usuario_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigogestionComite">Adjunto</label>
                                    <TextInput id="adjuntocompromiso" class="form-control" name="adjuntocompromiso"
                                        type="file" v-model="form.adjunto"></TextInput>
                                    <div v-if="form.errors.adjunto" class="text-sm text-danger">
                                        {{ form.errors.adjunto }}
                                    </div>

                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigogestionComite">Descripcion</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion"
                                        v-model="form.descripcion" rows="3"></textarea>
                                    <div v-if="form.errors.descripcion" class="text-sm text-danger">
                                        {{ form.errors.descripcion }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigogestionComite">Estado</label>
                                    <select class="form-control" id="estado" name="estado" v-model="form.estado">
                                        <option disabled value="">Please select one</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                        <option value="2">En Ejecucción</option>
                                        <option value="3">Finalizado</option>
                                    </select>
                                    <div v-if="form.errors.estado" class="text-sm text-danger">
                                        {{ form.errors.estado }}
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div class="d-flex justify-content-center text-center">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <i class="fa-solid fa-floppy-disk"></i> Guardar
                            </button>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cerrarModal">Close</button>
                </div>

            </div>
        </div>
    </div>
</template>