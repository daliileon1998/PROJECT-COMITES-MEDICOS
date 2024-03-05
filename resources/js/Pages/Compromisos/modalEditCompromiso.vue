<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Swal from 'sweetalert2';
import axios from 'axios';
import { ref, onMounted, defineProps, defineEmits, getCurrentInstance, watch } from 'vue';

const instancia = getCurrentInstance();
const emits = defineEmits(['compromisoGuardado']);

const props = defineProps({
    compromisos: { type: Object, default: () => ({}) },
    idGestion: String,
    fechaComite: Date,
    usuarios: { type: Object }
});

onMounted(() => {
    const modalElement = document.getElementById('modalEditarCompromiso');
    modalElement.addEventListener('hidden.bs.modal', () => {
        form.reset(); // Esto debería resetear el formulario
    });
});

const form = useForm({
    id: props.compromisos.id, fecha_inicio: props.compromisos.fecha_inicio, fecha_fin: props.compromisos.fecha_fin,
    usuario_id: props.compromisos.usuario_id, estado: props.compromisos.estado, idGestion: props.idGestion,
    adjuntocompromiso: props.compromisos.adjunto, adjunto_url: props.compromisos.adjunto_url
})


watch(() => {
    form.id = props.compromisos.id;
    form.fecha_inicio = props.compromisos.fecha_inicio;
    form.fecha_fin = props.compromisos.fecha_fin;
    form.usuario_id = props.compromisos.usuario_id;
    form.descripcion = props.compromisos.descripcion;
    form.idGestion = props.compromisos.gestion_comite_id;
    form.estado = props.compromisos.estado;
    form.adjuntocompromiso = props.compromisos.adjunto;
    form.adjunto_url = props.compromisos.adjunto_url;

}, { immediate: true });

const save = () => {
    form.idGestion = props.idGestion;

    if (form.idGestion > 0) {
       /* if (form.fecha_inicio < props.fechaComite || form.fecha_fin < props.fechaComite) {
            mensaje('La fecha de inicio y de fin deben ser mayores o iguales a la fecha del comite', 'error', 'Error')
        } else if (form.fecha_fin < form.fecha_inicio) {
            mensaje('La fecha fin debe ser mayor a la fecha inicio del compromiso', 'error', 'Error')
        } else {*/
            const fileInput = document.getElementById('adjuntocompromiso2');
            const fileName = fileInput.value;

            const formData = new FormData();
            formData.append('_method', 'put');
            formData.append('fecha_inicio', form.fecha_inicio);
            formData.append('fecha_fin', form.fecha_fin);
            formData.append('estado', form.estado);
            formData.append('descripcion', form.descripcion);
            formData.append('gestion_comite_id', form.idGestion);
            formData.append('usuario_id', form.usuario_id);

            if (fileName != "") {
                const fileExtension = fileName.split('.').pop();
                formData.append('adjunto', document.getElementById('adjuntocompromiso2').files[0]);
                //document.getElementById('adjunto').files[0]
            } else {
                formData.append('adjunto', '');
            }
            axios.post(route('compromisos.update', form.id, { compromiso: formData }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then(response => {
                console.log('Solicitud completada con éxito:', response);
                console.log(response.data);
                document.querySelector("#cerrarModal2").click();
                // Limpiar errores antes de la nueva solicitud
                form.errors = {};
                emits('compromisoGuardado', response.data.compromisoId);

            }).catch(error => {
                // Manejar errores de validación aquí
                if (error.response && error.response.status === 422) {
                    console.log('Errores de validación:', error.response.data.errors);
                    form.errors = error.response.data.errors;
                } else {
                    console.error('Error en la solicitud:', error);
                }
            });
       // }
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
    <div class="modal fade" id="modalEditarCompromiso" tabindex="-1" role="dialog" aria-labelledby="exampleModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Compromiso</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <form @submit.prevent="save()" enctype="multipart/form-data" method="PUT">
                        <TextInput id="idGestion" type="hidden" name="id" v-model="props.idGestion"></TextInput>
                        <TextInput id="id" type="hidden" name="id" v-model="form.id"></TextInput>
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
                                    <TextInput id="adjuntocompromiso2" class="form-control" name="adjuntocompromiso2"
                                        type="file" v-model="form.adjunto" @change="handleFileChange"></TextInput>
                                    <div v-if="form.errors.adjunto" class="text-sm text-danger">
                                        {{ form.errors.adjunto }}
                                    </div>
                                    <a v-if="form.adjuntocompromiso !== null && form.adjuntocompromiso !== ''"
                                        :href="form.adjunto_url" target="_blank">Adjunto</a>
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
                        <div class="d-flex justify-content-center text-center"><button type="submit" class="btn btn-primary"
                                :disabled="form.processing">
                                <i class="fa-solid fa-floppy-disk"></i> Guardar
                            </button>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cerrarModal2">Close</button>
                </div>

            </div>
        </div>
    </div>
</template>