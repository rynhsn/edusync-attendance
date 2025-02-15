"use strict";

let fv, offCanvasEl, editId = null, $editRow = null, base_url = window.location.href;

$(document).ready(function () {
  const $formMaintainJurusan = $("#form-maintain-jurusan");

  setTimeout(() => {
    const $newRecord = $(".create-new"),
      $offCanvasElement = $("#maintain-jurusan");

    if ($newRecord.length) {
      $newRecord.on("click", function () {
        offCanvasEl = new bootstrap.Offcanvas($offCanvasElement[0]);
        $offCanvasElement.find(".dt-jurusan").val("");
        $offCanvasElement.find(".dt-kode").val("");
        $offCanvasElement.find(".dt-keterangan").val("");
        editId = null;
        offCanvasEl.show();
        $offCanvasElement.find(".dt-jurusan").focus();
      });
    }
  }, 200);

  fv = FormValidation.formValidation($formMaintainJurusan[0], {
    fields: {
      basicJurusan: {
        validators: {
          notEmpty: { message: "Jurusan tidak boleh kosong" },
        },
      },
      basicKode: {
        validators: {
          notEmpty: { message: "Kode tidak boleh kosong" },
        },
      },
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: "",
        rowSelector: ".col-sm-12",
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus(),
    },
    init: (instance) => {
      instance.on("plugins.message.placed", function (e) {
        if ($(e.element).parent().hasClass("input-group")) {
          $(e.element).parent().after(e.messageElement);
        }
      });
    },
  });

  var $dt_jurusan_table = $(".datatables-jurusan"), dt_basic;

  if ($dt_jurusan_table.length) {
    dt_basic = $dt_jurusan_table.DataTable({
      ajax: { url: window.location.href + "/show", dataSrc: "data" },
      columns: [
        { data: "jurusan_nama" },
        { data: "jurusan_kode" },
        { data: "jurusan_keterangan" },
        { data: "created_at" },
        { data: "updated_at" },
        { data: "" },
      ],
      columnDefs: [
        {
          targets: [3, 4],
          render: function (data) {
            if (!data) return "-";
            var date = new Date(data);
            return `${date.getDate().toString().padStart(2, "0")}/${(date.getMonth() + 1).toString().padStart(2, "0")}/${date.getFullYear()} ${date.getHours().toString().padStart(2, "0")}:${date.getMinutes().toString().padStart(2, "0")}`;
          },
        },
        {
          targets: [2, 3, 4],
          render: function (data) {
            return data ? data : "-";
          },
        },
        {
          targets: -1,
          title: "Actions",
          orderable: false,
          searchable: false,
          render: function (data, type, full) {
            return `
              <a href="javascript:;" class="btn btn-sm btn-icon item-delete"><i class="text-danger ti ti-trash"></i></a>
              <a href="javascript:;" class="btn btn-sm btn-icon item-edit" data-jurusan_id="${full.jurusan_id}" data-jurusan_nama="${full.jurusan_nama}" data-jurusan_kode="${full.jurusan_kode}" data-jurusan_keterangan="${full.jurusan_keterangan}"><i class="text-primary ti ti-pencil"></i></a>
            `;
          },
        },
      ],
      order: [[0, "asc"]],
      dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      displayLength: 7,
      lengthMenu: [7, 10, 25, 50, 75, 100],
      buttons: [
        {
          extend: "collection",
          className: "btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light",
          text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
          buttons: [
            {
              extend: "print",
              text: '<i class="ti ti-printer me-1" ></i>Print',
              className: "dropdown-item",
              exportOptions: { columns: [0, 1, 2, 3, 4], format: { body: formatExportBody } },
              customize: customizeExport,
            },
            {
              extend: "csv",
              text: '<i class="ti ti-file-text me-1" ></i>Csv',
              className: "dropdown-item",
              exportOptions: { columns: [0, 1, 2, 3, 4], format: { body: formatExportBody } },
            },
            {
              extend: "excel",
              text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
              className: "dropdown-item",
              exportOptions: { columns: [0, 1, 2, 3, 4], format: { body: formatExportBody } },
            },
            {
              extend: "pdf",
              text: '<i class="ti ti-file-description me-1"></i>Pdf',
              className: "dropdown-item",
              exportOptions: { columns: [0, 1, 2, 3, 4], format: { body: formatExportBody } },
            },
            {
              extend: "copy",
              text: '<i class="ti ti-copy me-1" ></i>Copy',
              className: "dropdown-item",
              exportOptions: { columns: [0, 1, 2, 3, 4], format: { body: formatExportBody } },
            },
          ],
        },
        {
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Data</span>',
          className: "create-new btn btn-primary waves-effect waves-light",
        },
      ],
    });

    $("div.head-label").html('<h5 class="card-title mb-0">Data Jurusan</h5>');
  }

  fv.on("core.form.valid", function () {
    var $jurusan_baru = $(".dt-jurusan").val(),
      $kode_baru = $(".dt-kode").val(),
      $keterangan_baru = $(".dt-keterangan").val();

    if ($kode_baru && $jurusan_baru) {
      var url = editId ? `${window.location.href}/update/${editId}` : `${window.location.href}/create`;
      var type = editId ? "PUT" : "POST";

      $.ajax({
        url: url,
        type: type,
        data: {
          jurusan_nama: $jurusan_baru,
          jurusan_kode: $kode_baru,
          jurusan_keterangan: $keterangan_baru,
        },
        success: function (response) {
          if (editId) {
            dt_basic.row($editRow).data({
              jurusan_id: response.jurusan_id,
              jurusan_nama: $jurusan_baru,
              jurusan_kode: $kode_baru,
              jurusan_keterangan: $keterangan_baru,
              created_at: response.created_at,
              updated_at: response.updated_at,
            }).draw();
            showAlert("success", "Berhasil!", "Data berhasil diperbarui.");
          } else {
            dt_basic.row.add({
              jurusan_id: response.jurusan_id,
              jurusan_nama: $jurusan_baru,
              jurusan_kode: $kode_baru,
              jurusan_keterangan: $keterangan_baru,
              created_at: response.created_at,
              updated_at: response.updated_at,
            }).draw();
            showAlert("success", "Berhasil!", "Data berhasil dibuat.");
          }

          offCanvasEl.hide();
          $formMaintainJurusan[0].reset();
          editId = null;
          $editRow = null;
        },
        error: function (xhr) {
          console.error(xhr.responseText);
          showAlert("error", "Gagal", "Data gagal disimpan!");
        },
      });
    }
  });

  $dt_jurusan_table.on("click", ".item-edit", function () {
    $(".offcanvas-title").text("Edit Data Jurusan");
    $editRow = $(this).closest("tr");
    var data = dt_basic.row($editRow).data();

    $(".dt-jurusan").val(data.jurusan_nama);
    $(".dt-kode").val(data.jurusan_kode);
    $(".dt-keterangan").val(data.jurusan_keterangan);
    editId = data.jurusan_id;

    offCanvasEl = new bootstrap.Offcanvas($("#maintain-jurusan")[0]);
    offCanvasEl.show();
  });

  $dt_jurusan_table.on("click", ".item-delete", function () {
    var $row = $(this).closest("tr");
    var data = dt_basic.row($row).data();

    Swal.fire({
      title: "Apakah Anda yakin?",
      text: "Data ini akan dihapus secara permanen!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
      customClass: {
        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
        cancelButton: "btn btn-label-secondary waves-effect waves-light",
      },
      buttonsStyling: false,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `${window.location.href}/delete/${data.jurusan_id}`,
          type: "DELETE",
          success: function () {
            dt_basic.row($row).remove().draw();
            showAlert("success", "Dihapus!", "Data berhasil dihapus.");
          },
          error: function (xhr) {
            console.error(xhr.responseText);
            showAlert("error", "Gagal", "Data gagal dihapus!");
          },
        });
      } else {
        showAlert("error", "Batal", "Data tidak jadi dihapus!");
      }
    });
  });
});

function formatExportBody(inner) {
  if (!inner) return inner;
  var el = $.parseHTML(inner);
  var result = "";
  $.each(el, function (index, item) {
    if ($(item).hasClass("user-name")) {
      result += $(item).find("firstChild").text();
    } else if (item.innerText === undefined) {
      result += item.textContent;
    } else {
      result += item.innerText;
    }
  });
  return result;
}

function customizeExport(win) {
  $(win.document.body)
    .css("color", config.colors.headingColor)
    .css("border-color", config.colors.borderColor)
    .css("background-color", config.colors.bodyBg);
  $(win.document.body)
    .find("table")
    .addClass("compact")
    .css("color", "inherit")
    .css("border-color", "inherit")
    .css("background-color", "inherit");
}

function showAlert(icon, title, text) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    customClass: {
      confirmButton: "btn btn-success waves-effect waves-light",
    },
  });
}
