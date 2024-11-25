<script setup>
import { useAuthStore } from '@/stores/auth';
import { storeToRefs } from 'pinia';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { ref, reactive } from 'vue';
import AppConfig from '@/layout/AppConfig.vue';
import router from '@/router';
import { messages } from '@/utils/validations/messages';
import ErrorMessages from '@/components/errors/ErrorMessages.vue';

const store = useAuthStore();
const { tryRucLogin } = store;
const { hasLoggedInWithRuc, isLoggedIn, loginErrorMessage } = storeToRefs(store);

const invalidRuc = ref(false);
const invalidCredentials = ref(false);
const isLoading = ref(false);

const formData = reactive({
    userName: '',
    password: '',
    ruc: ''
});

const rules = {
    userName: { required: helpers.withMessage(messages.required, required) },
    password: { required: helpers.withMessage(messages.required, required) },
    ruc: { required: helpers.withMessage(messages.required, required) }
};

const v$ = useVuelidate(rules, formData);

const handleRucLogin = async () => {
    const result = await v$.value.$validate();
    const formError = !!v$.value.ruc?.$errors?.[0];

    if (!result && !formError) {
        isLoading.value = true;
        await tryRucLogin(formData.ruc);
        isLoading.value = false;

        if (!hasLoggedInWithRuc.value) {
            invalidRuc.value = loginErrorMessage;
        } else {
            v$.value.userName.$reset();
            v$.value.password.$reset();
        }
    }
};

const handleLogin = async () => {
    const result = await v$.value.$validate();
    const error = !!v$.value.userName?.$errors?.[0] || !!v$.value.password?.$errors?.[0];

    if (!error) {
        isLoading.value = true;
        await store.tryLogin({
            username: formData.userName,
            password: formData.password,
            // direccion_ip: store.dataBaseConfig.direccion_ip,
            // puerto: store.dataBaseConfig.puerto
        });
        isLoading.value = false;

        if (isLoggedIn.value) {
            router.push('/');
        } else {
            invalidCredentials.value = true;
        }
    }
};

const onExitClick = () => {
    location.reload();
};
</script>

<template>
    <div class="background-image surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <div class="card-container">
                <div class="login-container w-full surface-card py-4 px-5 sm:px-8">
                    <div class="text-center mb-0">
                        <img src="/ag/images/urban-logo-3.png" alt="Image" height="260" class="mb-0" />
                    </div>

                    <div>
                        <div >
                            <div class="flex flex-column mb-3">
                                <label for="usuario1" class="block text-900 font-medium py-1 montserrat-font">Usuario</label>
                                <InputText id="usuario" type="text" placeholder="Usuario" class="w-full md:w-0rem montserrat-font" style="padding: 1rem" v-model="formData.userName" @keyup.enter="handleLogin" />
                                <ErrorMessages :errors="v$.userName.$errors" />
                            </div>

                            <div class="flex flex-column mb-0">
                                <label for="password1" class="block text-900 font-medium py-1 montserrat-font">Contraseña</label>
                                <Password id="password1" v-model="formData.password" placeholder="Contraseña" :toggleMask="true" class="w-full mb-3 montserrat-font" inputClass="w-full" inputStyle="padding:1rem" @keyup.enter="handleLogin"></Password>
                                <ErrorMessages :errors="v$.password.$errors" />
                                <Message v-if="invalidCredentials" severity="warn" sticky>{{ loginErrorMessage }}</Message>
                            </div>
                            <br />
                            <div v-if="isLoading" class="flex flex-row justify-center">
                                <ProgressSpinner />
                            </div>
                            <Button v-else @click="handleLogin" label="Iniciar sesión" class="w-full p-3 text-xl mb-2 montserrat-font"></Button>
                            <Button @click="onExitClick()" label="Salir" class="w-full p-3 text-xl montserrat-font"></Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <AppConfig simple />
</template>

<style scoped>
.background-image {
    /* background-image: url('/ag/images/paintball_scene_converted.jpeg'); */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0.8;
}

.card-container {
    border-radius: 56px;
    padding: 0.3rem;
    background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%);
    border-radius: 53px;
}

.login-container {
    border-radius: 1rem; /* Ajusta el valor según sea necesario */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Sombra suave */
    background-color: white; /* Fondo blanco para el contenedor */
    padding: 0rem; /* Ajusta el padding según sea necesario */
    width: 100%; /* Asegúrate de que ocupe todo el ancho necesario */
    max-width: 400px; /* Ajusta el ancho máximo según tus necesidades */
    margin: auto; /* Centra el contenedor */
}
</style>
