// The cart lives in cookies named cart_<itemid>, valid for 30 minutes.
// (The same prefix is used for guests and signed-in users, so the cart survives logging in.)
const CART_PREFIX = 'cart_';

// creating a cart cookie (30 minutes)
function createCookie(name, value) {
    const expires = new Date(Date.now() + 30 * 60 * 1000).toUTCString();
    document.cookie = `${name}=${value}; path=/; expires=${expires}; SameSite=Lax`;
}

// deleting a cookie: set an expiry date in the past
function eraseCookie(name) {
    document.cookie = `${name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax`;
}

function addToCart(itemid, button = null) {
    createCookie(`${CART_PREFIX}${itemid}`, itemid);
    if (button) {
        const old = button.textContent;
        button.textContent = 'Added ✓';
        button.disabled = true;
        setTimeout(() => { button.textContent = old; button.disabled = false; }, 1000);
    }
}

function removeFromCart(itemid) {
    eraseCookie(`${CART_PREFIX}${itemid}`);   // exact name: cart_1 must not also remove cart_10
}
