export const isParent = (node, parent) => {
    while (node) {
        if (node === parent) {
            return true;
        }
        node = node.parentNode;
    }
    return false;
}

export const urlPath = (path) => {
    let origin = location.origin ? location.origin : location.protocol + "//" + location.host;
    return `${origin}${path}`;
}