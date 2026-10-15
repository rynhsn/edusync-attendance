$(document).ready(function () {
  const status = $("#time-setting-status");
  const tableBody = $("#time-setting-table tbody");
  const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  const pendingUpdates = [];
  const canManage = status.data("can-manage") === 1 || status.data("can-manage") === "1";
  let saving = false;

  function fetchTableData() {
    $.ajax({
      url: `${window.location.pathname.replace(/\/$/, "")}/show`,
      type: "GET",
      success: function (response) {
        const tbody = $("<tbody>");
        response.forEach(function (kelas) {
          const tr = $("<tr>");
          tr.append($("<th>").attr("scope", "row").text(kelas.kelas_nama));

          days.forEach(function (day) {
            const td = $("<td>");
            const div = $("<div>").addClass("d-flex align-items-center gap-2");
            ["jam_masuk", "jam_pulang"].forEach(function (field) {
              const timeInput = $("<input>").attr({
                type: "time",
                class: `form-control form-control-sm ${field === "jam_masuk" ? "border-success" : "border-danger"}`,
                "aria-label": `${field === "jam_masuk" ? "Jam masuk" : "Jam pulang"} ${kelas.kelas_nama} ${day}`,
                "data-field": field,
                "data-konfigurasi-waktu-id": kelas[day] ? kelas[day].konfigurasi_waktu_id : "",
                "data-original-value": kelas[day] ? kelas[day][field] : "",
                value: kelas[day] ? kelas[day][field] : "",
                disabled: !kelas[day] || !canManage,
              });
              if (canManage) {
                timeInput.on("focusout", function () {
                  updateData(
                    $(this).data("konfigurasi-waktu-id"),
                    kelas.kelas_id,
                    day,
                    field,
                    $(this).val(),
                    $(this)
                  );
                });
              }
              div.append(timeInput);
            });
            td.append(div);
            tr.append(td);
          });
          tbody.append(tr);
        });
        tableBody.empty().append(tbody.children());
        status.text("");
        if (response.length === 0) {
          tableBody.append($("<tr>").append(
            $("<td>").attr("colspan", 7).addClass("text-center text-muted py-4")
              .text("Belum ada pengaturan waktu untuk kelas.")
          ));
        }
      },
      error: function () {
        status.removeClass("text-success").addClass("text-danger")
          .text("Pengaturan waktu gagal dimuat. Muat ulang halaman untuk mencoba kembali.");
        tableBody.html(
          $("<tr>").append(
            $("<td>").attr("colspan", 7).addClass("text-center text-danger py-4")
              .text("Data pengaturan waktu gagal dimuat.")
          )
        );
      }
    });
  }

  function updateData(konfigurasiWaktuId, kelasId, hari, field, value, input) {
    if (value === input.data("original-value")) {
      return;
    }
    if (!konfigurasiWaktuId || value === "") {
      status.removeClass("text-success").addClass("text-danger")
        .text("Jam belum tersedia untuk hari ini atau kolom tidak boleh kosong.");
      return;
    }
    pendingUpdates.push({
      id: konfigurasiWaktuId,
      data: { kelas_id: kelasId, hari: hari, [field]: value },
      input: input,
      value: value,
    });
    saveNext();
  }

  function saveNext() {
    if (saving || pendingUpdates.length === 0) {
      return;
    }

    saving = true;
    const update = pendingUpdates.shift();
    const csrfField = $('input[name="csrf_test_name"]');
    const csrfToken = csrfField.val();
    status.removeClass("text-danger text-success").addClass("text-muted").text("Menyimpan perubahan...");
    $.ajax({
      url: `${window.location.pathname.replace(/\/$/, "")}/update/${update.id}`,
      type: "PUT",
      data: JSON.stringify(update.data),
      contentType: "application/json",
      headers: csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {},
      success: function (response) {
        if (response.status === "success") {
          if (response.csrfHash) {
            csrfField.val(response.csrfHash);
          }
          status.removeClass("text-muted text-danger").addClass("text-success")
            .text("Perubahan berhasil disimpan.");
          update.input.data("original-value", update.value);
          update.input.removeClass("is-invalid").addClass("is-valid");
        } else {
          if (response.csrfHash) {
            csrfField.val(response.csrfHash);
          }
          status.removeClass("text-muted text-success").addClass("text-danger")
            .text(response.message || "Perubahan gagal disimpan.");
          update.input.addClass("is-invalid");
        }
      },
      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.csrfHash) {
          csrfField.val(xhr.responseJSON.csrfHash);
        }
        const message = (xhr.responseJSON && xhr.responseJSON.message) || "Perubahan gagal disimpan. Periksa koneksi lalu coba kembali.";
        status.removeClass("text-muted text-success").addClass("text-danger").text(message);
        update.input.addClass("is-invalid");
      },
      complete: function () {
        saving = false;
        saveNext();
      },
    });
  }

  fetchTableData();
});
