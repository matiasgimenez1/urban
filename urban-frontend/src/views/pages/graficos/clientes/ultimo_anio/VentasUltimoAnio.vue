<script setup>
import { ref, onMounted } from 'vue';

onMounted(() => {
    chartData.value = setChartData();
});

const chartData = ref();
const chartOptions = ref({
    plugins: {
        legend: {
            labels: {
                usePointStyle: true
            }
        }
    }
});

const setChartData = () => {
    const documentStyle = getComputedStyle(document.body);

    return {
        labels: ['A', 'B', 'C'],
        datasets: [
            {
                data: [540, 325, 702],
                backgroundColor: [documentStyle.getPropertyValue('--blue-500'), documentStyle.getPropertyValue('--yellow-500'), documentStyle.getPropertyValue('--green-500')],
                hoverBackgroundColor: [documentStyle.getPropertyValue('--blue-400'), documentStyle.getPropertyValue('--yellow-400'), documentStyle.getPropertyValue('--green-400')]
            }
        ]
    };
};
</script>
<template>
    <div class="card flex justify-content-center">
        <h5 class="text-blue-500 text-2xl text-center">Evolución de Ventas del Último Año</h5>
    </div>
    <div class="card flex justify-content-center">
        <Chart type="pie" :data="chartData" :options="chartOptions" class="w-full md:w-30rem" />

        <DataTable :value="productsIndex" paginator :rows="5" dataKey="id" tableStyle="min-width: 50rem">
            <Column field="producto" header="Fechas" headerClass="text-white bg-blue-500 text-sm shadow"></Column>

            <Column field="nombre_comercial" header="Importe" headerClass="text-white bg-blue-500 text-sm shadow"></Column>
            <template #footer> Total de Items: {{ productsIndex ? productsIndex.length : 0 }} ventas. </template>
        </DataTable>
    </div>
</template>
