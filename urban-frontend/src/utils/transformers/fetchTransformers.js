export function getAttributesFromData(responseData) {
    const {
        data: {
            data: { attributes }
        }
    } = responseData;

    return { ...attributes };
}



export function getItemsAndMetaFromData(responseData) {
    const {
        data: { data: items, meta }
    } = responseData;

    return { items, meta };
}

export function mapItemsToIndexFormat(items) {
    return items.map((item) => ({
        id: item.id,
        ...item.attributes
    }));
}
