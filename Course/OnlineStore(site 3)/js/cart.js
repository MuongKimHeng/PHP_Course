// creating a cart cookie (30 minutes)
function createCookie(uname, id) {
    const expires = new Date(Date.now() + 30 * 60 * 1000).toUTCString();
    document.cookie = `${uname}=${id}; path=/; expires=${expires}`;
}

// deleting a cookie: set an expiry date in the past
function eraseCookie(uname) {
    for (const c of document.cookie.split('; ')) {
        const [key] = c.split('=');
        if (key.startsWith('cart_'+uname)) {
            document.cookie = `${key}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        }
    }
}
function addToCart(itemid) {
    createCookie(`cart_${itemid}`,itemid)
}
