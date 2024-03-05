<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from 'datatables.net-vue3'
import DataTableLib from 'datatables.net-bs5';
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { mostrarAlerta, mensaje } from '@/Pages/funciones.js';
import modalVisualizar from '@/Pages/GestionComite/modalMostrarGestion.vue'
import modalGenerarActa from '@/Pages/GestionComite/modalCrearPdfGestion.vue'


DataTable.use(DataTableLib);
const showModal = ref(false);
const showModal2 = ref(false);
const idSeleccionado = ref({});
const form = useForm({});
const props = defineProps({
    gestionComites: { type: Object },
    permisos: { type: Object },
    compromisosN: { type: Object },
    usuarios : { type: Object}
});

const eliminar = (id, name) => {
    if (props.permisos.eliminar == 1) {
        const swalWithBoottstrapButtons = Swal.mixin({
            buttonsStyling: true
        });
        swalWithBoottstrapButtons.fire({
            title: 'Seguro que desea inactivar El comite ' + name,
            text: 'Se inactivará El comite',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check"></i> Si, Inactivar',
            cancelButtonText: '<i class="fa-solid fa-ban"></i> Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                form.delete(route('gestionComites.destroy', id));
                mensaje("Gestion de Comite Inactivado con exito", 'success', 'Éxito')
            }
        });
    } else {
        mostrarAlerta('Eliminar', 'Gestion Comites');
    }
};

const editar = (id) => {
    if (props.permisos.editar == 1) {
        form.get(route('gestionComites.edit', id));
    } else {
        mostrarAlerta('Editar', 'Gestion Comites');
    }
};

function redirectToCreate() {
    if (props.permisos.agregar == 1) {
        form.get(route('gestionComites.create'));
    } else {
        mostrarAlerta('Agregar', 'Gestion Comites');
    }
}

// Maneja el evento emitido desde el botón de "Ver"
const openModal = (id) => {
    idSeleccionado.value = id;
    if (idSeleccionado.value != null) {
        showModal.value = true;
    }
};

const openModal2 = (id) => {
    idSeleccionado.value = id;
    if (idSeleccionado.value != null) {
        showModal2.value = true;
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

/*function pdf() {
    axios.post('/generar-pdf', {
        idGestion: 1,
        nombreComite: "Comité Dos",
        fechaComite: "2013-01-06",
        acta: '3456',
        tema: 'Tema de prueba',
        fecha: '2023-11-23',
        ubicacion: 'Bucaramanga / Santander',
        hora_inicio: '8:00',
        hora_fin: '12:00',
        objetivo: 'estes es un objetivo \n que se puedan lograr\n nuestros sueños\n y metas',
        agenda: ' * 8:00 a.m Inicio de la reunión \n 9:00 a.m lluvia de ideas \n 9:30 socialización de temas \n 10:00 toma de decisiones',
        descripcion: 'estes es un objetivo \n que se puedan lograr\n nuestros sueños\n y metas',
        compromisos: [
            {
                "id": 1,
                "fecha_inicio": "2013-01-06",
                "fecha_fin": "2013-01-08",
                "gestion_comite_id": 1,
                "usuario_id": 4,
                "adjunto": "compromisos/compromiso_comite_2013-01-06_2013-01-06_prueba1700462330",
                "descripcion": "prueba",
                "estado": 3,
                "created_at": "2023-11-12T14:45:16.000000Z",
                "updated_at": "2023-11-20T06:38:50.000000Z",
                "usuario_name": "Prof. Gwen Johnston DVM",
                "indice": 1
            },
            {
                "id": 2,
                "fecha_inicio": "1993-03-31",
                "fecha_fin": "2022-04-09",
                "gestion_comite_id": 1,
                "usuario_id": 3,
                "adjunto": "",
                "descripcion": "Voluptatem.",
                "estado": 1,
                "created_at": "2023-11-12T14:45:16.000000Z",
                "updated_at": "2023-11-12T14:45:16.000000Z",
                "usuario_name": "Coralie Johnston",
                "indice": 2
            }
        ],
        participantes: [
            {
                "nombre": "Prof. Gwen Johnston DVM",
                "proceso": 'Administrativo'
            },
            {
                "nombre": "Coralie Johnston",
                "proceso": 'Legal'
            }
        ],
    })
        .then(response => {
            console.log(response.data);
            window.open(response.data.ruta, '_blank');
        })
        .catch(error => {
            console.error('Error al generar y modificar el PDF:', error);
        });
}*/

</script>

<template>
    <Head title="gestionComites"></Head>
    <AuthenticatedLayout :compromisos="props.compromisosN">
        <template #header>
            <div class="center">
                <h1>Gestion de Comites</h1>
            </div>
            <div class="container-fluid mt-3 bg-white border rounded p-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-success" @click="redirectToCreate">
                                <i class="fa-solid fa-circle-plus"></i> Añadir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div>
                            <dataTable id="gestionComitesTable" class="table table-striped table-bordered" :options="{
                                responsive: true, autoWidth: false,
                                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' }
                            }">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Comite</th>
                                        <th>Lider Comité</th>
                                        <th>Adjunto</th>
                                        <th>Acta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(gestionComite, i) in props.gestionComites" :key="gestionComite.id">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ gestionComite.fecha }}</td>
                                        <td>{{ gestionComite.nombre }}</td>
                                        <td>{{ gestionComite.name }}</td>
                                        <td>
                                            <a v-if="gestionComite.adjunto !== '' && esArchivoPDF(gestionComite.adjunto)" :href="gestionComite.adjunto_url" target="_blank">
                                                Adjunto
                                            </a>
                                            <a v-if="gestionComite.adjunto !== '' && esDocumentoWord(gestionComite.adjunto)" :href="gestionComite.adjunto_url" >Adjunto</a>
                                        </td>
                                        <td>
                                            <a v-if="gestionComite.acta !== '' && gestionComite.acta !== null"
                                                :href="gestionComite.acta_url" target="_blank">Acta</a>
                                        </td>

                                        <td>{{ gestionComite.estado == 1 ? "Activo" : "Inactivo" }}</td>
                                        <td>
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalVisualizar" @click="openModal(gestionComite)">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <button class="btn btn-warning" @click="editar(gestionComite.id)"><i
                                                    class="fa-solid fa-edit" title="Editar Gestion"></i></button>
                                            <button class="btn btn-danger"
                                                @click="eliminar(gestionComite.id, gestionComite.nombre)">
                                                <i class="fa-solid fa-trash" title="Desactivar Gestión"></i></button>
                                            <button class="btn btn-success" data-bs-toggle="modal"
                                                data-bs-target="#modalGenerarActa" @click="openModal2(gestionComite)">
                                                <i class="fa-solid fa-file-circle-plus" title="Generar Acta"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </dataTable>
                            <modalVisualizar :showModal="showModal" :gestionComite="idSeleccionado"></modalVisualizar>
                            <modalGenerarActa :showModal="showModal2" :gestionComite="idSeleccionado" :usuarios="props.usuarios"></modalGenerarActa>
                        </div>
                    </div>
                </div>
            </div>
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
