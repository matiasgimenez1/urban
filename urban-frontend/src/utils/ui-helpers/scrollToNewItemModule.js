import { nextTick } from 'vue';

export const useScrollToNewItem = () => {
    const scrollToRow = async (index, detailIdentifier = 'detail') => {
        const element = document.getElementById(`${detailIdentifier}-${index}`);

        if (element) {
            element.focus();
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    };

    const addNewItem = async (detailArray, createNewItemFunction, detailIdentifier = 'detail') => {
        const newItem = createNewItemFunction(detailArray.length + 1);
        detailArray.push(newItem);

        nextTick(async () => {
            await scrollToRow(detailArray.length - 1, detailIdentifier);
        });
    };

    return { addNewItem };
};
