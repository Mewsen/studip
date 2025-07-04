export const highlightText = (keyword, containerSelector)=>  {
    const elements = document.querySelectorAll(containerSelector);

    for (const element of elements) {
        const re = new RegExp(keyword, "gi");
        element.innerHTML = element.innerHTML.replace(re, match => `<mark>${match}</mark>`);
    }
}

export const removeHighlight = (containerSelector) =>  {
    const markedElements = document.querySelectorAll(containerSelector);
    for (const element of markedElements) {
        const textNode = document.createTextNode(element.textContent);
        element.replaceWith(textNode);
    }
}
