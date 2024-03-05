
<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';

DataTable.use(DataTableLib);

const props = defineProps({
    tipoUsuario: { type: Object, default: () => ({}) },
    modulos: { type: Object },
    compromisos : {type:Object}
});

const form = useForm({
    nombre: props.tipoUsuario.nombre, codigo: props.tipoUsuario.codigo, estado: props.tipoUsuario.estado,
    permisos: {}
});

props.modulos.forEach(modulo => {
    form.permisos[modulo.id] = {
        agregar: false,
        editar: false,
        eliminar: false,
        visualizar: false,
        idmodulo: modulo.id
    };
});

const save = () => {
    form.post(route('tipoUsuarios.store'), {
        onSuccess: (response) => {
            //console.log('Solicitud completada con éxito:', response);
            mensaje();
        }
    });
}


const mensaje = () => {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Tipo de usuario agregado exitosamente'
    });
}

const cancelar = () => {
    window.location.href = route('tipoUsuarios.index');
}

const updateCheckbox = (moduloId, tipo) => {
    form.permisos[moduloId][tipo] = !form.permisos[moduloId][tipo];
    console.log(form);
};

</script>

<template>
    <Head title="tipoUsuarios"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos">
        <template #header>
            <form @submit.prevent="save()">
                <div class="d-flex justify-content-center text-center">
                    <h1>Crear Tipo de Usuario</h1>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigotipoUsuario">Codigo</label>
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
                            <label for="nombretipoUsuario">Nombre</label>
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
                            <label for="estadotipoUsuario">Estado</label>
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
                <br>
                <h3>Permisos</h3><br>
                <dataTable id="tipoUsuariosTable" class="table table-striped table-bordered" :options="{
                    responsive: true, autoWidth: false,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                }">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Modulo</th>
                            <th>Agregar</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(modulo, i) in props.modulos" :key="modulo.id">
                            <td>{{ i + 1 }}</td>
                            <td>{{ modulo.nombre }}</td>
                            <td>
                                <Checkbox :name="'agregar_' + modulo.id" :checked="form.permisos[modulo.id].agregar"
                                    @change="updateCheckbox(modulo.id, 'agregar')" />
                            </td>
                            <td>
                                <Checkbox :name="'editar_' + modulo.id" :checked="form.permisos[modulo.id].editar"
                                    @change="updateCheckbox(modulo.id, 'editar')" />
                            </td>
                            <td>
                                <Checkbox :name="'eliminar_' + modulo.id" :checked="form.permisos[modulo.id].eliminar"
                                    @change="updateCheckbox(modulo.id, 'eliminar')" />
                            </td>
                            <td>
                                <Checkbox :name="'visualizar_' + modulo.id" :checked="form.permisos[modulo.id].visualizar"
                                    @change="updateCheckbox(modulo.id, 'visualizar')" />
                            </td>
                        </tr>
                    </tbody>
                </dataTable>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" @click="cancelar"><i class="fa-solid fa-xmark"></i>
                        Cancelar</button>
                </div>
            </form>
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
