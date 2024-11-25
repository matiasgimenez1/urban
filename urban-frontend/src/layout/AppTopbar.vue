<script setup>
import { ref, computed, onMounted, onBeforeUnmount, reactive } from 'vue';
import { useLayout } from '@/layout/composables/layout';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { getUsuario } from '@/http/usuarios/usuarios-api';

const { layoutConfig, onMenuToggle } = useLayout();

const currentAction = ref('INSERT');
const currentItemId = ref(null);

const outsideClickListener = ref(null);
const topbarMenuActive = ref(false);
const router = useRouter();
const form = reactive({});
const { user, dataBaseConfig, handleLogout } = useAuthStore(); // obtener el usuario del store

const visible = ref(false);

const closeModal = () => {
    visible.value = false;
};

const openUserDialog = async () => {
    visible.value = true;
    // Realiza la llamada después de abrir el diálogo
    const response = await getUsuario(user);

    const attributes = response.data[0].attributes;
    Object.assign(form, attributes); // This assigns the attributes to the form
};

onMounted(() => {
    bindOutsideClickListener();
});

onBeforeUnmount(() => {
    unbindOutsideClickListener();
});

const logoUrl = computed(() => {
    return `ag/images/urban-logo-white-2.png`;
});

const onTopBarMenuButton = () => {
    topbarMenuActive.value = !topbarMenuActive.value;
};
const onSettingsClick = () => {
    topbarMenuActive.value = false;
    router.push('/documentation');
};
const topbarMenuClasses = computed(() => {
    return {
        'layout-topbar-menu-mobile-active': topbarMenuActive.value
    };
});

const bindOutsideClickListener = () => {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                topbarMenuActive.value = false;
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
};
const unbindOutsideClickListener = () => {
    if (outsideClickListener.value) {
        document.removeEventListener('click', outsideClickListener);
        outsideClickListener.value = null;
    }
};
const isOutsideClicked = (event) => {
    if (!topbarMenuActive.value) return;

    const sidebarEl = document.querySelector('.layout-topbar-menu');
    const topbarEl = document.querySelector('.layout-topbar-menu-button');

    return !(sidebarEl.isSameNode(event.target) || sidebarEl.contains(event.target) || topbarEl.isSameNode(event.target) || topbarEl.contains(event.target));
};

const onExitClick = () => {
    //location.reload();
    handleLogout();
};
</script>

<template>
    <div class="layout-topbar">
        <router-link to="/" class="layout-topbar-logo">
            <img :src="logoUrl" alt="logo" />
        </router-link>

        <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
            <i class="pi pi-bars text-white"></i>
        </button>

        <button class="p-link layout-topbar-menu-button layout-topbar-button" @click="onTopBarMenuButton()">
            <i class="pi pi-ellipsis-v"></i>
        </button>
        <span class="text-2xl mr-3 text-white montserrat-font">{{ dataBaseConfig.empresa }}</span>

        <div class="layout-topbar-menu d-flex align-items-center mt-1" :class="topbarMenuClasses">
            <div class="user-display text-sm mr-5 font-bold">
                <button class="p-link" @click="openUserDialog()">
                    <i class="topbar-icon pi pi-user text-white"></i>
                </button>
                <Dialog v-model:visible="visible" modal header="Datos del Usuario" :style="{ width: '800px' }" contentClass="bg-white">
                    <div class="col-12">
                        <div class="card">
                            <div class="p-fluid formgrid grid">
                                <div class="field col-12 md:col-6">
                                    <label for="firstname2">Cuenta</label>
                                    <InputText id="firstname2" type="text" v-model="form.cuenta" />
                                </div>
                                <div class="field col-12 md:col-6">
                                    <label for="lastname2">Nombre</label>
                                    <InputText id="lastname2" type="text" v-model="form.nombre" />
                                </div>
                                <div class="field col-12 md:col-2">
                                    <label for="address">Sucursal</label>
                                    <InputText id="lastname2" type="text" v-model="form.sucursal" />
                                </div>
                                <div class="field col-12 md:col-4">
                                    <label for="lastname2">Oficina</label>
                                    <InputText id="lastname2" type="text" v-model="form.oficina" />
                                </div>
                                <div class="field col-12 md:col-6">
                                    <label for="lastname2">Cargo</label>
                                    <InputText id="lastname2" type="text" v-model="form.cargo" />
                                </div>
                                <div class="field col-12 md:col-4">
                                    <label for="lastname2">Celular</label>
                                    <InputText id="lastname2" type="text" v-model="form.celular" />
                                </div>
                                <div class="field col-12 md:col-6">
                                    <label for="lastname2">Correo</label>
                                    <InputText id="lastname2" type="text" v-model="form.correo" />
                                </div>

                                <div class="field col-12 md:col-3 text-center">
                                    <label for="state">Estado</label>
                                    <Dropdown
                                        class="text-xs text-center"
                                        id="clase"
                                        v-model="form.estado"
                                        :options="[
                                            { name: 'ACTIVO', code: 'A' },
                                            { name: 'INACTIVO', code: 'I' }
                                        ]"
                                        optionLabel="name"
                                        optionValue="code"
                                        placeholder=""
                                        :style="{ width: '100%' }"
                                    ></Dropdown>
                                </div>
                                <div class="field col-12 md:col-3 text-center">
                                    <label for="zip">Último Acceso</label>
                                    <InputText id="zip" type="text" v-model="form.ultimo_acceso" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="closeModal" />
                    </template>
                </Dialog>
            </div>

            <div class="user-display text-sm mr-3 font-bold text-white montserrat-font">
                USUARIO:<br />
                <span class="font-semibold text-white">{{ user }}</span>
            </div>
            <!-- <div class="user-display text-sm mr-3 font-bold text-white montserrat-font">
                ENTORNO:<br />
                <span class="font-semibold text-white">{{ dataBaseConfig.entorno }}</span>
            </div> -->

            <button @click="onExitClick()" class="p-link layout-topbar-button hover:text-red-500 montserrat-font">
                <i class="fas fa-right-from-bracket text-white"></i>
                <span>Cerrar Sesión</span>
            </button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.layout-topbar .logo-title {
    font-size: 0.9em; /* Ajusta el tamaño de la fuente */
    color: #333; /* Cambia el color del texto */
    font-weight: thin; /* Hace que el texto sea negrita */
    text-transform: uppercase; /* Convierte el texto a mayúsculas */
    background-color: #469cd7 !important;
}

.layout-topbar {
    height: 4rem;
    color: #333;
    background-color: #469cd7 !important;
}

.user-display {
    font-size: 1.2rem;
    font-weight: 500;

    color: #333;
}
.user-display span {
    display: block; /* Hacer que los spans se muestren en bloques */
    color: #333;
}

label {
    /* tamaño de titulos de campos */
    color: var(--blue-500) !important;
    font-size: 11px;
}
</style>
