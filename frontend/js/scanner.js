let lastCode = "";

function resetForm(){
    document.getElementById("productForm").style.display = "none";
    document.getElementById("message").innerText = "";
    document.getElementById("newProductFields").style.display = "none";
}

function showProductForm(product){
    document.getElementById("productForm").style.display = "block";
    if(product.exists){
        document.getElementById("productTitle").innerText = product.product.name;
        document.getElementById("currentStock").innerText = product.product.stock_quantity;
        document.getElementById("newProductFields").style.display = "none";
    } else {
        document.getElementById("productTitle").innerText = "Nuovo Prodotto";
        document.getElementById("currentStock").innerText = 0;
        document.getElementById("newProductFields").style.display = "block";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    resetForm();

    const html5QrcodeScanner = new Html5Qrcode("reader");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        async (decodedText) => {
            if(decodedText === lastCode) return;
            lastCode = decodedText;

            const product = await apiGet(`check-product.php?barcode=${decodedText}`);
            showProductForm(product);

            document.getElementById("saveBtn").onclick = async () => {
                const qty = parseInt(document.getElementById("quantity").value);
                const type = document.getElementById("movementType").value;

                if(product.exists){
                    const res = await apiPost("update-stock.php", {
                        product_id: product.product.id,
                        quantity: qty,
                        type: type
                    });
                    document.getElementById("message").innerText = res.success ? "Movimento salvato" : res.error;
                } else {
                    const name = document.getElementById("name").value;
                    const description = document.getElementById("description").value;
                    const shelf = document.getElementById("shelf").value;

                    const res = await apiPost("create-product.php", {
                        barcode: decodedText,
                        name, description, shelf,
                        quantity: qty
                    });
                    document.getElementById("message").innerText = res.success ? "Prodotto creato e movimento salvato" : res.error;
                }
            };
        },
        (errorMessage) => {
            // scanning error
        }
    );
});
