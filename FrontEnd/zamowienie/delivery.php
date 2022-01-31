<?php require_once('../fragments/header.php'); ?>

<div class="container">
    <main class="align-self-center mx-auto">
        <div class="row g-5 mt-auto justify-content-center">
            <div class="col-8 flex-row-reverse">
                <div class="row">
                    <form class="col-5">
                        <h3>Dostawa</h3>
                        <label for="name" class="my-2 form-label">Imie Nazwisko</label>
                        <input id="name" class="form-control" onkeyup="return check_empty('#name')" value=""
                               placeholder="Imie Nazwisko">
                        <label for="address" class="my-2 form-label">Adres</label>
                        <input id="address" class="form-control" onkeyup="return check_empty('#address')" value=""
                               placeholder="Adres">
                        <label for="postalcode" class="my-2 form-label">Kod Pocztowy</label>
                        <input id="postalcode" class="form-control" onkeyup="return check_empty('#postalcode')" value=""
                               placeholder="Kod Pocztowy">
                        <label for="city" class="my-2 form-label">Miejscowość</label>
                        <input id="city" class="form-control" onkeyup="return check_empty('#city')" value=""
                               placeholder="Miejscowość">
                    </form>
                </div>

            </div>
            <div class="col-4 ">
                <div class="row  border border-primary p-2">
                    <h4 class="text-center">Podsumowanie Koszyka</h4>
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
            <button id="accept" type="submit" disabled class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                    onclick="return finalize();">Zaakceptuj
            </button>
        </div>
    </main>
</div>
<script>

    function finalize() {
        Cookies.set("delivery", JSON.stringify({
            "name": $("#name").val(),
            "address": $("#address").val(),
            "postal": $("#postalcode").val(),
            "city": $("#city").val()
        }))
        window.location.href = "delivery_form.php"
    }

    function check_empty(id) {
        let obj = $(id)
        if (obj.val().length > 0) {
            obj.removeClass("border-danger")
        } else {
            obj.addClass("border-danger")
        }
        check_all()
    }

    function check_all() {
        if ($("#name").val().length > 0 && $("#address").val().length > 0 && $("#postalcode").val().length > 0 && $("#city").val().length > 0) {
            $("#accept").prop("disabled", false)
        } else {
            $("#accept").prop("disabled", true)

        }
    }

    window.user_found = function () {
        console.log(window.user)
        if (window.user !== undefined) {
            $("#name").val(window.user["Imie"] + " " + window.user["Nazwisko"])
        }
        check_all()
    }
    $(function () {
        $("#sum").text("RAZEM: " + currencyFormatter.format(Cookies.get("basket_sum")))

    })
</script>

<?php require_once("../fragments/footer.php"); ?>
