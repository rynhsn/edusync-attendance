"use strict";

let fv,
  offCanvasEl,
  editId = null,
  $editRow = null; // Tambahkan $editRow untuk menyimpan referensi ke baris yang sedang diedit

document.addEventListener("DOMContentLoaded", function (e) {
  (function () {
    const formMaintainSiswa = document.getElementById("form-maintain-siswa");

    setTimeout(() => {
      const newRecord = document.querySelector(".create-new"),
        offCanvasElement = document.querySelector("#maintain-siswa");

      // To open offCanvas, to add new record
      if (newRecord) {
        newRecord.addEventListener("click", function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          // Empty fields on offCanvas open
          offCanvasElement.querySelector(".dt-nama-siswa").value = "";
          offCanvasElement.querySelector(".dt-nis").value = "";
          offCanvasElement.querySelector(".dt-nisn").value = "";
          offCanvasElement.querySelector(".dt-jenis-kelamin").value = "";
          offCanvasElement.querySelector(".dt-tempat-lahir").value = "";
          offCanvasElement.querySelector(".dt-tanggal-lahir").value = "";
          offCanvasElement.querySelector(".dt-alamat").value = "";
          offCanvasElement.querySelector(".dt-rfid-tag").value = "";
          offCanvasElement.querySelector(".dt-kelas").value = "";
          // Reset editId saat menambah data baru
          editId = null;
          // Open offCanvas with form
          offCanvasEl.show();
          // Focus on nama siswa input
          offCanvasElement.querySelector(".dt-nama-siswa").focus();
        });
      }
    }, 200);

    // Form validation for Add new record
    fv = FormValidation.formValidation(formMaintainSiswa, {
      fields: {
        nama_siswa: {
          validators: {
            notEmpty: {
              message: "Nama siswa harus diisi",
            },
          },
        },
        nis: {
          validators: {
            notEmpty: {
              message: "NIS harus diisi",
            },
          },
        },
        nisn: {
          validators: {
            notEmpty: {
              message: "NISN harus diisi",
            },
          },
        },
        jenis_kelamin: {
          validators: {
            notEmpty: {
              message: "Jenis kelamin harus diisi",
            },
          },
        },
        tempat_lahir: {
          validators: {
            notEmpty: {
              message: "Tempat lahir harus diisi",
            },
          },
        },
        tanggal_lahir: {
          validators: {
            notEmpty: {
              message: "Tanggal lahir harus diisi",
            },
          },
        },
        alamat: {
          validators: {
            notEmpty: {
              message: "Alamat harus diisi",
            },
          },
        },
        rfid_tag: {
          validators: {
            notEmpty: {
              message: "RFID Tag harus diisi",
            },
          },
        },
        kelas_id: {
          validators: {
            notEmpty: {
              message: "Kelas harus diisi",
            },
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
  var dt_siswa_table = $(".datatables-siswa"),
    dt_basic;

  // DataTable with buttons
  if (dt_siswa_table.length) {
    dt_basic = dt_siswa_table.DataTable({
      ajax: {
        url: window.location.href + "/show",
        dataSrc: "data",
      },
      columns: [
        { data: "" },
        { data: "" },
        { data: null },
        { data: "nama_siswa" },
        { data: "nis" },
        { data: "nisn" },
        { data: "jk_display" },
        { data: "tempat_lahir" },
        { data: "tanggal_lahir" },
        { data: "alamat" },
        { data: "rfid_tag" },
        { data: "kelas" },
      ],
      columnDefs: [
        {
          // For Responsive
          className: "control",
          orderable: false,
          searchable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return "";
          },
        },
        {
          targets: 2,
          orderable: true,
          searchable: false,
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          targets: 1,
          title: "Actions",
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return `
              <a href="javascript:;" class="btn btn-sm btn-icon item-delete"><i class="text-danger ti ti-trash"></i></a>
              <a href="javascript:;" class="btn btn-sm btn-icon item-edit" data-id="${full.siswa_id}" data-nama_siswa="${full.nama_siswa}" data-nis="${full.nis}" data-nisn="${full.nisn}" data-jk="${full.jk}" data-tempat_lahir="${full.tempat_lahir}" data-tanggal_lahir="${full.tanggal_lahir}" data-alamat="${full.alamat}" data-rfid_tag="${full.rfid_tag}" data-kelas="${full.kelas}"><i class="text-primary ti ti-pencil"></i></a>
            `;
          },
        },
      ],
      order: [[2, "asc"]],
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
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
              },
            },
            {
              extend: "csv",
              text: '<i class="ti ti-file-text me-1" ></i>Csv',
              className: "dropdown-item",
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
              },
            },
            {
              extend: "excel",
              text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
              className: "dropdown-item",
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
              },
            },
            {
              extend: "pdf",
              text: '<i class="ti ti-file-description me-1"></i>Pdf',
              className: "dropdown-item",
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
              },
            },
            {
              extend: "copy",
              text: '<i class="ti ti-copy me-1" ></i>Copy',
              className: "dropdown-item",
              exportOptions: {
                columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
              },
            },
          ],
        },
        {
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Tambah Data</span>',
          className: "create-new btn btn-primary waves-effect waves-light",
        },
      ],
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return "Details of " + data["nama_siswa"];
            },
          }),
          type: "column",
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    "<td>" +
                    col.title +
                    ":" +
                    "</td> " +
                    "<td>" +
                    col.data +
                    "</td>" +
                    "</tr>"
                : "";
            }).join("");

            return data
              ? $('<table class="table"/><tbody />').append(data)
              : false;
          },
        },
      },
    });
    $("div.head-label").html('<h5 class="card-title mb-0">Data Siswa</h5>');
  }

  // Add New record
  // On form submit, if form is valid
  fv.on("core.form.valid", function () {
    var $nama_siswa_baru = document.querySelector(".dt-nama-siswa").value,
      $nis_baru = document.querySelector(".dt-nis").value,
      $nisn_baru = document.querySelector(".dt-nisn").value,
      $jenis_kelamin_baru = document.querySelector(".dt-jenis-kelamin").value,
      $tempat_lahir_baru = document.querySelector(".dt-tempat-lahir").value,
      $tanggal_lahir_baru = document.querySelector(".dt-tanggal-lahir").value,
      $alamat_baru = document.querySelector(".dt-alamat").value,
      $rfid_tag_baru = document.querySelector(".dt-rfid-tag").value,
      $kelas_baru = document.querySelector(".dt-kelas").value;

    if ($nama_siswa_baru != "" && $nis_baru != "") {
      var url = editId
        ? window.location.href + "/update/" + editId
        : window.location.href + "/create";
      var type = editId ? "PUT" : "POST";

      $.ajax({
        url: url,
        type: type,
        data: {
          nama_siswa: $nama_siswa_baru,
          nis: $nis_baru,
          nisn: $nisn_baru,
          jk: $jenis_kelamin_baru,
          tempat_lahir: $tempat_lahir_baru,
          tanggal_lahir: $tanggal_lahir_baru,
          alamat: $alamat_baru,
          rfid_tag: $rfid_tag_baru,
          kelas: $kelas_baru,
        },
        success: function (response) {
          if (editId) {
            // Update data in DataTable
            dt_basic
              .row($editRow)
              .data({
                siswa_id: response.id, // Pastikan ID disimpan
                nama_siswa: $nama_siswa_baru,
                nis: $nis_baru,
                nisn: $nisn_baru,
                jk: $jenis_kelamin_baru,
                tempat_lahir: $tempat_lahir_baru,
                tanggal_lahir: $tanggal_lahir_baru,
                alamat: $alamat_baru,
                rfid_tag: $rfid_tag_baru,
                kelas: $kelas_baru,
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
                siswa_id: response.id, // Pastikan ID disimpan
                nama_siswa: $nama_siswa_baru,
                nis: $nis_baru,
                nisn: $nisn_baru,
                jk: $jenis_kelamin_baru,
                tempat_lahir: $tempat_lahir_baru,
                tanggal_lahir: $tanggal_lahir_baru,
                alamat: $alamat_baru,
                rfid_tag: $rfid_tag_baru,
                kelas: $kelas_baru,
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
          $("#form-maintain-siswa")[0].reset();
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
  $(".datatables-siswa tbody").on("click", ".item-edit", function () {
    console.log("Edit");
    // ubah judul offcanvas
    document.querySelector(".offcanvas-title").innerHTML = "Edit Data Siswa";
    console.log("Edit");

    $editRow = $(this).closest("tr"); // Simpan referensi ke baris yang sedang diedit
    var data = dt_basic.row($editRow).data();

    // Set values in the form
    document.querySelector(".dt-nama-siswa").value = data.nama_siswa;
    document.querySelector(".dt-nis").value = data.nis;
    document.querySelector(".dt-nisn").value = data.nisn;
    document.querySelector(".dt-jenis-kelamin").value = data.jk;
    document.querySelector(".dt-tempat-lahir").value = data.tempat_lahir;
    document.querySelector(".dt-tanggal-lahir").value = data.tanggal_lahir;
    document.querySelector(".dt-alamat").value = data.alamat;
    document.querySelector(".dt-rfid-tag").value = data.rfid_tag;
    document.querySelector(".dt-kelas").value = data.kelas;

    // Set editId
    editId = data.id;

    // Show the offcanvas
    offCanvasEl = new bootstrap.Offcanvas(
      document.getElementById("maintain-siswa")
    );
    offCanvasEl.show();
  });

  // Delete Record
  $(".datatables-siswa tbody").on("click", ".item-delete", function () {
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
          url: window.location.href + "/delete/" + data.id,
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
