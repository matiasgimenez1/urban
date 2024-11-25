// export const formatNumber = (value, useThousandsSeparator, decimalPlaces) => {
//     if (value === null || value === undefined || isNaN(value)) {
//         return value;
//     }

//     const numberValue = parseFloat(value);

//     const options = {
//         minimumFractionDigits: decimalPlaces,
//         maximumFractionDigits: decimalPlaces
//     };

//     if (!useThousandsSeparator) {
//         options.useGrouping = false;
//     }

//     return numberValue.toLocaleString(undefined, options);
// }; ORIGINAL FUNCION EB 19/10/2023 COMENTADO

export const formatNumber = (value, useThousandsSeparator, decimalPlaces) => {
    if (value === null || value === undefined || isNaN(value)) {
        return value;
    }

    const numberValue = parseFloat(value);

    let formattedValue;

    if (decimalPlaces === 0) {
        // Si no se requieren decimales, podemos usar toLocaleString directamente
        formattedValue = numberValue.toLocaleString(undefined, {
            useGrouping: useThousandsSeparator,
            maximumFractionDigits: 0,
        });
    } else {
        const options = {
            useGrouping: useThousandsSeparator,
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces,
        };
        formattedValue = numberValue.toLocaleString(undefined, options);
    }

    return formattedValue;
};


export const cleanFormattedNumber = (str) => {
    // Suponiendo que estás usando punto como separador de miles y coma como decimal
    // Reemplaza el punto por nada y cambia la coma por un punto para manejar decimales
    return parseFloat(str.replace(/\./g, '').replace(',', '.'));
};
