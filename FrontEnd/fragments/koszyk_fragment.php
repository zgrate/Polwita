<div class="col-sm flex-row-reverse">
    <h5 class="row" id="suma">KOSZYK</h5>
    <ul id="basket" class="row list-group">
        Ładowanie...
    </ul>
</div>

<script>

    const formatter = new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: "PLN"
    });

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
        console.log(sum)
        $("#suma").text(`KOSZYK: ${formatter.format(sum)} PLN`)
    }

    $(function () {
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
