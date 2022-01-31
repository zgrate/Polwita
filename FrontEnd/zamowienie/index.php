<?php require_once('../fragments/header.php'); ?>

    <div class="container">
        <main class="align-self-center mx-auto">
            <div class="row g-5 mt-auto justify-content-center">
                <div class="col-sm flex-row-reverse">
                    <h1 class="row">PODSUMOWANIE KOSZYKA</h1>
                    <ul id="basket" class="row list-group">
                        Ładowanie...
                    </ul>
                </div>
                <div class="row">
                    <div class="col-9"></div>
                    <h2 id="suma" class="col align-self-end my-2">

                    </h2>
                </div>

            </div>
            <div class="row">
                <button id="cancel" type="reset" class="btn btn-danger col-4 my-3 me-auto"
                        onclick="window.location.href='../index.php'"> Wyjdz
                </button>
                <button id="accept" type="submit" class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                        onclick="return finalize();">Finalizuj Zamówienie
                </button>
            </div>
        </main>
    </div>


    <script type="application/javascript">
        $("#errorModal").on("hidden.bs.modal", function () {
            window.location.href = "../login.php"
        })

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
                    let price = currencyFormatter.format(entry["article"]["Cena"])
                    sum += (entry["article"]["Cena"] * entry["amount"])
                    liel.className = "list-group-item border-primary border-top"
                    const fromRec = entry['article']['CzyRecepta'] === "1" ? `<h6 class="">Na Recepte</h6>` : "";

                    liel.innerHTML = `<div class="row"> <div class="col-7"> ${entry["article"]["Nazwa"]}${fromRec}</div> <div class="col-3">

                                        <button class="btn" onclick="return decreaseOne(${entry['article']['IdT']});">▼</button>
                                        <input class="form-control-sm text-center col-lg-2" value='${entry["amount"]}' onchange="return changeTo(${entry['article']['IdT']}, this.value)"/>
                                        <button class="btn" onclick="return increaseOne(${entry['article']['IdT']});">▲</button>
                                    </div>
                                  <div class="col-1">${price}/sztukę</div></div>`
                    $("#basket").append(liel)
                }
            }
            console.log(sum)
            window.sum = sum
            $("#suma").text(`Razem: ${currencyFormatter.format(sum)} PLN`)
        }

        function decreaseOne(id) {
            const item = window.basket.find(item => item["id"] === parseInt(id))
            const newValue = (item["amount"] - 1);
            updateAmount(item["id"], newValue)
        }

        function increaseOne(id) {
            const item = window.basket.find(item => item["id"] === parseInt(id))
            const newValue = (item["amount"] + 1);
            updateAmount(item["id"], newValue)
        }

        function changeTo(id, value) {
            const item = window.basket.find(item => item["id"] === parseInt(id))
            updateAmount(item["id"], value)
        }

        function finalize() {
            Cookies.set("basket_sum", window.sum)
            window.location.href = "delivery.php"
        }

        function updateAmount(id, amount) {
            $.ajax(ip_address + "/update_get_basket/" + user_id + "?article=" + id + "&new_amount=" + amount).done(function (data) {
                console.log(data)
                window.basket = data["basket"]
                refresh_basket(data["basket"])
            }).fail(function () {
                alert("ERROR")
            })
        }

        $(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
                $.ajax(ip_address + "/get_basket/" + user_id).done(function (data) {
                    window.basket = data["basket"]
                    refresh_basket(data["basket"])
                }).fail(function () {
                    $("#errorModalText").text("Wystąpił błąd połączenia!")
                    $("#errorModal").modal("show")
                })
            }
        )
    </script>
<?php require_once("../fragments/footer.php"); ?>