<?php require_once('../fragments/header.php'); ?>


    <form name="erecepta" action="#" onsubmit="return submit_erecepte();">
        <div class="container">
            <main class="align-self-center mx-auto">
                <div class="row g-5 mt-auto justify-content-center">
                    <div class="col-sm-8">
                        <h1 class="row col-9">Wprowadź E-Recepte</h1>
                        <label for="erec_number" class="form-label row">Number E-Recepty</label>
                        <input id="erec_number" type="text" onkeyup="return validate_code();"
                               class="form-control row w-25" name="erec_number" minlength="4" maxlength="4"/>
                        <label for="pesel" class="form-label row col-4 mt-3">PESEL</label>
                        <input id="pesel" type="text" onkeyup="return validate_pesel();" class="form-control row w-50"
                               maxlength="11" name="pesel"/>

                    </div>
                    <?php include_once("../fragments/koszyk_fragment.php"); ?>
                </div>
                <div class="row">
                    <input id="cancel" type="reset" class="btn btn-danger col-4 my-3 me-auto">
                    <button id="accept" type="submit" disabled
                            class="btn btn-primary ms-auto col-4 my-3 align-self-end">Pobierz E-Recepte
                    </button>
                </div>
            </main>
        </div>
    </form>

    <script type="application/javascript">
        let requesting = false

        function submit_erecepte() {
            let button = $("#accept")
            let erec = $("#erec_number")
            let pesel = $("#pesel")
            requesting = true
            button[0].disabled = true
            button[0].innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>`

            erec[0].readOnly = true
            pesel[0].readOnly = true

            let res = function () {
                button[0].disabled = false
                button[0].innerHTML = `Pobierz E-Recepte`
                erec[0].readOnly = false
                pesel[0].readOnly = false
            }
            $.ajax(ip_address + "/erecepta?number=" + erec.val() + "&pesel=" + pesel.val()).done(function (data) {
                console.log(data)
                Cookies.set("ereceipt", JSON.stringify(data))
                window.location.href = "e-recepta.php"
            }).fail(function () {
                res()
                requesting = false
                $("#errorModal").modal("show")

            })
            return false;
        }

        function isValidPesel(pesel) {
            let weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];
            let sum = 0;
            let controlNumber = parseInt(pesel.substring(10, 11));

            for (let i = 0; i < weight.length; i++) {
                sum += (parseInt(pesel.substring(i, i + 1)) * weight[i]);
            }
            sum = sum % 10;
            return (10 - sum) % 10 === controlNumber;
        }

        function validate_pesel() {
            if (isValidPesel($("#pesel").val())) {
                $("#pesel").removeClass("border-danger")
            } else {
                $("#pesel").addClass("border-danger")
            }
            typing_fun()
        }

        function validate_code() {
            if ($("#erec_number").val().length === 4) {
                $("#erec_number").removeClass("border-danger")
            } else {
                $("#erec_number").addClass("border-danger")
            }
            typing_fun()
        }

        function typing_fun() {
            if (!requesting && $("#erec_number").val().length === 4 && $("#pesel").val().length === 11 && isValidPesel($("#pesel").val())) {
                $("#accept")[0].disabled = false
            } else {
                $("#accept")[0].disabled = true

            }

        }

        $(function () {

            console.log(ip_address);
            typing_fun()
            $("errorModalText").text("Wystąpił błąd w pobieraniu E-Recepty! Sprawdz poprawność kodu oraz spróbuj ponownie później!")

        })

    </script>
<?php require_once("../fragments/footer.php"); ?>