<template>
    <div class="invoice-wrapper bg-white px-3 text-sm">
        <div id="invoice-content">
            <table class="invoice-table invoice-table-1">
                <thead style="border: 1px solid black; border-collapse: collapse" class="invoice-table-header sticky top-0 bg-white">
                    <tr class="invoice-header">
                        <td :colspan="headers.length">
                            <div class="flex flex-column md:items-center gap-1">
                                <span class="text-center text-3xl mt-2">{{ empresa }}</span>
                                <span class="text-center text-xl">{{ subtitle }}</span>
                                <span class="text-center text-base mb-2">{{ filter }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="py-2" v-for="header in headers" :key="header.key" :style="{ width: getColumnWidth(header) }" :class="getTextAlignment(header)">{{ header.title }}</th>
                    </tr>
                </thead>
                <tbody class="invoice-table-content">
                    <template v-for="group in groupedData">
                        <tr>
                            <td v-for="header in headers" :key="header.key" :style="{ width: getColumnWidth(header) }" :class="{ 'border-top-1': groupBy.length > 0, ...getTextAlignment(header) }">
                                <!-- Mostrar el valor de la clave solo si el header.key está en groupBy -->
                                {{ groupBy.includes(header.key) ? group.key.split('##_#')[groupBy.indexOf(header.key)] : '' }}
                            </td>
                        </tr>
                        <tr v-for="item in group.items" :key="item.id">
                            <td
                                v-for="header in headers"
                                :key="`${header.key}-${item.id}`"
                                :style="[
                                    {
                                        paddingLeft: header.key === 'cuenta_contable' || header.key === 'nombre' ? `${item.nivel * 6}px` : '0px'
                                    },
                                    {
                                        width: getColumnWidth(header)
                                    }
                                ]"
                                :class="[getTextAlignment(header), getTextColor(item), { 'font-bold': header.key == 'nombre' && item.nivel == 1 }]"
                            >
                                <!-- Mostrar el valor del ítem solo si el header.key no está en groupBy -->
                                {{ groupBy.includes(header.key) ? '' : header.useThousandsSeparator || header.decimalPlaces !== undefined ? formatNumber(item[header.key], header.useThousandsSeparator, header.decimalPlaces) : item[header.key] }}
                            </td>
                        </tr>
                        <tr class="group-totals" v-if="groupBy.length > 0">
                            <td v-for="header in headers" :key="header.key" :class="getTextAlignment(header)" class="group-total">
                                {{ header.summable ? formatNumber(group.totals[header.key], header.useThousandsSeparator, header.decimalPlaces) : '' }}
                            </td>
                        </tr>

                        <!-- Espaciado entre grupos -->
                        <tr>
                            <td v-for="header in headers" :key="header.key" class="group-divider"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <tfoot>
                <tr v-if="columnTotals.hasSummableColumns">
                    <td class="py-1 font-bold text-xs" v-for="(header, index) in headers" :key="header.key" :style="{ width: getColumnWidth(header) }" :class="getTextAlignment(header)">
                        <template v-if="index === 0"> Totales: </template>
                        <template v-else-if="header.summable">
                            {{ columnTotals.totals[header.key] }}
                        </template>
                    </td>
                </tr>
                <tr class="footer-info">
                    <td class="mt-4" :colspan="Math.floor(headers.length * 0.5)">Fin del Listado</td>
                    <td :colspan="headers.length - Math.floor(columnCount * 0.5)" class="text-right">www.app.puntodeventa.com.py</td>
                </tr>
            </tfoot>
        </div>
    </div>
</template>
<script>
import { useEmpresaStore } from '@/stores/empresaStore'; // Reemplaza con la ruta correcta a tu archivo del store
import { cleanFormattedNumber, formatNumber } from '@/utils/reports/format';
import { ref, onMounted } from 'vue';

export default {
    setup() {
        const empresaStore = useEmpresaStore();

        // Si necesitas cargar la empresa cuando se inicializa el componente
        const empresa = ref('');

        onMounted(async () => {
            await empresaStore.fetchEmpresa(); // Asume que fetchEmpresa es asíncrono. Si no lo es, simplemente elimina el async/await.
            empresa.value = empresaStore.empresa.nombre;
        });

        return {
            empresa,
            formatNumber
        };
    },

    props: {
        headers: {
            type: Array,
            required: true
        },
        subtitle: {
            type: String,
            default: ''
        },
        filter: {
            type: String,
            default: ''
        },
        data: {
            type: Array,
            required: true
        },
        groupBy: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        groupTotals() {
            return this.groupedData.map((group) => group.totals);
        },
        columnTotals() {
            let totals = {};
            let hasSummableColumns = false;

            this.headers.forEach((header) => {
                if (header.summable) {
                    // Suma basado en groupTotals en lugar de data
                    const total = this.groupTotals.reduce((acc, groupTotal) => {
                        return acc + (groupTotal[header.key] || '0');
                    }, 0);

                    totals[header.key] = header.useThousandsSeparator || header.decimalPlaces !== undefined ? formatNumber(total, header.useThousandsSeparator, header.decimalPlaces) : total;

                    hasSummableColumns = true;
                }
            });

            return {
                totals,
                hasSummableColumns
            };
        },
        groupedData() {
            if (this.groupBy.length === 0) {
                return this.data.map((item) => ({
                    key: item.id,
                    items: [item],
                    totals: this.calculateGroupTotals([item])
                }));
            }

            let grouped = {};

            this.data.forEach((item) => {
                let groupKey = this.groupBy.map((key) => item[key]).join('##_#');

                if (!grouped[groupKey]) {
                    grouped[groupKey] = {
                        key: groupKey,
                        items: [],
                        totals: {}
                    };
                }

                grouped[groupKey].items.push(item);
            });

            // Calcular los totales para cada grupo
            for (let groupKey in grouped) {
                grouped[groupKey].totals = this.calculateGroupTotals(grouped[groupKey].items);
            }

            return Object.values(grouped);
        }
    },
    methods: {
        printClick() {
            window.print();
        },
        getTextAlignment(header) {
            switch (header.align) {
                case 'center':
                    return 'text-center';
                case 'right':
                    return 'text-right';
                default:
                    return 'text-left';
            }
        },
        getTextColor(item) {
            switch (item.color) {
                case 'red':
                    return 'text-red-800';
                case 'blue':
                    return 'text-blue';
                default:
                    return 'text-black';
            }
        },
        getColumnWidth(header) {
            return header.width || 'auto'; // retorna el valor de 'width' si está presente, de lo contrario, 'auto'
        },

        getColumnIndentation(header) {
            return header.indentation ? `${header.indentation}rem` : '0'; // retorna el valor de 'indentation' si está presente, de lo contrario, '0'
        },
        calculateGroupTotals(items) {
            let totals = {};

            this.headers.forEach((header) => {
                if (header.summable) {
                    totals[header.key] = items.reduce((acc, item) => acc + parseFloat(item[header.key] || 0), 0);
                }
            });

            return totals;
        }
    }
};
</script>

<style scoped>
.text-blue {
    color: blue;
}
.invoice-table {
    border-spacing: 0;
}

.invoice-table thead th {
    border-bottom: 1px solid black; /* o el color que prefieras */
    border-top: 2px solid black; /* o el color que prefieras */
}

.invoice-footer tr:last-child td {
    border-bottom: 1px solid black; /* o el color que prefieras */
    border-top: 1px solid black; /* o el color que prefieras */
}

.group-total {
    border-bottom: 1px solid black;
    border-top: 1px solid black;
}

tfoot tr:first-child td {
    border-top: 1px solid black;
}

.invoice-footer-print {
    display: table;
    width: 100%;
    border-spacing: 0;
}

.group-separator {
    height: 10px; /* espacio entre grupos */
    background-color: transparent;
    border: none;
}

.row {
    display: flex;
    flex-direction: row;
    width: 100%;
}

.invoice-footer-print > .row > span {
    display: table-cell;
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

.invoice-table thead th {
    border-bottom: 2px solid #333; /* Hace que el borde inferior de los encabezados sea un poco más grueso */
    padding: 8px 0px; /* Añade un padding para mejorar la legibilidad */
}

.group-header td {
    border-top: 2px solid #333; /* Un borde superior más grueso para destacar el inicio de un nuevo grupo */
    padding: 12px 12px; /* Un poco más de padding en la cabecera de grupo */
}

.group-totals td {
    border-top: 1px solid #333;
    border-bottom: 2px solid #333; /* Un borde inferior más grueso para los totales de grupo */
    padding: 10px 0px;
}

.group-divider td {
    border-top: 2px solid #ddd; /* Un borde sutil entre grupos */
    padding: 6px 0; /* Un poco de espacio vertical para separar visualmente los grupos */
}

.invoice-footer-print {
    border-top: 2px solid #333; /* Un borde superior más grueso para la sección de pie de página */
    padding: 10px 0;
}

.row span {
    padding: 0 8px; /* padding horizontal para cada "celda" en el pie de página */
    border-right: 1px solid #ddd; /* Un borde a la derecha de cada "celda" para separarlas */
}

.row span:last-child {
    border-right: none; /* Remueve el borde de la última "celda" */
}

@media print {
    .invoice-wrapper {
        page-break-before: avoid;
        page-break-after: avoid;
    }

    .invoice-table {
        page-break-inside: auto;
    }

    /* Configuramos el tfoot como pie de tabla */
    .invoice-table tfoot {
        display: table-footer-group;
    }

    .invoice-table tfoot {
        display: table-row-group;
        page-break-after: avoid;
        page-break-before: avoid;
    }
}
</style>
