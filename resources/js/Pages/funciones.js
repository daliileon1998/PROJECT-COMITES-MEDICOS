import Swal from 'sweetalert2';

export function mostrarAlerta(tipopermiso, modulo) {
  Swal.fire({
    icon: 'error',
    title: 'Error',
    html: `El grupo de usuario cuenta con los permisos para <strong>${tipopermiso}</strong> ${modulo}`,
  });
}

export function mensaje(text,icon,title) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text
  });
}
