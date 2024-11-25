import router from '@/router';

export const formatNumber = (numero) => {
    const numeroFormateado = Number(numero)
        .toFixed(2)
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return numeroFormateado.substring(0, numeroFormateado.length - 3);
};

export const formatDateFilter = (fecha) => {
  
    if (!fecha) return null;

    let partesFecha = fecha.split('/');
    let dia = partesFecha[0];
    let mes = partesFecha[1];
    let año = partesFecha[2];

    let fechaFormateada = año + '-' + mes + '-' + dia;

    return fechaFormateada;
};

export const onClose = () => router.go(-1);
