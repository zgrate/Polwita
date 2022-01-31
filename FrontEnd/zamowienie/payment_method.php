<?php require_once('../fragments/header.php'); ?>

    <div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addcard"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dodaj nową kartę!</h5>
                    <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close"
                            onclick="$('#addCardModal').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mx-auto">
                            <input class="form-check border" id="cardNr" placeholder="Numer karty">
                        </div>
                        <div class="row mx-auto">
                            <input class="me-2 col form-check border" id="expirationDate" placeholder="MM/YY"
                                   maxlength="5">
                            <input class="ms-2 col form-check border" id="cvv" placeholder="CVV" maxlength="3">
                        </div>
                        <div class="text-center my-2">
                            <input class="mx-auto btn btn-primary" value="Dodaj" onclick="return add_card_btn()">
                            <input class="mx-auto btn btn-danger" value="Anuluj"
                                   onclick="$('#addCardModal').modal('hide')">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <main class="align-self-center mx-auto">
            <div class="row g-5 mt-auto justify-content-center">
                <div class="col-8 flex-row-reverse">
                    <div class="row">
                        <form class="col-8" action="#">
                            <div class="row my-2">
                                <h3 class="col-8">Sposób płatności</h3>
                                <button class="col-4 btn btn-primary" onclick="return add_card()">Dodaj kartę</button>
                            </div>
                            <ul id='cards' class="row list-group ">
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

        function finalize() {
            Cookies.set("payment_method", JSON.stringify(window.payment_method))
            window.location.href = "summary.php"
        }

        function selected(id) {
            window.payment_method = window.payments.find(item => item["id"] === id)
        }

        function add_card() {
            $('#addCardModal').modal("show")
        }

        function add_card_btn() {
            const cardNrObj = $("#cardNr")
            const mmyyObj = $("#expirationDate")
            const cvvObj = $("#cvv")

            const mmddRegex = new RegExp("[0-9][0-9]/[0-9][0-9]");
            const cvvRegex = new RegExp("[0-9][0-9][0-9]");
            let valid = true

            if (!validateCardNumber(cardNrObj.val())) {
                cardNrObj.addClass("border-danger")
                valid = false
            } else {
                cardNrObj.removeClass("border-danger")
            }

            if (!mmddRegex.test(mmyyObj.val())) {
                mmyyObj.addClass("border-danger")
                valid = false

            } else {
                mmyyObj.remove("border-danger")
            }

            if (!cvvRegex.test(cvvObj.val())) {
                valid = false
                cvvObj.addClass("border-danger")
            } else {
                cvvObj.removeClass("border-danger")
            }
            if (valid) {
                window.payment_method = {
                    'type': 'card',
                    'four-digits': cardNrObj.val().slice(-4),
                    'card': {
                        'number': cardNrObj.val(),
                        'expiration': mmyyObj.val(),
                        'cvv': cvvObj.val()
                    },
                    'id': Math.floor(Math.random() * 200)
                }
                window.payments.push(window.payment_method)
                refreshPayments()
                $('#item_' + window.payment_method["id"]).click()
                $('#addCardModal').modal('hide')
            }
        }

        function refreshPayments() {
            $("#cards")[0].innerHTML = ""
            let first = true
            for (let key in window.payments) {
                const entry = window.payments[key]
                let liel = document.createElement("li")
                let name = entry["name"]
                if (entry["type"] === "card") {
                    name = "Karta o ostatnich cyfrach " + entry["four-digits"]
                }
                let checked = ""
                if (first) {
                    checked = "checked"
                    first = false
                }

                liel.className = "list-group-item border-primary border-top"
                liel.innerHTML = `<input id="item_${entry['id']}" name="delivery_form" class="form-check-input" type="radio" onclick="return selected(${entry['id']});" value="" ${checked}>
                                            <label for="item_${entry['id']}" class="form-check-label" >${name}</label>`
                $("#cards").append(liel)
            }
        }

        $(function () {
            window.delivery_method = JSON.parse(Cookies.get("delivery_method"))
            $("#basket").text("Koszyk: " + currencyFormatter.format(Cookies.get("basket_sum")))
            $("#delivery_sum").text("Dostawa: " + currencyFormatter.format(window.delivery_method["price"]))
            $("#sum").text("RAZEM: " + currencyFormatter.format(window.delivery_method["price"] + parseInt(Cookies.get("basket_sum"))))
            $.ajax(ip_address + "/payments/" + user_id).done(function (data) {
                window.payments = data["payments"]
                refreshPayments()
                selected(window.payments[0]["id"])
            })
        })


        const validateCardNumber = number => {
            //Check if the number contains only numeric value
            //and is of between 13 to 19 digits
            const regex = new RegExp("^[0-9]{13,19}$");
            if (!regex.test(number)) {
                return false;
            }

            return luhnCheck(number);
        }

        const luhnCheck = val => {
            let checksum = 0; // running checksum total
            let j = 1; // takes value of 1 or 2

            // Process each digit one by one starting from the last
            for (let i = val.length - 1; i >= 0; i--) {
                let calc = 0;
                // Extract the next digit and multiply by 1 or 2 on alternative digits.
                calc = Number(val.charAt(i)) * j;

                // If the result is in two digits add 1 to the checksum total
                if (calc > 9) {
                    checksum = checksum + 1;
                    calc = calc - 10;
                }

                // Add the units element to the checksum total
                checksum = checksum + calc;

                // Switch the value of j
                if (j === 1) {
                    j = 2;
                } else {
                    j = 1;
                }
            }

            //Check if it is divisible by 10 or not.
            return (checksum % 10) === 0;
        }

    </script>
<?php require_once("../fragments/footer.php"); ?>