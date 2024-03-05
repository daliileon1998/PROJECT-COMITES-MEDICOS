<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    permisos: { type: Object },
    compromisos : { type: Object },
});

console.log();

</script>

<template>
    <Head title="tipoUsuarios"></Head>
    <AuthenticatedLayout :compromisos="props.compromisos" >
        <template #header>
            <br>
            <center>
                <i class="fa-sharp fa-solid fa-lock" style="font-size: 60px;"></i>
                <br><br>
                <h1>Accesso denegado</h1>
                <br>
                <p> No tiene permisos sobre el módulo <b>{{ permisos.modulo_nombre }}</b>. </p>
                <p>Grupo Usuarios <b>{{ permisos.tipo_usuario_nombre }}</b>, módulo <b>{{ permisos.modulo_codigo }}</b></p>
                <p>Contacte a su administrador de sistema.</p>
            </center>
        </template>
    </AuthenticatedLayout>
</template>