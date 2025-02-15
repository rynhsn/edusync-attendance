/**
 * DataTables Basic
 */

"use strict";

let fv,
  offCanvasEl,
  editId = null,
  $editRow = null; // Tambahkan $editRow untuk menyimpan referensi ke baris yang sedang diedit

document.addEventListener("DOMContentLoaded", function (e) {
  (function () {
    const formMaintainKelas = document.getElementById("form-maintain-kelas");

    setTimeout(() => {
      const newRecord = document.querySelector(".create-new"),
        offCanvasElement = document.querySelector("#maintain-kelas");

      // To open offCanvas, to add new record
      if (newRecord) {
        newRecord.addEventListener("click", function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          // Empty fields on offCanvas open
          (offCanvasElement.querySelector(".dt-tingkat").value = ""),
            (offCanvasElement.querySelector(".dt-nama-kelas").value = ""),
            (offCanvasElement.querySelector(".dt-keterangan").value = "");
          // Reset editId saat menambah data baru
          editId = null;
          // Open offCanvas with form
          offCanvasEl.show();
          // Focus on tingkat input
          offCanvasElement.querySelector(".dt-tingkat").focus();
        });
      }
    }, 200);

    // Form validation for Add new record
    fv = FormValidation.formValidation(formMaintainKelas, {
      fields: {
        basicTingkat: {
          validators: {
            notEmpty: {
              message: "Tingkat tidak boleh kosong",
            },
          },
        },
        basicName: {
          validators: {
            notEmpty: {
              message: "Nama Kelas tidak boleh kosong",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: "",
          rowSelector: ".col-sm-12",
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus(),
      },
      init: (instance) => {
        instance.on("plugins.message.placed", function (e) {
          if (e.element.parentElement.classList.contains("input-group")) {
            e.element.parentElement.insertAdjacentElement(
              "afterend",
              e.messageElement
            );
          }
        });
      },
    });
  })();
});

// datatable (jquery)
$(function () {
  var dt_kelas_table = $(".datatables-kelas"),
    dt_basic;

  // DataTable with buttons
  // --------------------------------------------------------------------

  if (dt_kelas_table.length) {
    dt_basic = dt_kelas_table.DataTable({
      ajax: {
        url: window.location.href + "/show",
        dataSrc: "data",
      },
      columns: [
        { data: "kelas_tingkat" },
        { data: "kelas_nama" },
        { data: "kelas_keterangan" },
        { data: "created_at" },
        { data: "updated_at" },
        { data: "" },
      ],
      columnDefs: [
        //kolom 3 dan 4 adalah tanggal, buat formatnya menjadi dd/MM/yyyy HH:mm, tapi dari server datangnya dalam format datetime, tidak perlu menggunakan timezone
        {
          targets: [3, 4],
          render: function (data, type, full, meta) {
            if (data == null || data.length == 0) return "-";
            var date = new Date(data);
            return (
              date.getDate().toString().padStart(2, "0") +
              "/" +
              (date.getMonth() + 1).toString().padStart(2, "0") +
              "/" +
              date.getFullYear() +
              " " +
              date.getHours().toString().padStart(2, "0") +
              ":" +
              date.getMinutes().toString().padStart(2, "0")
            );
          },
        },

        //buat agar kolom timestamp dipastikan selalu tidak kekecilan
        {
          targets: [2, 3, 4],
          render: function (data, type, full, meta) {
            // data null or empty string = '-'
            return data == null || data.length == 0 ? "-" : data;
          },
        },
        {
          // Actions
          targets: -1,
          title: "Actions",
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return `
                    <a href="javascript:;" class="btn btn-sm btn-icon item-delete"><i class="text-danger ti ti-trash"></i></a>
                    <a href="javascript:;" class="btn btn-sm btn-icon item-edit" data-kelas_id="${full.kelas_id}" data-kelas_nama="${full.kelas_nama}" data-kelas_tingkat="${full.kelas_tingkat}" data-kelas_keterangan="${full.kelas_keterangan}"><i class="text-primary ti ti-pencil"></i></a>
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
          className:
            "btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light",
          text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
          buttons: [
            {
              extend: "print",
              text: '<i class="ti ti-printer me-1" ></i>Print',
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3, 4],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function (index, item) {
                      if (
                        item.classList !== undefined &&
                        item.classList.contains("user-name")
                      ) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  },
                },
              },
              customize: function (win) {
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
              },
            },
            {
              extend: "csv",
              text: '<i class="ti ti-file-text me-1" ></i>Csv',
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3, 4],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function (index, item) {
                      if (
                        item.classList !== undefined &&
                        item.classList.contains("user-name")
                      ) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  },
                },
              },
            },
            {
              extend: "excel",
              text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3, 4],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function (index, item) {
                      if (
                        item.classList !== undefined &&
                        item.classList.contains("user-name")
                      ) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  },
                },
              },
            },
            {
              extend: "pdf",
              text: '<i class="ti ti-file-description me-1"></i>Pdf',
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3, 4],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function (index, item) {
                      if (
                        item.classList !== undefined &&
                        item.classList.contains("user-name")
                      ) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  },
                },
              },
            },
            {
              extend: "copy",
              text: '<i class="ti ti-copy me-1" ></i>Copy',
              className: "dropdown-item",
              exportOptions: {
                columns: [0, 1, 2, 3, 4],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = "";
                    $.each(el, function (index, item) {
                      if (
                        item.classList !== undefined &&
                        item.classList.contains("user-name")
                      ) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  },
                },
              },
            },
          ],
        },
        {
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Data</span>',
          className: "create-new btn btn-primary waves-effect waves-light",
        },
      ],
      // responsive: {
      //   details: {
      //     display: $.fn.dataTable.Responsive.display.modal({
      //       header: function (row) {
      //         var data = row.data();
      //         return "Details of " + data["nama_kelas"];
      //       },
      //     }),
      //     renderer: function (api, rowIdx, columns) {
      //       var data = $.map(columns, function (col, i) {
      //         return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
      //           ? '<tr data-dt-row="' +
      //               col.rowIndex +
      //               '" data-dt-column="' +
      //               col.columnIndex +
      //               '">' +
      //               "<td>" +
      //               col.title +
      //               ":" +
      //               "</td> " +
      //               "<td>" +
      //               col.data +
      //               "</td>" +
      //               "</tr>"
      //           : "";
      //       }).join("");

      //       return data
      //         ? $('<table class="table"/><tbody />').append(data)
      //         : false;
      //     },
      //   },
      // },
    });
    $("div.head-label").html('<h5 class="card-title mb-0">Data Kelas</h5>');
  }

  // Add New record
  // On form submit, if form is valid
  fv.on("core.form.valid", function () {
    var $nama_kelas_baru = document.querySelector(".dt-nama-kelas").value,
      $tingkat_baru = document.querySelector(".dt-tingkat").value,
      $keterangan_baru = document.querySelector(".dt-keterangan").value;

    if ($tingkat_baru != "" && $nama_kelas_baru != "") {
      var url = editId
        ? window.location.href + "/update/" + editId
        : window.location.href + "/create";
      var type = editId ? "PUT" : "POST";

      $.ajax({
        url: url,
        type: type,
        data: {
          kelas_tingkat: $nama_kelas_baru,
          kelas_nama: $tingkat_baru,
          kelas_keterangan: $keterangan_baru,
        },
        success: function (response) {
          if (editId) {
            // Update data in DataTable
            dt_basic
              .row($editRow)
              .data({
                kelas_id: response.kelas_id, // Pastikan ID disimpan
                kelas_tingkat: $tingkat_baru,
                kelas_nama: $nama_kelas_baru,
                kelas_keterangan: $keterangan_baru,
                created_at: response.created_at,
                updated_at: response.updated_at,
              })
              .draw();
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: "Data berhasil diperbarui.",
              customClass: {
                confirmButton: "btn btn-success waves-effect waves-light",
              },
            });
          } else {
            // Tambahkan data ke DataTable
            dt_basic.row
              .add({
                kelas_id: response.kelas_id, // Pastikan ID disimpan
                kelas_tingkat: $tingkat_baru,
                kelas_nama: $nama_kelas_baru,
                kelas_keterangan: $keterangan_baru,
                created_at: response.created_at,
                updated_at: response.updated_at,
              })
              .draw();

            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: "Data berhasil dibuat.",
              customClass: {
                confirmButton: "btn btn-success waves-effect waves-light",
              },
            });
          }

          // Hide offcanvas using javascript method
          offCanvasEl.hide();

          // Reset form
          $("#form-maintain-kelas")[0].reset();
          editId = null; // Reset editId setelah submit
          $editRow = null; // Reset $editRow setelah submit
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          Swal.fire({
            title: "Gagal",
            text: "Data gagal disimpan!",
            icon: "error",
            customClass: {
              confirmButton: "btn btn-success waves-effect waves-light",
            },
          });
        },
      });
    }
  });

  // Edit Record
  $(".datatables-kelas tbody").on("click", ".item-edit", function () {
    // ubaj judul offcanvas
    document.querySelector(".offcanvas-title").innerHTML = "Edit Data Kelas";

    $editRow = $(this).closest("tr"); // Simpan referensi ke baris yang sedang diedit
    var data = dt_basic.row($editRow).data();

    // Set values in the form
    document.querySelector(".dt-tingkat").value = data.kelas_tingkat;
    document.querySelector(".dt-nama-kelas").value = data.kelas_nama;
    document.querySelector(".dt-keterangan").value = data.kelas_keterangan;

    // Set editId
    editId = data.kelas_id;

    // Show the offcanvas
    offCanvasEl = new bootstrap.Offcanvas(
      document.getElementById("maintain-kelas")
    );
    offCanvasEl.show();
  });

  // Delete Record
  $(".datatables-kelas tbody").on("click", ".item-delete", function () {
    var $row = $(this).closest("tr");
    var data = dt_basic.row($row).data();

    // Tampilkan konfirmasi SweetAlert sebelum menghapus data
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
        // Lakukan penghapusan data jika dikonfirmasi
        $.ajax({
          url: window.location.href + "/delete/" + data.kelas_id,
          type: "DELETE",
          success: function (response) {
            dt_basic.row($row).remove().draw();
            Swal.fire({
              icon: "success",
              title: "Dihapus!",
              text: "Data berhasil dihapus.",
              customClass: {
                confirmButton: "btn btn-success waves-effect waves-light",
              },
            });
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            Swal.fire({
              title: "Gagal",
              text: "Data gagal dihapus!",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success waves-effect waves-light",
              },
            });
          },
        });
      } else {
        Swal.fire({
          title: "Batal",
          text: "Data tidak jadi dihapus!",
          icon: "error",
          customClass: {
            confirmButton: "btn btn-success waves-effect waves-light",
          },
        });
      }
    });
  });
});
