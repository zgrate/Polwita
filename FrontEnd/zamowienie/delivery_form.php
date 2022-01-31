<?php require_once('../fragments/header.php'); ?>


<div class="container">
    <main class="align-self-center mx-auto">
        <div class="row g-5 mt-auto justify-content-center">
            <div class="col-8 flex-row-reverse">
                <div class="row">
                    <form class="col-8">
                        <h3>Sposób dostawy</h3>
                        <ul id='delivery' class="row list-group">

                        </ul>
                    </form>
                </div>

            </div>
            <div class="col-4 ">
                <div class="row  border border-primary p-2">
                    <h4 class="text-center">Podsumowanie Koszyka</h4>
                    <h4 id="basket" class="text-center">Koszyk: </h4>
                    <h4 id="delivery_sum" class="text-center">Dostawa: </h4>
                    <h2 id="sum" class="text-center">RAZEM: </h2>
                    <button class="my-2 btn btn-primary text-center">Wróć</button>
                </div>
                <h2 id="suma" class="col align-self-end my-2">
                </h2>
            </div>

        </div>
        <div class="row">
            <button id="cancel" type="reset" class="btn btn-danger col-4 my-3 me-auto"
                    onclick="window.location.href='../index.php'"> Wyjdz
            </button>
            <button id="accept" type="submit" class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                    onclick="return finalize();">Zaakceptuj
            </button>
        </div>
    </main>
</div>
<script>
    function selected(id) {
        const item = window.deliveries.find(item => item["id"] === id)
        window.choosen_delivery = item
        $("#delivery_sum").text("Dostawa: " + currencyFormatter.format(item["price"]))
        $("#sum").text("RAZEM: " + currencyFormatter.format(item["price"] + parseInt(Cookies.get("basket_sum"))))
    }

    function finalize() {
        Cookies.set("delivery_method", JSON.stringify(window.choosen_delivery))
        if (window.choosen_delivery["prepaid"])
            window.location.href = "summary.php"
        else
            window.location.href = "payment_method.php"
    }

    $(function () {
        $("#basket").text("Koszyk: " + currencyFormatter.format(Cookies.get("basket_sum")))
        $.ajax(ip_address + "/delivery_forms").done(function (data) {
            window.deliveries = data["deliveries"]
            let first = true
            for (let key in data["deliveries"]) {
                const entry = data['deliveries'][key]
                let liel = document.createElement("li")
                let price = currencyFormatter.format(entry["price"])
                let checked = ""
                if (first) {
                    checked = "checked"
                    first = false
                }
                liel.className = "list-group-item border-primary border-top"
                liel.innerHTML = `<input id="item_${entry['id']}" name="delivery_form" class="form-check-input" type="radio" onclick="return selected(${entry['id']});" value="" ${checked}>
                                    <label for="item_${entry['id']}" class="form-check-label" >${entry["name"]} - ${price}</label>`
                $("#delivery").append(liel)
            }
            selected(window.deliveries[0]["id"])
        })
    })
</script>

<?php require_once("../fragments/footer.php"); ?>
