<!-- alertaPermisos.vue -->
<script >
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

const alertaVisible = ref(false);

onMounted(() => {
  if (alertaVisible.value) {
    mostrarAlerta();
  }
});

const mostrarAlerta = (tipopermiso, modulo) => {
  Swal.fire({
    icon: 'error',
    title: 'Error',
    html: `El grupo de usuario cuenta con los permisos para <strong>${tipopermiso}</strong> ${modulo}`,
  });
};

export { mostrarAlerta, alertaVisible };
</script>
