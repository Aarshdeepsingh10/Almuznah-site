
console.log("productdetails.js loaded - updated version");

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("cart-form");
    const skuInput = document.getElementById("sku_id");
    const quantityInput = document.getElementById("input");
    const cartQuantity = document.getElementById("quantity");
    const addButton = document.getElementById("add-to-cart-button");
    const message = document.getElementById("sku-message");

    const colorDots = document.querySelectorAll(".dot[data-color-id]");
    const sizeButtons = document.querySelectorAll(".my-btn[data-size-id]");
    const skuOptions = window.skuOptions || [];

    let selectedColorId = null;
    let selectedSizeId = null;
    let selectedStock = 0;

    console.log("DOM READY HANDLER RUNNING");
    console.log("Form found:", !!form);
    console.log("Color dots found:", colorDots.length);
    console.log("Size buttons found:", sizeButtons.length);
    console.log("SKU options found:", skuOptions.length);

    if (
        !form ||
        !skuInput ||
        !quantityInput ||
        !cartQuantity ||
        !addButton ||
        !message
    ) {
        console.error(
            "Required elements are missing. Check the PHP form IDs."
        );
        return;
    }

    function findSku() {
        if (selectedColorId === null || selectedSizeId === null) {
            return null;
        }

        return skuOptions.find(function (sku) {
            return Number(sku.color_id) === selectedColorId &&
                   Number(sku.size_id) === selectedSizeId;
        }) || null;
    }

    function updateQuantity() {
        let quantity = parseInt(quantityInput.value, 10);

        if (!Number.isInteger(quantity) || quantity < 1) {
            quantity = 1;
        }

        if (selectedStock > 0) {
            quantity = Math.min(quantity, selectedStock);
        }

        quantityInput.value = quantity;
        cartQuantity.value = quantity;
    }

    function updateSku() {
        const sku = findSku();

        skuInput.value = "";
        selectedStock = 0;
        quantityInput.removeAttribute("max");

        if (selectedColorId === null || selectedSizeId === null) {
            addButton.disabled = true;
            message.textContent = "Please select a color and size.";
            return;
        }

        if (!sku) {
            addButton.disabled = true;
            message.textContent =
                "This color and size combination is unavailable.";
            return;
        }

        selectedStock = Number(sku.stock_quantity) || 0;

        if (selectedStock < 1) {
            addButton.disabled = true;
            message.textContent = "This variant is out of stock.";
            return;
        }

        skuInput.value = String(sku.sku_id);
        quantityInput.max = selectedStock;

        updateQuantity();

        addButton.disabled = false;
        message.textContent = "Available stock: " + selectedStock;

        console.log("Selected SKU ID:", sku.sku_id);
    }

    // Color selection
    function selectColor(dot) {
        selectedColorId = Number(dot.dataset.colorId);

        colorDots.forEach(function (item) {
            item.classList.remove("selected");
        });

        dot.classList.add("selected");

        updateSku();
    }

    colorDots.forEach(function (dot) {
        dot.addEventListener("click", function () {
            selectColor(dot);
        });

        dot.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                selectColor(dot);
            }
        });
    });

    // Size selection
    function selectSize(button) {
        selectedSizeId = Number(button.dataset.sizeId);

        sizeButtons.forEach(function (item) {
            item.classList.remove("selected");
        });

        button.classList.add("selected");

        updateSku();
    }

    sizeButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            selectSize(button);
        });
    });

    // Decrease quantity
    const minusButton = document.getElementById("minus");

    if (minusButton) {
        minusButton.addEventListener("click", function () {
            const current = parseInt(quantityInput.value, 10) || 1;

            quantityInput.value = Math.max(1, current - 1);
            updateQuantity();
        });
    }

    // Increase quantity
    const plusButton = document.getElementById("plus");

    if (plusButton) {
        plusButton.addEventListener("click", function () {
            const current = parseInt(quantityInput.value, 10) || 1;

            if (selectedStock > 0 && current >= selectedStock) {
                quantityInput.value = selectedStock;
                updateQuantity();
                return;
            }

            quantityInput.value = current + 1;
            updateQuantity();
        });
    }

    // Manually entered quantity
    quantityInput.addEventListener("input", function () {
        if (quantityInput.value === "") {
            cartQuantity.value = "";
            return;
        }

        updateQuantity();
    });

    quantityInput.addEventListener("change", updateQuantity);

    // Final validation before submitting
    form.addEventListener("submit", function (event) {
        updateSku();

        const quantity = Number(cartQuantity.value);

        if (
            !skuInput.value ||
            !Number.isInteger(quantity) ||
            quantity < 1 ||
            quantity > selectedStock
        ) {
            event.preventDefault();
            alert("Please select an available color and size.");
            return;
        }

        console.log("Submitting SKU:", skuInput.value);
        console.log("Quantity:", cartQuantity.value);
    });

    addButton.disabled = true;
    message.textContent = "Please select a color and size.";
});

document.addEventListener("DOMContentLoaded", function () {
    const wishlistButton = document.getElementById("wishlist-button");
    const wishlistMessage = document.getElementById("wishlist-message");

    if (!wishlistButton || !wishlistMessage) {
        return;
    }

    wishlistButton.addEventListener("click", async function () {
        const productId = wishlistButton.dataset.productId;
        const icon = wishlistButton.querySelector("i");
        const text = wishlistButton.querySelector("span");

        wishlistButton.disabled = true;
        wishlistMessage.textContent = "Processing...";

        const formData = new FormData();
        formData.append("action", "add");
        formData.append("product_id", productId);

        try {
            const response = await fetch("wishlist_action.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                wishlistMessage.textContent = data.message;
                return;
            }

            wishlistButton.classList.remove("btn-outline-danger");
            wishlistButton.classList.add("btn-danger");

            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");

            text.textContent = "Added to Wishlist";
            wishlistMessage.textContent = data.message;
        } catch (error) {
            wishlistMessage.textContent =
                "Unable to update wishlist. Please try again.";
        } finally {
            wishlistButton.disabled = false;
        }
    });
});
