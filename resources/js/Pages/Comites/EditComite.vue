<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
    comite: { type: Object, default: () => ({}) },
    usuarios: { Object },
    compromisos : {type:Object}
});

const form = useForm({
    id: props.comite.id, nombre: props.comite.nombre, codigo: props.comite.codigo, descripcion: props.comite.descripcion,
    estado: props.comite.estado, usuario_id: props.comite.usuario_id
});

const update = () => {
    var id = document.getElementById('id').value;
    form.put(route('comites.update', id), {
        onSuccess: (response) => {
            console.log(response);
            mensaje('Comité actualizado exitosamente','success', 'Éxito');
        }
    });
}

const mensaje = () => {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Comité agregado exitosamente'
    });
}

const cancelar = () => {
    window.location.href = route('comites.index');
}

</script>

<template>
    <Head title="Comites"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <form @submit.prevent="update()">
                <div class="d-flex justify-content-center text-center">
                    <h1>Crear Comite</h1>
                </div>
                <TextInput id="id" type="hidden" name="id" v-model="form.id"></TextInput>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigoComite">Codigo</label>
                            <TextInput id="codigo" class="form-control" name="codigo" type="text" v-model="form.codigo"
                                maxlength="10" placeholder="Ingrese el codigo del comité" autofocus required></TextInput>
                        </div>
                        <div v-if="form.errors.codigo" class="text-sm text-danger">
                            {{ form.errors.codigo }}
                        </div>
                        <br>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombreComite">Nombre</label>
                            <TextInput id="nombre" class="form-control" name="nombre" type="text" v-model="form.nombre"
                                maxlength="60" placeholder="Ingrese el nombre del comité"></TextInput>
                        </div>
                        <div v-if="form.errors.nombre" class="text-sm text-danger">
                            {{ form.errors.nombre }}
                        </div><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuarioId">Lider</label>
                            <SelectInput class="form-control" id="usuario_id" name="usuario_id" v-model="form.usuario_id"
                                :options="props.usuarios">
                            </SelectInput>
                        </div>
                        <div v-if="form.errors.usuario_id" class="text-sm text-danger">
                            {{ form.errors.usuario_id }}
                        </div><br>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estadoComite">Estado</label>
                            <select class="form-control" id="estado" name="estado" v-model="form.estado">
                                <option disabled value="">Please select one</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div v-if="form.errors.estado" class="text-sm text-danger">
                            {{ form.errors.estado }}
                        </div><br>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descripcionComite">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" v-model="form.descripcion"
                        rows="3"></textarea>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                    <button type="button" @click="cancelar" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i>
                        Cancelar</button>
                </div>
            </form>
        </template>
    </AuthenticatedLayout>
</template>

<style scoped></style>