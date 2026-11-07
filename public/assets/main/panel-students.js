"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const loadingScreen = document.getElementById("loading-screen");
  if (loadingScreen) {
    loadingScreen.remove();
  }

  const table = document.getElementById("studentsTable");
  const modal = document.getElementById("studentModal");
  const form = document.getElementById("studentForm");

  if (!table || !modal || !form) {
    return;
  }

  const title = document.getElementById("studentModalTitle");
  const errorBox = document.getElementById("studentFormError");
  const saveButton = document.getElementById("saveStudentButton");
  const addButton = document.getElementById("addStudentButton");
  const photoInput = document.getElementById("studentPhoto");
  const photoPreview = document.getElementById("studentPhotoPreview");
  const photoPreviewImage = document.getElementById("studentPhotoPreviewImage");
  const tableBody = table.querySelector("tbody");
  const csrfToken = form.elements.namedItem(form.dataset.csrfTokenName);
  let selectedPhotoUrl = null;
  let currentPhotoUrl = null;
  const fields = [
    "siswa_id",
    "nama_siswa",
    "orang_tua_id",
    "nis",
    "nisn",
    "jk",
    "tempat_lahir",
    "tanggal_lahir",
    "alamat",
    "rfid_tag",
    "kelas_id",
    "status",
  ];

  const closeModal = () => {
    modal.hidden = true;
    document.body.classList.remove("student-modal-open");
    if (selectedPhotoUrl !== null) {
      URL.revokeObjectURL(selectedPhotoUrl);
      selectedPhotoUrl = null;
    }
    currentPhotoUrl = null;
  };

  const showPhotoPreview = (url, alt = "Pratinjau foto siswa") => {
    photoPreviewImage.src = url;
    photoPreviewImage.alt = alt;
    photoPreview.hidden = false;
  };

  const openModal = (student = null) => {
    if (selectedPhotoUrl !== null) {
      URL.revokeObjectURL(selectedPhotoUrl);
      selectedPhotoUrl = null;
    }
    form.reset();
    photoPreview.hidden = true;
    photoPreviewImage.removeAttribute("src");
    currentPhotoUrl = null;
    errorBox.hidden = true;
    errorBox.textContent = "";
    form.elements.namedItem("siswa_id").value = student?.siswa_id ?? "";

    if (student) {
      title.textContent = "Edit Siswa";
      fields.slice(1).forEach((field) => {
        const input = form.elements.namedItem(field);
        if (input) {
          input.value = student[field] ?? "";
        }
      });
      const photoName = String(student.foto ?? "");
      if (photoName !== "") {
        const photoPath = /^[a-f0-9]{32}(?:-cutout\.png|\.(?:jpg|png|webp))$/i.test(photoName)
          ? form.dataset.studentPhotoUrl
          : form.dataset.studentAvatarUrl;
        currentPhotoUrl = `${photoPath}/${encodeURIComponent(photoName)}`;
        showPhotoPreview(currentPhotoUrl, `Foto ${student.nama_siswa}`);
      }
    } else {
      title.textContent = "Tambah Siswa";
    }

    modal.hidden = false;
    document.body.classList.add("student-modal-open");
    form.elements.namedItem("nama_siswa").focus();
  };

  const sendForm = async (url, values) => {
    if (!csrfToken || !csrfToken.value) {
      throw new Error("Token keamanan tidak tersedia. Muat ulang halaman lalu coba lagi.");
    }

    const response = await fetch(url, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        [form.dataset.csrfHeaderName]: csrfToken.value,
      },
      body: values instanceof FormData || values instanceof URLSearchParams
        ? values
        : new URLSearchParams(values),
    });

    if (!response.headers.get("content-type")?.includes("application/json")) {
      throw new Error(`Server mengembalikan respons tidak valid (HTTP ${response.status}).`);
    }

    const result = await response.json();

    if (!response.ok) {
      const validationErrors = result.errors
        ? Object.values(result.errors).join(" ")
        : "";
      throw new Error(validationErrors || result.message || "Permintaan gagal.");
    }

    return result;
  };

  addButton.addEventListener("click", () => openModal());

  modal.querySelectorAll("[data-close-student-modal]").forEach((button) => {
    button.addEventListener("click", closeModal);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) {
      closeModal();
    }
  });

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    errorBox.hidden = true;
    saveButton.disabled = true;

    const studentId = form.elements.namedItem("siswa_id").value;
    const url = studentId
      ? `${form.dataset.updateUrl}/${encodeURIComponent(studentId)}`
      : form.dataset.createUrl;

    try {
      await sendForm(url, new FormData(form));
      window.location.reload();
    } catch (error) {
      errorBox.textContent = error.message;
      errorBox.hidden = false;
    } finally {
      saveButton.disabled = false;
    }
  });

  photoInput.addEventListener("change", () => {
    if (selectedPhotoUrl !== null) {
      URL.revokeObjectURL(selectedPhotoUrl);
      selectedPhotoUrl = null;
    }

    const photo = photoInput.files?.[0];
    if (!photo) {
      if (currentPhotoUrl !== null) {
        showPhotoPreview(currentPhotoUrl);
      } else {
        photoPreview.hidden = true;
        photoPreviewImage.removeAttribute("src");
      }
      return;
    }

    selectedPhotoUrl = URL.createObjectURL(photo);
    showPhotoPreview(selectedPhotoUrl, "Pratinjau foto yang akan diunggah");
  });

  tableBody.addEventListener("click", async (event) => {
    const editButton = event.target.closest(".edit-student");
    const deleteButton = event.target.closest(".delete-student");
    const statusButton = event.target.closest(".toggle-student-status");

    if (editButton) {
      const row = editButton.closest("tr");
      const student = JSON.parse(editButton.dataset.student);
      student.kelas_id = row.dataset.classId || "";
      openModal(student);
      return;
    }

    if (!deleteButton && !statusButton) {
      return;
    }

    const actionButton = deleteButton ?? statusButton;
    const row = actionButton.closest("tr");
    const studentName = row.dataset.studentName || row.cells[2]?.textContent.trim() || "";
    const nextStatus = statusButton?.dataset.nextStatus;
    const action = deleteButton
      ? "menghapus"
      : nextStatus === "aktif"
        ? "mengaktifkan"
        : "menonaktifkan";

    const confirmation = deleteButton
      ? `Hapus siswa "${studentName}"? Data absensinya akan tetap tersimpan.`
      : `Anda yakin ingin ${action} siswa "${studentName}"?`;

    if (!window.confirm(confirmation)) {
      return;
    }

    actionButton.disabled = true;

    try {
      if (deleteButton) {
        await sendForm(
          `${form.dataset.deleteUrl}/${encodeURIComponent(deleteButton.dataset.id)}`,
          new URLSearchParams(),
        );
      } else {
        await sendForm(
          `${form.dataset.statusUrl}/${encodeURIComponent(statusButton.dataset.id)}`,
          { status: nextStatus },
        );
      }
      window.location.reload();
    } catch (error) {
      window.alert(error.message);
      actionButton.disabled = false;
    }
  });

  const filterRows = () => {
    const query = document.getElementById("filterStudent").value.trim().toLocaleLowerCase();
    const selectedStatuses = new Set(
      Array.from(document.querySelectorAll(".student-status-filter:checked"))
        .map((checkbox) => checkbox.value),
    );
    const searchableColumns = [2, 3, 4, 8];

    tableBody.querySelectorAll("tr[data-student-id]").forEach((row) => {
      const matchesSearch = query === "" || searchableColumns.some((column) =>
        row.cells[column]?.textContent.toLocaleLowerCase().includes(query),
      );
      row.hidden = !matchesSearch || !selectedStatuses.has(row.dataset.status);
    });
  };

  document.getElementById("filterStudent").addEventListener("input", filterRows);
  document.querySelectorAll(".student-status-filter").forEach((checkbox) => {
    checkbox.addEventListener("change", filterRows);
  });
});
