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
                    <tr v-for="item in data" :key="item.id" class="invoice-table-row">
                        <td
                            :style="{
                                paddingLeft: header.key === 'cuenta_contable' || header.key === 'nombre' ? `${item.nivel * 6}px` : '0px'
                            }"
                            style="padding-top: 10px"
                            :class="[getTextAlignment(header), getTextColor(item), { 'font-bold': header.key == 'nombre' && item.nivel == 1 }]"
                            v-for="header in headers"
                            :key="`${header.key}-${item.id}`"
                        >
                            {{ header.summable && !item[header.key] ? '0' : item[header.key] }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-if="columnTotals.hasSummableColumns">
                        <td class="font-bold" v-for="(header, index) in headers" :key="header.key" :style="{ width: getColumnWidth(header) }" :class="getTextAlignment(header)">
                            <template v-if="index === 0"> Totales: </template>
                            <template v-else-if="header.summable">
                                {{ columnTotals.totals[header.key] }}
                            </template>
                        </td>
                    </tr>
                    <tr class="footer-info">
                        <td class="mt-4" :colspan="Math.floor(columnCount * 0.4)">Fin del Listado</td>
                        <td :colspan="columnCount - Math.floor(columnCount * 0.4)" class="text-right">www.app.puntodeventa.com.py</td>
                    </tr>
                </tfoot>
            </table>
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

        const empresa = ref('');

        onMounted(async () => {
            await empresaStore.fetchEmpresa(); // Asume que fetchEmpresa es asíncrono. Si no lo es, simplemente elimina el async/await.
            empresa.value = empresaStore.empresa.nombre;
        });

        return {
            empresa
        };
    },

    props: {
        headers: {
            type: Array,
            required: true
        },
        title: {
            type: String,
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
        }
    },
    computed: {
        columnTotals() {
            let totals = {};
            let hasSummableColumns = false;

            this.headers.forEach((header) => {
                if (header.summable) {
                    const total = this.data.reduce((acc, item) => acc + cleanFormattedNumber(item[header.key] || '0'), 0);

                    // Aplica el formato
                    totals[header.key] = header.useThousandsSeparator || header.decimalPlaces !== undefined ? formatNumber(total, header.useThousandsSeparator, header.decimalPlaces) : total;

                    hasSummableColumns = true;
                }
            });

            return {
                totals,
                hasSummableColumns
            };
        },
        columnCount() {
            return this.headers.length;
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
        }
    }
};
</script>

<style scoped>
/* Estilos generales */

.text-blue {
    color: blue;
}

.invoice-table {
    width: 100%;
    border-spacing: 0;
}

.invoice-table thead th {
    border-bottom: 1px solid black;
    border-top: 1px solid black;
}

tfoot tr:first-child td {
    border-top: 1px solid black;
}

.invoice-footer tr:last-child td {
    border-top: 1px solid black;
}

.invoice-footer-print {
    display: table;
    width: 100%;
    border-spacing: 0;
}

.row {
    display: flex;
    flex-direction: row;
    width: 100%;
}

.footer-info td {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

.invoice-footer-print > .row > span {
    display: table-cell;
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

/* Estilos para impresión */

@media print {
    /* Mueve el tfoot al pie de página cuando imprimes */

    #invoice-content {
        height: auto;
    }
    .invoice-table tfoot {
        display: table-row-group;
        page-break-after: avoid;
        page-break-before: avoid;
    }
}
</style>
