<script setup>
import $ from 'jquery';
import { defineProps, ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import VueMultiselect from 'vue-multiselect'
import axios from 'axios';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { component as CKEditor } from '@ckeditor/ckeditor5-vue';

const props = defineProps({
    gestionComite: { type: Object, default: () => ({}) },
    usuarios: { type: Object, default: () => ({}) },
});

const selected = ref(null);
const selected2 = ref(null);

console.log("usuarios ----->",props);

const form = useForm({
    num_acta: '', tema: '', fecha: '', hora_inicio: '', hora_fin: '', ubicacion: '', objetivo: '', agenda: '',
    compromisos: props.gestionComite.compromisos, idGestion: props.gestionComite.id, descripcion: '', lider: '', secretario: ''
});

const cancelar = () => {
    window.location.href = route('gestionComites.index');
}

const openModal = (modalId) => {
    const modal = new bootstrap.Modal(document.getElementById(modalId), {
        backdrop: 'static',
        keyboard: false,
    });

    modal.show();
}

const editAndSaveDocument = async () => {
    axios.post('/generar-pdf', {
        idGestion: props.gestionComite.id,
        nombreComite: props.gestionComite.nombre,
        fechaComite: props.gestionComite.fecha,
        acta: form.num_acta,
        tema: form.tema,
        fecha: form.fecha,
        ubicacion: form.ubicacion,
        hora_inicio: form.hora_inicio,
        hora_fin: form.hora_fin,
        objetivo: form.objetivo,
        agenda: form.agenda,
        descripcion: form.descripcion,
        compromisos: props.gestionComite.compromisos,
        participantes: selected.value,
        firmas: selected2.value,
        lider: form.lider,
        secretario: form.secretario,
    })
        .then(response => {
            window.open(response.data.ruta, '_blank');
        })
        .catch(error => {
            console.error('Error al generar y modificar el PDF:', error);
        });
}

</script>

<template>
    <div class="modal fade" id="modalGenerarActa" tabindex="-1" role="dialog" aria-labelledby="exampleModal"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gestión Comité</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="editAndSaveDocument()">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Fecha</label>
                                <TextInput id="fecha" class="form-control" name="fecha" type="date" v-model="form.fecha"
                                    placeholder="Ingrese la fecha" required></TextInput>
                            </div>
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Hora Inicio</label>
                                <TextInput id="hora_inicio" class="form-control" name="hora_inicio" type="time"
                                    v-model="form.hora_inicio" placeholder="Ingrese la hora de inicio" required></TextInput>
                            </div>
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Hora Fin</label>
                                <TextInput id="hora_fin" class="form-control" name="hora_fin" type="time"
                                    v-model="form.hora_fin" maxlength="200" placeholder="Ingrese la hora fin" required>
                                </TextInput>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Acta</label>
                                <TextInput id="acta" class="form-control" name="acta" type="text" v-model="form.num_acta"
                                    maxlength="10" placeholder="Ingrese el número de acta" autofocus required></TextInput>
                            </div>
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Tema</label>
                                <TextInput id="tema" class="form-control" name="tema" type="text" v-model="form.tema"
                                    maxlength="60" placeholder="Ingrese el tema" required></TextInput>
                            </div>
                            <div class="col-md-4">
                                <label for="codigotipoUsuario">Ubicacion</label>
                                <TextInput id="ubicacion" class="form-control" name="ubicacion" type="text"
                                    v-model="form.ubicacion" maxlength="200" placeholder="Ingrese la Ubicación" required>
                                </TextInput>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Objetivo</label>
                                <textarea id="objetivo" class="form-control" name="objetivo" v-model="form.objetivo"
                                    rows="2" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Agenda</label>
                                <textarea id="agenda" class="form-control" name="agenda" v-model="form.agenda" rows="2"
                                    maxlength="200" required></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Lider Reunión</label>
                                <TextInput id="lider" class="form-control" name="lider" type="text" v-model="form.lider"
                                    maxlength="200" placeholder="Ingrese El nombre del Lider de la Reunión" required>
                                </TextInput>
                            </div>
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Secretario</label>
                                <TextInput id="secretario" class="form-control" name="secretario" type="text"
                                    v-model="form.secretario" maxlength="200" placeholder="Ingrese El nombre del Secretario"
                                    required>
                                </TextInput>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Participantes</label>
                                <div>
                                    <VueMultiselect v-model="selected" :options="props.usuarios" :multiple="true"
                                        :close-on-select="true" placeholder="Selecciona los participantes"
                                        select-placeholder="Buscar" label="name" track-by="name" />
                                </div>

                            </div>
                            <div class="col-md-6">
                                <label for="codigotipoUsuario">Firmas</label>
                                <VueMultiselect v-model="selected2" :options="props.usuarios" :multiple="true"
                                    :close-on-select="true" placeholder="Selecciona los participantes"
                                    select-placeholder="Buscar" label="name" track-by="name" />
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <label for="codigotipoUsuario">Descripción</label>
                            <div class="col-md-12" style="height: 50vh; overflow-y: auto;">
                                <CKEditor :editor="ClassicEditor" id="miEditor" v-model="form.descripcion" :config="editorConfig">
                                </CKEditor>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="bg-blue-600 text-white p-2 rounded" :disabled="form.processing">
                            <i class="fa-solid fa-floppy-disk"></i> Generar Acta
                        </button>
                    </form>
                </div>
                <div class="modal-footer">

                    <button type="button" class="bg-red-600 text-white p-2 rounded" data-bs-dismiss="modal"
                        id="cerrarModalC" @click="cancelar">Close</button>
                </div>

            </div>
        </div>
    </div>
</template>




<style src="vue-multiselect/dist/vue-multiselect.css"></style>