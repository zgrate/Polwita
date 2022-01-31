<?php require_once('../fragments/header.php'); ?>
    <div class="container">
        <main class="align-self-center mx-auto">
            <h1 class="row">PODSUMOWANIE</h1>
            <div class="row g-5 mt-auto justify-content-center">
                <div class="col-sm flex-row-reverse">
                    <ul id="basket" class="row list-group">
                        Ładowanie...
                    </ul>
                    <h6 class="col my-5" id="suma">KOSZYK</h6>

                </div>

                <div class="col">
                    <h5 class="row text-center">Dostawa</h5>
                    <p id='name' class="row text-center"></p>
                    <p id='address' class="row text-center"></p>
                    <p id='city' class="row text-center"></p>
                    <h6 id="delivery-method" class="row"></h6>
                </div>
                <div class="col">
                    <h5 class="row">Sposób płatności</h5>
                    <p id="payment-method" class="row"></p>
                </div>
            </div>
            <h1 class="row ">
                <p id="sum" class="align-self-end my-3 align-self-end ms-auto me-1 text-end">RAZEM
                </p>
            </h1>
            <div class="row">
                <button id="cancel" type="reset" class="btn btn-danger col-4 my-3 me-auto"
                        onclick="window.location.href='../index.php'"> Wyjdz
                </button>
                <button id="accept" type="submit" class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                        onclick="return finalize();">Finalizuj trasakcję!
                </button>
            </div>
        </main>
    </div>


    <script>

        const formatter = new Intl.NumberFormat('pl-PL', {
            style: 'currency',
            currency: "PLN"
        });

        function finalize() {
            let button = $("#accept")
            requesting = true
            button[0].disabled = true
            button[0].innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>`

            let res = function () {
                button[0].disabled = false
                button[0].innerHTML = `Finalizuj trasakcję!`

            }
            $.ajax({
                url: ip_address + `/order/${user_id}`,
                data: JSON.stringify({
                    "name": window.delivery["name"],
                    "address": window.delivery["address"],
                    "postcode": window.delivery["postal"],
                    "city": window.delivery["city"],
                    "delivery_method": window.choosen_delivery,
                    "payment_method": window.payment_method
                }),
                contentType: "application/json; charset=utf-8",
                type: "POST"
            }).done(function () {
                if (window.payment_method === "prepaid") {
                    window.location.href = "success.php"
                } else {
                    window.location.href = "payment.php"
                }
            }).fail(function () {
                res()
                requesting = false
                $("#errorModal").modal("show")
            })
        }

        function refresh_basket(basket) {
            let sum = 0
            $("#basket").text("")
            $("#basket")[0].innerHTML = ""
            if (basket.length === 0) {
                $("#basket").text("Pusty!")
            } else {
                for (let e in basket) {
                    let entry = basket[e]
                    let liel = document.createElement("li")
                    let price = formatter.format(entry["article"]["Cena"])
                    sum += (entry["article"]["Cena"] * entry["amount"])
                    liel.className = "list-group-item  border-primary border-top"
                    liel.innerHTML = `<div class="row"> <div class="col-7"> ${entry["article"]["Nazwa"]}</div>
                                  <div class="col-4">${entry["amount"]} x ${price}</div></div>`
                    $("#basket").append(liel)
                }
            }
            window.basket_price = sum
            $("#suma").text(`KOSZYK: ${formatter.format(sum)}`)
            $("#sum").text("RAZEM: " + formatter.format(window.basket_price + window.choosen_delivery["price"]))

        }

        $(function () {
                $.ajax(ip_address + "/get_basket/" + user_id).done(function (data) {
                    window.basket = data["basket"]
                    refresh_basket(data["basket"])
                }).fail(function () {
                    $("#errorModalText").text("Wystąpił błąd połączenia!")
                    $("#errorModal").modal("show")
                })


                window.choosen_delivery = JSON.parse(Cookies.get("delivery_method"))
                window.delivery = JSON.parse(Cookies.get("delivery"))
                const payment = Cookies.get("payment_method")
                if (payment === undefined) {
                    window.payment_method = "prepaid"
                    $("#payment-method").text("Prepaid")
                } else {
                    window.payment_method = JSON.parse(payment)
                    if (window.payment_method["type"] === "card") $("#payment-method").text("Karta płatniczna o końcówce " + window.payment_method["four-digits"])
                    else $("#payment-method").text(window.payment_method["name"])
                }

                $("#name").text(window.delivery["name"])
                $("#address").text("ul." + window.delivery["address"])
                $("#city").text(window.delivery["postal"] + " " + window.delivery["city"])
                $("#delivery-method").text(window.choosen_delivery["name"] + " (" + formatter.format(window.choosen_delivery["price"]) + ")")


            }
        )
    </script>
<?php require_once("../fragments/footer.php"); ?>