<script setup>
import { reactive, onMounted } from 'vue';
import { getResumenCompras } from '@/http/graficos/compras/resumen-compras';
import DateRangeFilter from '@/components/headers/filters/DateRangeFilter.vue';
import { formatDateFilter } from '@/utils/functions';

let initialDate = null;
let finalDate = null;

const lineData = reactive({
    labels: [],
    datasets: []
});

const clearChart = () => {
    lineData.labels = [];
    lineData.datasets = [];
};

const onSearch = async (ini, fin) => {
    // EB 18/07/2023 dejo de usar el formato del input, porque uso un componente calendario de PRIMEVUE https://primevue.org/calendar/ y porque no funciona si dejo ambos implementados
    // initialDate = formatDateFilter(ini);
    // finalDate = formatDateFilter(fin);

    initialDate = ini;
    finalDate = fin;
    await fetchData();
};

const fetchData = async () => {
    clearChart();
    try {
        let stats = await getResumenCompras({ fecha_desde: initialDate, fecha_hasta: finalDate });

        stats = Object.values(stats);

        //setting labels
        let labels = [];
        stats.forEach((element) => {
            labels.push(element.ejercicio);
        });

        lineData.labels = labels;

        //setting datasets values
        let data = [];
        stats.forEach((element) => {
           
            data.push(element.totalCompras);
        });

        lineData.datasets.push({
            label: 'Compras',
            data: data,
            fill: false,
            backgroundColor: '#2196F3',
            borderColor: '#2196F3',
            pointBorderColor: '#3B82F6',
            pointBorderWidth: '2',
            pointBackgroundColor: '#EFB702',
            tension: 0.3
        });
    } catch (error) {
        console.error('Error:', error);
    }
};

onMounted(() => {
    fetchData();
});
</script>

<template>
    <div class="grid">
        <div class="col-12">
            <div class="card bg-blue-500 shadow-4 mb-1 p-3">
                <!-- <h4 class="text-center font-bold text-white mb-0 py-1">Compras Mensuales</h4> -->
                <h5 class="logo-title text-2xl text-center text-white">Compras Mensuales</h5>
            </div>
            <div class="card mt-1 p-1">
                <DateRangeFilter @search="onSearch"></DateRangeFilter>
                <div class="chart-container">
                    <Chart type="line" :data="lineData" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.chart-container {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    padding: 0 8px;
}
</style>
