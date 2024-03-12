<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import SelectInput from '@/Components/SelectInput.vue';
import axios from 'axios';

const props = defineProps({
    usuario: { type: Object, default: () => ({}) },
    tipoUsuario: { Object },
    compromisos: { type: Object }
});

const form = useForm({
    id: props.usuario.id, name: props.usuario.name, cc: props.usuario.cc, estado: props.usuario.estado,
    email: props.usuario.email, phone: props.usuario.phone, tipo_usuario_id: props.usuario.tipo_usuario_id,
    password: props.usuario.password, password2: props.usuario.password2, cargo: props.cargo, firma: props.usuario.firma, firma_url: props.usuario.firma_url
});

const save = () => {

    const fileInput = document.getElementById('firmaCreate');
    const fileName = fileInput.value;

    const formData = new FormData();
    formData.append('cc', form.cc);
    formData.append('name', form.name);
    formData.append('estado', form.estado);
    formData.append('phone', form.phone);
    formData.append('email', form.email);
    formData.append('cargo', form.cargo);
    formData.append('password2', form.password2);
    formData.append('tipo_usuario_id', form.tipo_usuario_id);

    if (fileName != "") {
        const fileExtension = fileName.split('.').pop();
        formData.append('firma', document.getElementById('firmaCreate').files[0]);
        console.log(formData,fileName);
        //document.getElementById('adjunto').files[0]
    } else {
        formData.append('firma', '');
    }

    axios.post(route('usuarios.store',{ usuario: formData }), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    }).then(response => {
        console.log('Solicitud completada con éxito:', response);
        console.log(response.data);
        // Limpiar errores antes de la nueva solicitud
        form.errors = {};
        mensaje();

    }).catch(error => {
        // Manejar errores de validación aquí
        if (error.response && error.response.status === 422) {
            console.log('Errores de validación:', error.response.data.errors);
            form.errors = error.response.data.errors;
        } else {
            console.error('Error en la solicitud:', error);
        }
    });
    /*form.post(route('usuarios.store'), {
        onSuccess: (response) => {
            mensaje();
        }
    });*/
}

const mensaje = () => {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Usuario Agregado exitosamente'
    });
}

const cancelar = () => {
    window.location.href = route('usuarios.index');
}

</script>

<template>

    <Head title="usuarios"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <form @submit.prevent="save()">
                <div class="d-flex justify-content-center text-center">
                    <h1>Crear Usuario</h1>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cedulausuario">Cedula</label>
                            <TextInput id="cedula" class="form-control" name="cedula" type="number" v-model="form.cc"
                                maxlength="10" placeholder="Ingrese el cedula del Usuario" autofocus required>
                            </TextInput>
                        </div>
                        <div v-if="form.errors.cc" class="text-sm text-danger">
                            {{ form.errors.cc }}
                        </div>
                        <br>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nameusuario">Nombre</label>
                            <TextInput id="name" class="form-control" name="name" type="text" v-model="form.name"
                                maxlength="60" placeholder="Ingrese el name del Usuario"></TextInput>
                        </div>
                        <div v-if="form.errors.name" class="text-sm text-danger">
                            {{ form.errors.name }}
                        </div><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nameusuario">Telefono</label>
                            <TextInput id="phone" class="form-control" name="phone" type="number" v-model="form.phone"
                                maxlength="10" placeholder="Ingrese el phone del Usuario"></TextInput>
                        </div>
                        <div v-if="form.errors.phone" class="text-sm text-danger">
                            {{ form.errors.phone }}
                        </div><br>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nameusuario">Correo Electronico</label>
                            <TextInput id="email" class="form-control" name="email" type="email" v-model="form.email"
                                maxlength="60" placeholder="Ingrese el Correo Electronico del Usuario"></TextInput>
                        </div>
                        <div v-if="form.errors.email" class="text-sm text-danger">
                            {{ form.errors.email }}
                        </div><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuarioId">Grupo Usuario</label>
                            <SelectInput class="form-control" id="tipo_usuario_id" name="tipo_usuario_id"
                                v-model="form.tipo_usuario_id" :options="props.tipoUsuario">
                            </SelectInput>
                        </div>
                        <div v-if="form.errors.tipo_usuario_id" class="text-sm text-danger">
                            {{ form.errors.tipo_usuario_id }}
                        </div><br>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuarioId">Contraseña</label>
                            <TextInput id="password2" type="password" class="form-control" v-model="form.password2"
                                required autocomplete="new-password" />
                        </div>
                        <div v-if="form.errors.password2" class="text-sm text-danger">
                            {{ form.errors.password2 }}
                        </div><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estadousuario">Cargo</label>
                            <TextInput id="cargo" class="form-control" name="cargo" type="text" v-model="form.cargo"
                                maxlength="60" placeholder="Ingrese el Cargo del Usuario"></TextInput>
                        </div>
                        <div v-if="form.errors.cargo" class="text-sm text-danger">
                            {{ form.errors.cargo }}
                        </div><br>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estadousuario">Estado</label>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigogestionComite">Firma</label>
                            <TextInput id="firmaCreate" class="form-control" name="firmaCreate" type="file"
                                v-model="form.firma" accept="image/*"></TextInput>
                            <div v-if="form.errors.firma" class="text-sm text-danger">
                                {{ form.errors.firma }}
                            </div>
                        </div>
                    </div>
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