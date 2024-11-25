export const clearFormAttributes = (formObject) => {
    for (let key in formObject) {
        formObject[key] = undefined; // o puedes usar `null` dependiendo de tus necesidades
    }
};
