
$(document).ready(function () {

    const departmentUrl = BASE_URL + 'schemes/list';
    const schemeUrl = BASE_URL + 'schemes/list-by-id';
    const detailUrl = BASE_URL + 'schemes/details-by-id';

    // DEPARTMENT CHANGE
    $('#department_name').on('change', function () {        
        let departmentId = $(this).val();

        $('#scheme_list').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#sub_scheme_list').html('<option value="">-- उप योजना चुनें --</option>').prop('disabled', true);
        $('#viewScheme').empty();

        if (!departmentId) {
            $('#scheme_list').html('<option value="">-- योजना चुनें --</option>');
            return;
        }

        $.ajax({
            url: departmentUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                department_id: departmentId
            },

            success: function (data) {

                $('#scheme_list').html('<option value="">-- योजना चुनें --</option>');

                $.each(data, function (index, item) {

                    if (item.id != 65 && item.id != 66) {

                        $('#scheme_list').append(
                            $('<option>', {
                                value: item.id,
                                text: item.scheme_name
                            })
                        );

                    }

                });

                $('#scheme_list').prop('disabled', false);

            },

            error: function () {

                $('#scheme_list').html('<option value="">Unable to load schemes</option>');

            }
        });

    });


    // SCHEME CHANGE
    $('#scheme_list').on('change', function () {

        let schemeId = $(this).val();
        let departmentId = $('#department_name').val();

        $('#sub_scheme_list').html('<option value="">-- उप योजना चुनें --</option>').prop('disabled', true);
        $('#viewScheme').empty();

        if (!schemeId) {
            return;
        }

        $('#scheme-loader').removeClass('d-none');

        $.ajax({
            url: schemeUrl,
            type: 'POST',
            dataType: 'json',

            data: {
                scheme_id: schemeId,
                department_id: departmentId
            },

            success: function (data) {

                $('#scheme-loader').addClass('d-none');

                if (!data || data.length === 0) {
                    $('#viewScheme').text('जानकारी उपलब्ध नहीं है।');
                    return;
                }

                let hasSubScheme = false;

                $.each(data, function (index, item) {

                    if (item.sub_scheme_name) {

                        hasSubScheme = true;

                        $('#sub_scheme_list').append(
                            $('<option>', {
                                value: item.new_schemeid,
                                text: item.sub_scheme_name
                            })
                        );

                    }

                });

                if (hasSubScheme) {

                    $('#sub_scheme_list').prop('disabled', false);

                    $('#sub_scheme_list').off('change').on('change', function () {

                        let selectedId = $(this).val();

                        if (selectedId) {
                            showSchemeDetails(selectedId);
                        }

                    });

                } else {

                    showSchemeButton(data[0].new_schemeid);

                }

            },

            error: function () {

                $('#scheme-loader').addClass('d-none');
                $('#viewScheme').text('योजना की जानकारी प्राप्त करने में समस्या हुई।');

            }
        });

    });


    // SHOW SCHEME BUTTON
    function showSchemeButton(id) {

        $('#viewScheme').empty();

        let button = $('<button>', {
            type: 'button',
            class: 'btn btn-primary',
            text: 'योजना विवरण देखें'
        });

        button.on('click', function () {
            showSchemeDetails(id);
        });

        $('#viewScheme').append(button);

    }


    // SCHEME DETAILS
    function showSchemeDetails(id) {

        $('#scheme-modal-content').text('जानकारी लोड हो रही है...');

        let modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById('scheme-detail-popup')
        );

        modal.show();

        $.ajax({
            url: detailUrl,
            type: 'POST',
            dataType: 'json',

            data: {
                id: id
            },

           success: function (data) {

    let item = Array.isArray(data) ? data[0] : data;

    if (!item || !item.scheme_name) {
        $('#scheme-modal-content').text('रिकॉर्ड उपलब्ध नहीं है।');
        return;
    }

 
    let content = $('<div>');

    // First Row: Department Name, Scheme Name, Category, Type
    let row1 = $('<div>', { class: 'row' });

    // Prepare available details
    let details = [
        {
            label: 'विभाग का नाम',
            value: item.name
        },
        {
            label: 'योजना का नाम',
            value: item.scheme_name
        },
        {
            label: 'योजना की श्रेणी',
            value: item.scheme_category
        },
        {
            label: 'योजना का प्रकार',
            value: item.scheme_type
        }
    ];

    // Filter out empty values
    let availableDetails = details.filter(detail =>
        detail.value !== null &&
        detail.value !== undefined &&
        detail.value !== ''
    );

    // Calculate column width dynamically
    let colClass = availableDetails.length === 4
        ? 'col-md-3'
        : availableDetails.length === 3
            ? 'col-md-4'
            : availableDetails.length === 2
                ? 'col-md-6'
                : 'col-md-12';

    // Generate columns
    availableDetails.forEach(detail => {
        let col = $('<div>', { class: colClass });

        addDetail(col, detail.label, detail.value);

        row1.append(col);
    });

    content.append(row1);

    // REMAINING DETAILS
    if(item.if_scheme_isloan && item.if_scheme_isloan != 'N/A') {
        addDetail(content, 'लोन के उद्देश्य', item.if_scheme_isloan);
    }
    
    addDetail(content, 'पात्रता', item.applying_scheme_point);
    addDetail(content, 'योजना के लाभ', item.benefits_from_the_scheme);
    addDetail(content, 'वार्षिक वित्तीय लाभ', item.financial_benefit_scheme);

    $('#scheme-modal-content').empty().append(content);

},

            error: function () {

                $('#scheme-modal-content').text(
                    'जानकारी प्राप्त करने में समस्या हुई। कृपया पुनः प्रयास करें।'
                );

            }
        });

    }


    // SAFE DETAIL DISPLAY
   
function addDetail(container, heading, value) {

    if (!value) return;

    let section = $('<div>', {
        class: 'mb-4 scheme-detail-section'
    });

    $('<h6>').text(heading).appendTo(section);

    let content = $('<div>', {
        class: 'scheme-detail-content'
    });

    // Sanitize HTML received from database
    content.html(DOMPurify.sanitize(value));

    section.append(content);

    container.append(section);
}


});
