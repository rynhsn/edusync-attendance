$(document).ready(function() {
    // Fetch and populate table data
    function fetchTableData() {
        $.ajax({
            url: window.location.href + '/show',
            type: 'GET',
            success: function(response) {
                // Populate table body with data
                var tbody = $('<tbody>');
                response.forEach(function(kelas) {
                    var tr = $('<tr>').attr('data-id', kelas.kelas_id);
                    var th = $('<th>');
                    var input = $('<input>').attr({
                        type: 'text',
                        class: 'form-control form-control-sm bg-label-primary text-center',
                        value: kelas.kelas_nama,
                        disabled: true
                    });
                    th.append(input);
                    tr.append(th);

                    var days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    days.forEach(function(day) {
                        var td = $('<td>');
                        var div = $('<div>').addClass('d-flex');
                        var inputJamMasuk = $('<input>').attr({
                            type: 'text',
                            class: 'form-control form-control-sm border-success w-50 jam-masuk me-1',
                            'data-field': day + '_jam_masuk',
                            value: kelas[day] ? kelas[day].jam_masuk : ''
                        });
                        var span = $('<span>').addClass('text-muted fw-light').text(' - ');
                        var inputJamPulang = $('<input>').attr({
                            type: 'text',
                            class: 'form-control form-control-sm border-danger w-50 jam-pulang ms-1',
                            'data-field': day + '_jam_pulang',
                            value: kelas[day] ? kelas[day].jam_pulang : ''
                        });
                        div.append(inputJamMasuk).append(span).append(inputJamPulang);
                        td.append(div);
                        tr.append(td);
                    });

                    tbody.append(tr);
                });
                $('.table').append(tbody);
            }
        });
    }

    // Call the function to fetch and populate table data
    fetchTableData();
});