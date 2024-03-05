
<script setup>
import $ from 'jquery';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ModalCompromiso from '@/Pages/Compromisos/modalCreateCompromiso.vue'
import ModalCompromisoDos from '@/Pages/Compromisos/modalEditCompromiso.vue'
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { ref, onMounted, defineProps } from 'vue';
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Swal from 'sweetalert2';
import axios from 'axios';
DataTable.use(DataTableLib);

const props = defineProps({
    gestionComite: { type: Object, default: () => ({}) },
    comites: { type: Object },
    usuarios: { type: Object },
    compromisosN: { type: Object },
});

const compromisosG = ref([]);
const compromisoE = ref([]);
const showModal = ref(false);

onMounted(() => {
    obtenerCompromisos(props.gestionComite.id);
});

const form = useForm({
    id: props.gestionComite.id, fecha_comite: props.gestionComite.fecha_comite, comite_id: props.gestionComite.comite_id, estado: 1,
    descripcion: props.gestionComite.descripcion, adjunto: props.gestionComite.adjunto,
    adjunto_url: props.gestionComite.adjunto_url
});

const handleCompromisoGuardado = (datos) => {
    //console.log('Compromiso guardado. Datos recibidos:', datos);
    obtenerCompromisos(props.gestionComite.id);
};

const obtenerCompromisos = (gestionComiteId) => {
    axios.get(route('compromisos.index', { gestion_comite_id: gestionComiteId }))
        .then(response => {
            if (response.data.compromisos.length > 0) {
                compromisosG.value = response.data.compromisos;

                // Destruir la instancia DataTables existente
                if ($.fn.DataTable.isDataTable('#compromisosTable')) {
                    $('#compromisosTable').DataTable().destroy();
                }

                // Inicializar DataTables con los nuevos datos
                $('#compromisosTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                    data: compromisosG.value,
                    columns: [
                        { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
                        { data: 'fecha_inicio' },
                        { data: 'fecha_fin' },
                        { data: 'descripcion' },
                        { data: 'usuario.name' },
                        {
                            data: null,
                            render: function (data, type, row) {
                                var url = row.adjunto;
                                var url2 = row.adjunto_url;
                                if (url !== '' && url !== null) {
                                    if(esArchivoPDF(url)){
                                        return '<a href="' + url2 + '" target="_blank">Adjunto</a>';
                                    }else{
                                        return '<a href="' + url2 + '">Adjunto</a>';
                                    }
                                   // return '<a href="' + url2 + '" target="_blank">Adjunto</a>';
                                } else {
                                    return 'Sin Adjunto';
                                }
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                var idCompromiso = row.id;
                                return `<button type="button" class="btn btn-warning editar" data-bs-toggle="modal" data-bs-target="#modalEditarCompromiso" data-id="${idCompromiso}"><i class="fa-solid fa-edit"></i></button> <button type="button" class="btn btn-danger eliminar" data-id="${idCompromiso}"><i class="fa-solid fa-trash"></i></button>`;
                            }
                        }
                    ]
                });

                $('#compromisosTable').on('click', '.editar', function () {
                    var idCompromiso = $(this).data('id');
                    openEditarModal(idCompromiso);
                });

                $('#compromisosTable').on('click', '.eliminar', function () {
                    var idCompromiso = $(this).data('id');
                    eliminarCompromiso(idCompromiso);
                });
            }
        })
        .catch(error => {
            console.error('Error al obtener los compromisos:', error);
        });
};

const save = () => {
    const formData = new FormData();
    formData.append('_method', 'put');
    formData.append('id', props.gestionComite.id);
    formData.append('fecha_comite', form.fecha_comite);
    formData.append('comite_id', form.comite_id);
    formData.append('estado', form.estado);
    formData.append('descripcion', form.descripcion);
    //formData.append('adjunto', document.getElementById('adjuntoGestion').files[0]);
    const fileInput = document.getElementById('adjunto');
    const fileName = fileInput.value;
    if (fileName != "") {

        const fileExtension = fileName.split('.').pop();
        if (['pdf', 'doc', 'docx'].includes(fileExtension.toLowerCase())) {
            const fileExtension = fileName.split('.').pop();
            formData.append('adjunto', document.getElementById('adjunto').files[0]);
            editarGestion(formData)
        } else {
            mensaje('El archivo debe ser de tipo: pdf, doc o docx.', 'error', 'Error');
        }
    } else if (form.adjunto != "") {
        formData.append('adjunto', form.adjunto);
        editarGestion(formData)
    } else {
        mensaje('Debe seleccionar un archivo', 'error', 'Error');
    }

}

function editarGestion(formData) {
    axios.post(route('gestionComites.update', form.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })
        .then(response => {
            if (response.data.error == 0) {
                mensaje(response.data.mensaje, 'success', 'Éxito');
            } else {
                mensaje(response.data.mensaje, 'error', 'Error');
            }
        })
}

function eliminarCompromiso(id) {
    // Lógica para eliminar el compromiso con el ID obtenido
    const swalWithBoottstrapButtons = Swal.mixin({
        buttonsStyling: true
    });

    swalWithBoottstrapButtons.fire({
        title: 'Seguro que desea inactivar El comite ' + name,
        text: 'Se eliminará el compromiso',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inactivar',
        cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(route('compromisos.destroy', { compromiso: id })).then(response => {
                if (response.data.error == 0) {
                    mensaje(response.data.mensaje, 'success', 'Éxito');
                    obtenerCompromisos(props.gestionComite.id)
                } else {
                    mensaje(response.data.mensaje, 'error', 'Error');
                }
            })
        }
    });
}

function openEditarModal(id) {
    const compromisoSeleccionado = compromisosG.value.find(compromiso => compromiso.id === id);
    compromisoE.value = compromisoSeleccionado;
    // Puedes agregar una comprobación adicional antes de abrir el modal
    if (compromisoE.value) {
        showModal.value = true;
    } else {
        console.error('Error: compromisoE no está definido correctamente.');
    }
}

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

const handleFileChange = (e) => {
    // Asegúrate de que hay archivos antes de intentar acceder a ellos
    if (e.target.files.length > 0) {
        const selectedFile = e.target.files[0];
        console.log('Archivo seleccionado:', selectedFile);
    }
};

const esDocumentoWord = (nombreArchivo) => {
    const extension = nombreArchivo.split('.').pop().toLowerCase();
    return extension === 'docx';
};

const esArchivoPDF = (nombreArchivo) => {
    console.log(nombreArchivo);
    const extension = nombreArchivo.split('.').pop().toLowerCase();
    return extension === 'pdf';
};

</script>

<template>
    <Head title="gestionComites"> Gestion Comites</Head>
    <AuthenticatedLayout :compromisos="props.compromisosN">
        <template #header>
            <form @submit.prevent="save()" enctype="multipart/form-data" method="POST">
               <div class="center"><h1>Editar Gestion Comité</h1></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigogestionComite">Fecha</label>
                            <TextInput id="fecha_comite" class="form-control" name="fecha_comite" type="date"
                                v-model="form.fecha_comite" maxlength="10" required></TextInput>
                            <div v-if="form.errors.fecha_comite" class="text-sm text-danger">
                                {{ form.errors.fecha_comite }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuarioId">Comite</label>
                            <SelectInput class="form-control" id="comite_id" name="comite_id" v-model="form.comite_id"
                                :options="props.comites"></SelectInput>
                            <div v-if="form.errors.comite_id" class="text-sm text-danger">
                                {{ form.errors.comite_id }}
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigogestionComite">Adjunto</label>
                            <TextInput id="adjunto" class="form-control" name="adjunto" type="file"
                                @change="handleFileChange" v-model="form.adjuntoGestion"></TextInput>
                            <div v-if="form.errors.adjunto" class="text-sm text-danger">
                                {{ form.errors.adjunto }}
                            </div>
                            <a v-if="form.adjunto !== null && form.adjunto !== '' " :href="form.adjunto_url" target="_blank">Adjunto</a>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombregestionComite">Descripcion</label>
                            <textarea id="descripcion" class="form-control" name="descripcion" type="text"
                                v-model="form.descripcion" maxlength="100"
                                placeholder="Ingrese la descripcion del comité"></textarea>
                            <div v-if="form.errors.descripcion" class="text-sm text-danger">
                                {{ form.errors.descripcion }}
                            </div>
                        </div>
                    </div>
                </div><br>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" @click="cancelar"><i class="fa-solid fa-xmark"></i>
                        Cancelar</button>
                </div>
            </form>
            <br>
            <h3>Compromisos</h3><br>
            <div class="col-lg-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="fa-solid fa-circle-plus"></i> Añadir
                </button>
            </div>
            <br>

            <dataTable id="compromisosTable" class="table table-striped table-bordered" :options="{
                responsive: true, autoWidth: false,
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
            }">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Descripcion</th>
                        <th>Responsable</th>
                        <th>Adjunto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </dataTable>
            <ModalCompromiso :idGestion="props.gestionComite.id" :usuarios="props.usuarios" :fechaComite="form.fecha_comite"
                @compromisoGuardado="handleCompromisoGuardado"></ModalCompromiso>

            <ModalCompromisoDos :showModal="showModal" :idGestion="props.gestionComite.id" :usuarios="props.usuarios"
                :fechaComite="form.fecha_comite" :compromisos="compromisoE" @compromisoGuardado="handleCompromisoGuardado">
            </ModalCompromisoDos>
        </template>

    </AuthenticatedLayout>
</template>

<style scoped>
@import 'datatables.net-bs5';

.border {
    border: 1px solid #e0e0e0;
    /* Ajusta el color del borde según tu diseño */
}

.center {
  text-align: center;
}
</style>