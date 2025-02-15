$(document).ready(function () {
  // Fetch and populate table data
  function fetchTableData() {
    $.ajax({
      url: window.location.href + "/show",
      type: "GET",
      success: function (response) {
        // Populate table body with data
        console.log(response);
        var tbody = $("<tbody>");
        response.forEach(function (kelas) {
          var tr = $("<tr>").attr("data-id", kelas.konfigurasi_waktu_id);
          var th = $("<th>");
          var input = $("<input>").attr({
            type: "text",
            class: "form-control form-control-sm bg-label-primary text-center",
            value: kelas.kelas_nama,
            disabled: true,
          });
          th.append(input);
          tr.append(th);

          var days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
          days.forEach(function (day) {
            var td = $("<td>");
            var div = $("<div>").addClass("d-flex");
            var inputJamMasuk = $("<input>").attr({
              type: "text",
              class:
                "form-control form-control-sm border-success w-50 jam-masuk me-1",
              "data-hari": day,
              "data-field": "jam_masuk",
              "data-konfigurasi-waktu-id": kelas[day]
                ? kelas[day].konfigurasi_waktu_id
                : "",
              value: kelas[day] ? kelas[day].jam_masuk : "",
            });
            var span = $("<span>").addClass("text-muted fw-light").text(" - ");
            var inputJamPulang = $("<input>").attr({
              type: "text",
              class:
                "form-control form-control-sm border-danger w-50 jam-pulang ms-1",
              "data-hari": day,
              "data-field": "jam_pulang",
              "data-konfigurasi-waktu-id": kelas[day]
                ? kelas[day].konfigurasi_waktu_id
                : "",
              value: kelas[day] ? kelas[day].jam_pulang : "",
            });

            // Add focusout event listener to update data
            inputJamMasuk.on("focusout", function () {
              updateData(
                $(this).data("konfigurasi-waktu-id"),
                kelas.kelas_id,
                $(this).data("hari"),
                $(this).data("field"),
                $(this).val()
              );
            });
            inputJamPulang.on("focusout", function () {
              updateData(
                $(this).data("konfigurasi-waktu-id"),
                kelas.kelas_id,
                $(this).data("hari"),
                $(this).data("field"),
                $(this).val()
              );
            });

            div.append(inputJamMasuk).append(span).append(inputJamPulang);
            td.append(div);
            tr.append(td);
          });
          tbody.append(tr);
        });
        $("table").append(tbody);
      },
    });
  }

  // Function to update data
  function updateData(konfigurasiWaktuId, kelasId, hari, field, value) {
    var data = {
      kelas_id: kelasId,
      hari: hari,
    };
    data[field] = value;
    console.log(data);
    $.ajax({
      url: window.location.href + "/update/" + konfigurasiWaktuId,
      type: "PUT",
      data: JSON.stringify(data),
      contentType: "application/json",
      success: function (response) {
        if (response.status === "success") {
          console.log("Data updated successfully");
        }
      },
      error: function (err) {
        console.error(err);
      },
    });
  }

  // Initial fetch of table data
  fetchTableData();
});
