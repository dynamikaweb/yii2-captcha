function loadCaptcha(vendor, id, input, key)
{
    const el = document.querySelector(input);

    vendor.render(id, {
        "sitekey": key,
        "callback": (response) => {
            el.value = response;
            el.dispatchEvent(new Event("change"));
        },
        "expired-callback": () => {
            el.value = '';
            el.dispatchEvent(new Event("change"));
        }
    });
}
