
document.addEventListener("DOMContentLoaded", function () {
    const colorInput = document.querySelector('input[name="color"]');
    const sizeInput = document.querySelector('input[name="size"]');

    const colorDots = document.querySelectorAll(".dot");
    const sizeButtons = document.querySelectorAll(".my-btn");

    // Color selection
    colorDots.forEach(function (dot) {
        dot.addEventListener("click", function () {
            colorDots.forEach(function (item) {
                item.classList.remove("selected");
            });

            dot.classList.add("selected");
            colorInput.value = dot.id;
        });
    });

    // Size selection
    sizeButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            sizeButtons.forEach(function (item) {
                item.classList.remove("selected");
            });

            button.classList.add("selected");
            sizeInput.value = button.textContent.trim();
        });
    });

    // Quantity controls
    const minusButton = document.getElementById("minus");
    const plusButton = document.getElementById("plus");
    const quantityInput = document.getElementById("input");

    minusButton.addEventListener("click", function () {
        let quantity = parseInt(quantityInput.value) || 1;

        if (quantity > 1) {
            quantityInput.value = quantity - 1;
        }
    });

    plusButton.addEventListener("click", function () {
        let quantity = parseInt(quantityInput.value) || 1;
        quantityInput.value = quantity + 1;
    });

    quantityInput.addEventListener("change", function () {
        let quantity = parseInt(quantityInput.value);

        if (isNaN(quantity) || quantity < 1) {
            quantityInput.value = 1;
        }
    });
});