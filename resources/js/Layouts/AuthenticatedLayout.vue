<script setup>
import { ref, computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { differenceInDays, parseISO } from 'date-fns';


const props = defineProps({
    compromisos: { type: Object, default: () => ({}) },
})

const showingNotifications = ref(false);
const showingNavigationDropdown = ref(false); // Añade esta línea

const toggleNotifications = () => {
    showingNotifications.value = !showingNotifications.value;
};

const daysDifference = (endDate) => {
    const currentDate = new Date();
    const parsedEndDate = parseISO(endDate);
    return differenceInDays(parsedEndDate, currentDate);
};

const pendingNotifications = computed(() => {
    const compromisosConDiferencia = [];

    props.compromisos.forEach((compromiso) => {
        const diasDiferencia = daysDifference(compromiso.fecha_fin);

        if (compromiso.estado !== 0 && compromiso.estado !== 3 && diasDiferencia <= 2) {
            compromiso.diasDiferencia = diasDiferencia;
            compromisosConDiferencia.push(compromiso);
        }
    });

    return compromisosConDiferencia;
});

</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                <img src="../../../public/img/logo-img.png"
                                    class="block h-12 w-auto fill-current text-gray-800" alt="">
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Inicio
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('tipoUsuarios.index')"
                                    :active="route().current('tipoUsuarios.index')">
                                    Tipo Usuario
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('usuarios.index')" :active="route().current('usuarios.index')">
                                    Usuarios
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('comites.index')" :active="route().current('comites.index')">
                                    Comites
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('gestionComites.index')"
                                    :active="route().current('gestionComites.index')">
                                    Gestion Comites
                                </NavLink>
                            </div>

                        </div>
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="relative">
                                <button type="button"
                                    class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                    @click="toggleNotifications">
                                    <i class="fa-solid fa-bell"></i>
                                    <span class="badge" v-if="pendingNotifications.length > 0">{{
                                        pendingNotifications.length }}</span>
                                </button>

                                <div v-if="showingNotifications"
                                    class="fixed top-10 right-4 z-10 bg-white border border-gray-200 max-w-xl max-h-52 min-h-52 overflow-y-auto p-4 mt-2">
                                    <ul class="space-y-2">
                                        <li v-if="pendingNotifications.length > 0" v-for="notification in pendingNotifications" :key="notification.id">
                                            <span v-if="notification.diasDiferencia >= 0" class="message">
                                                Faltan {{ notification.diasDiferencia + 1 }} días para que venza el
                                                compromiso
                                                <b>{{ notification.compromiso_descripcion }}</b> del comité <b>{{
                                                    notification.nombre_comite }}</b>
                                            </span>
                                            <span v-if="notification.diasDiferencia < 0" class="message">
                                                Tiene {{ (notification.diasDiferencia * -1) + 1 }} días de retraso en el
                                                compromiso
                                                <b>{{ notification.compromiso_descripcion }}</b> del comité <b>{{
                                                    notification.nombre_comite }}</b>
                                            </span>
                                            <hr class="my-1 border-t border-gray-300">
                                        </li>
                                        <li v-else>
                                            <span class="message">No tienes compromisos pendientes</span>
                                            <hr class="my-1 border-t border-gray-300">
                                        </li>
                                    </ul>
                                </div>
                                
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Perfil </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Inicio
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('tipoUsuarios.index')"
                            :active="route().current('tipoUsuarios.index')">
                            Tipo Usuario
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('usuarios.index')" :active="route().current('usuarios.index')">
                            Usuarios
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('comites.index')" :active="route().current('comites.index')">
                            Comites
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('gestionComites.index')"
                            :active="route().current('gestionComites.index')">
                            Gestion Comites
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Perfil </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Cerrar Sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
.badge {
    background-color: red;
    /* Color del fondo del badge */
    color: white;
    /* Color del texto del badge */
    font-size: 12px;
    /* Tamaño de fuente del badge */
    padding: 4px 8px;
    /* Espaciado interno del badge */
    border-radius: 50%;
    /* Radio de borde para hacerlo redondeado */
    position: absolute;
    /* Posición absoluta para superponerlo encima del ícono */
    top: 0;
    right: 0;
}
</style>