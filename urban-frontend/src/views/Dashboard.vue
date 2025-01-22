<template>
    <div class="grid">
        <!-- Total Jugadores -->
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-users text-color"></i>
                    <h6 class="m-0 text-color pl-2">Total Jugadores</h6>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 90px">
                        <span class="mb-1 text-4xl">{{ totalJugadores }}</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">Este Mes: {{ nuevosJugadoresMes }}</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="jugadoresChartData" :options="overviewChartOptions" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reservas Mes -->
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-calendar text-color"></i>
                    <h6 class="m-0 text-color pl-2">Reservas</h6>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 105px">
                        <span class="mb-1 text-4xl">+ {{ reservasMesActual }}</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">Vs. Mes Pasado</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="reservasChartData" :options="overviewChartOptions" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
        <!-- Partidos Jugados -->
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-chart-line text-color"></i>
                    <h6 class="m-0 text-color pl-2">Partidos Jugados</h6>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 90px">
                        <span class="mb-1 text-4xl">{{ totalPartidos }}</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">Este Mes: {{ partidosMesActual }}</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="line" :data="partidosChartData" :options="overviewChartOptions" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancha Más Usada -->
        <div class="col-12 md:col-6 lg:col-3">
            <div class="card flex flex-column">
                <div class="flex align-items-center text-gray-700">
                    <i class="pi pi-map-marker text-color"></i>
                    <h6 class="m-0 text-color pl-2">Cancha Más Usada</h6>
                </div>
                <div class="flex justify-content-between mt-3 flex-wrap">
                    <div class="flex flex-column" style="width: 110px">
                        <span class="mb-1 text-3xl">{{ canchaMasUsada }}</span>
                        <span class="font-medium border-round-xs text-white p-1 bg-teal-500 text-sm">Uso: {{ usoCanchaMasUsada }}</span>
                    </div>
                    <div class="flex align-items-end">
                        <Chart type="bar" :data="canchaChartData" :options="overviewChartOptions" :height="60" :width="160"></Chart>
                    </div>
                </div>
            </div>
        </div>
 
    </div>
</template>

<script setup>
import { ref } from 'vue';

// Datos para las tarjetas de estadísticas
const totalPartidos = ref(320);
const partidosMesActual = ref(25);
const reservasMesActual = ref(85); // Total de reservas del mes actual
const totalJugadores = ref(120);
const nuevosJugadoresMes = ref(10);
const canchaMasUsada = ref('Cancha 1');
const usoCanchaMasUsada = ref(85);

// Datos para el ranking de jugadores
const rankingPlayers = ref([
    { name: 'Xuxue Feng', email: 'feng@prueba.org', avatar: '/demo/images/avatar/xuxuefeng.png', position: 1, points: 1200, category: 8 },
    { name: 'Elwin Sharvill', email: 'sharvill@prueba.org', avatar: '/demo/images/avatar/elwinsharvill.png', position: 2, points: 1150, category: 7 },
    { name: 'Anna Fali', email: 'fali@prueba.org', avatar: '/demo/images/avatar/asiyajavayant.png', position: 3, points: 1100, category: 6 },
    { name: 'Jon Stone', email: 'stone@prueba.org', avatar: '/demo/images/avatar/ivanmagalhaes.png', position: 4, points: 1050, category: 4 },
    { name: 'Stephen Shaw', email: 'shaw@prueba.org', avatar: '/demo/images/avatar/stephenshaw.png', position: 5, points: 1020, category: 3 }
]);

// Datos para gráficos
const partidosChartData = ref({
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre'],
    datasets: [{ data: [40, 45, 30, 50, 55, 60, 45, 70, 65], borderColor: ['#4DD0E1'], backgroundColor: ['rgba(77, 208, 225, 0.8)'], borderWidth: 2, fill: true, tension: 0.4 }]
});

const reservasChartData = ref({
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre'],
    datasets: [{ data: [50, 55, 65, 75, 80, 85, 90, 95, reservasMesActual.value], borderColor: ['#4DD0E1'], backgroundColor: ['rgba(77, 208, 225, 0.8)'], borderWidth: 2, fill: true, tension: 0.4 }]
});

const jugadoresChartData = ref({
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre'],
    datasets: [{ data: [5, 12, 8, 15, 20, 25, 18, 30, 22], borderColor: ['#4DD0E1'], backgroundColor: ['rgba(77, 208, 225, 0.8)'], borderWidth: 2, fill: true, tension: 0.4 }]
});

const canchaChartData = ref({
    labels: ['Cancha 1', 'Cancha 2', 'Cancha 3', 'Cancha 4'],
    datasets: [{ data: [85, 70, 55, 40], backgroundColor: ['#4DD0E1', '#673AB7', '#FF9800', '#009688'], borderWidth: 1 }]
});

// Opciones de gráfico
const overviewChartOptions = ref({
    plugins: { legend: { display: false } },
    responsive: true,
    scales: { y: { display: false }, x: { display: false } },
    tooltips: { enabled: false },
    elements: { point: { radius: 0 } }
});
</script>

<style scoped>
.bg-gold {
    background-color: #ffd700;
}
.bg-silver {
    background-color: #c0c0c0;
}
.bg-bronze {
    background-color: #cd7f32;
}
</style>
