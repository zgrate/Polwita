<?php require_once('../fragments/header.php'); ?>

    <div class="container">
        <main class="align-self-center mx-auto">
            <div class="row g-5 mt-auto justify-content-center">
                <div class="col-sm-8">
                    <h5>E-Recepta</h5>
                    <ul id="receipt" class="row list-group mx-auto">
                    </ul>
                </div>
                <?php include_once("../fragments/koszyk_fragment.php") ?>
            </div>
            <div class="row">
                <button id="cancel" class="btn btn-danger col-4 my-3 me-auto" onclick="return go_back();">Wróć</button>
                <button id="accept" type="submit" disabled class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                        onclick="return submit_koszyk();">Zaakceptuj
                </button>
            </div>
        </main>
    </div>
    <script>

        let requesting = false

        function submit_koszyk() {
            let button = $("#accept")
            requesting = true
            button[0].disabled = true
            button[0].innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>`

            let res = function () {
                button[0].disabled = false
                button[0].innerHTML = `Zaakceptuj`

            }

            let items = window.new_basket_items.map(function (it) {
                return {"id": it["article"]["IdT"], "amount": it["amount"]}
            })
            $.ajax({
                url: ip_address + "/add_to_basket",
                data: JSON.stringify({"user_id": user_id, "items": items}),
                contentType: "application/json; charset=utf-8",
                type: "POST"
            }).done(function () {
                window.location.href = "koszyk.php"
            }).fail(function () {
                res()
                requesting = false
                $("#errorModal").modal("show")
            })
            return false;
        }

        function remove_item(id) {
            if (!requesting) {
                $("#item_" + id).removeClass("bg-light-green")
                const plus = $("#plus_" + id)
                plus.text("+")
                plus[0].onclick = (function () {
                    add_item(id)
                })
                let item = window.receipt.find(function (it) {
                    return it["article"]["IdT"] === id.toString()
                })
                window.new_basket_items = window.new_basket_items.filter(it => it !== item)
                refresh_basket([...window.basket, ...window.new_basket_items])
                enable_button()
            }
        }

        function add_item(id) {
            if (!requesting) {
                $("#item_" + id).addClass("bg-light-green")
                const plus = $("#plus_" + id)
                plus.text("-")
                plus[0].onclick = (function () {
                    remove_item(id)
                })
                let item = window.receipt.find(function (it) {
                    return it["article"]["IdT"] === id.toString()
                })
                window.new_basket_items.push(item)
                refresh_basket([...window.basket, ...window.new_basket_items])
                enable_button()
            }
        }

        function enable_button() {
            $("#accept")[0].disabled = window.new_basket_items.length <= 0;
        }

        function go_back() {
            window.location.href = "index.php"
        }

        $(function () {
            window.new_basket_items = []
            $("#errorModalText").text("Wystąpił błąd w przetwarzaniu E-Recepty! Spróbuj ponownie później!")
            const erecepta_json = (Cookies.get("ereceipt"))
            if (erecepta_json === undefined) {
                $("#receipt").append(document.createTextNode("Wystąpił błąd pobierania E-Recepty"))
            } else {
                let erecepta = JSON.parse(erecepta_json)
                let list = []
                for (const e in erecepta["erecepta"]) {
                    list.push(e)
                }

                $.ajax(ip_address + "/get_articles/" + list.join(",")).done(function (data) {
                    window.receipt = []
                    for (let item in data["articles"]) {
                        let entry = data["articles"][item]
                        window.receipt.push({'article': entry, 'amount': erecepta["erecepta"][parseInt(entry["IdT"])]})
                        let lili = document.createElement("li")
                        let price = currencyFormatter.format(entry["Cena"])
                        console.log(erecepta["erecepta"][parseInt(entry["IdT"])])
                        lili.className = "list-group-item  border-primary border-top"
                        lili.id = `item_${entry["IdT"]}`
                        lili.innerHTML = `<div class="row"> <div class="col-6"> ${entry["Nazwa"]}</div>
                                  <div class="col-2 mx-auto">${erecepta["erecepta"][parseInt(entry["IdT"])]} szt. </div>
                                    <div class="col-2"> ${price}</div> <div id="plus_${entry["IdT"]}" class='col-1 text-primary' style="font-size: 20px" onclick="return add_item(${entry["IdT"]});">&plus;</a>
                                    </div>`
                        $("#receipt").append(lili)
                    }
                }).fail(function () {
                    $("errorModal").modal("show")
                })
            }
        })


    </script>
<?php require_once("../fragments/footer.php"); ?>