"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const pageSize = 10;
  const tables = document.querySelectorAll("table");

  tables.forEach((table) => {
    if (
      table.matches(".dataTable, [class*='datatables-']") ||
      table.closest(".dataTables_wrapper, .dt-container")
    ) {
      return;
    }

    const body = table.tBodies[0];
    const container = table.closest(".table-responsive") || table;
    if (!body || !container.parentElement) {
      return;
    }

    let currentPage = 1;
    const pagination = document.createElement("div");
    pagination.className =
      "d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 p-3 border-top";
    pagination.hidden = true;
    pagination.setAttribute("role", "navigation");
    pagination.setAttribute("aria-label", "Table pagination");

    const info = document.createElement("p");
    info.className = "small text-muted mb-0";

    const navigation = document.createElement("nav");
    navigation.setAttribute("aria-label", "Table pages");

    const pageList = document.createElement("ul");
    pageList.className = "pagination pagination-sm mb-0";
    navigation.append(pageList);
    pagination.append(info, navigation);
    container.insertAdjacentElement("afterend", pagination);

    const render = () => {
      const rows = Array.from(body.rows);
      const visibleRows = rows.filter((row) => !row.hidden);
      const pageCount = Math.ceil(visibleRows.length / pageSize);

      if (pageCount <= 1) {
        pagination.hidden = true;
        rows.forEach((row) => row.classList.remove("d-none"));
        currentPage = 1;
        return;
      }

      pagination.hidden = false;
      currentPage = Math.min(Math.max(currentPage, 1), pageCount);
      const start = (currentPage - 1) * pageSize;
      const end = Math.min(start + pageSize, visibleRows.length);
      info.textContent = `Showing ${start + 1} to ${end} of ${visibleRows.length} entries`;

      const pageItems = new Map();
      const addPage = (page) => pageItems.set(page, page);
      addPage(1);
      for (
        let page = Math.max(2, currentPage - 1);
        page <= Math.min(pageCount - 1, currentPage + 1);
        page++
      ) {
        addPage(page);
      }
      if (pageCount > 1) {
        addPage(pageCount);
      }

      pageList.replaceChildren();
      const addButton = (label, page, { disabled = false, active = false } = {}) => {
        const item = document.createElement("li");
        item.className = `page-item${disabled ? " disabled" : ""}${active ? " active" : ""}`;
        const button = document.createElement("button");
        button.type = "button";
        button.className = "page-link";
        button.textContent = label;
        button.disabled = disabled;
        if (active) {
          button.setAttribute("aria-current", "page");
        }
        button.addEventListener("click", () => {
          currentPage = page;
          render();
        });
        item.append(button);
        pageList.append(item);
      };

      addButton("Previous", currentPage - 1, { disabled: currentPage === 1 });
      let previousPage = 0;
      pageItems.forEach((page) => {
        if (page - previousPage > 1) {
          const ellipsis = document.createElement("li");
          ellipsis.className = "page-item disabled";
          ellipsis.innerHTML = '<span class="page-link" aria-hidden="true">&hellip;</span>';
          pageList.append(ellipsis);
        }
        addButton(String(page), page, { active: page === currentPage });
        previousPage = page;
      });
      addButton("Next", currentPage + 1, { disabled: currentPage === pageCount });

      let visibleIndex = 0;
      rows.forEach((row) => {
        const filteredOut = row.hidden;
        row.classList.toggle(
          "d-none",
          filteredOut || visibleIndex < start || visibleIndex >= end,
        );
        if (!filteredOut) {
          visibleIndex++;
        }
      });
    };

    pageList.addEventListener("click", (event) => {
      if (event.target.closest("button")) {
        event.preventDefault();
      }
    });

    new MutationObserver(() => render()).observe(body, {
      attributes: true,
      attributeFilter: ["hidden"],
      childList: true,
    });

    render();
  });
});
