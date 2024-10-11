

$('[data-is="convert"]').on('click', convert);
$('#form').on('submit', convert);

$('body').on('click', '[data-is="download-media"]', function () {

    event.preventDefault();

    let target = event.target;
    let k = target.dataset.k;
    let vid = target.dataset.vid;

    $('.media-section').append(`
            <span class="loading">
            <img width="25" src="./images/loader.svg" alt="loading">
            <span>Downloading ....</span>
        </span>
    `);

    $.ajax({
        type: 'POST',
        url: './backend/api.php',
        data: { k: k, vid: vid, api: 'download' },
        success: function (response) {

            $('.media-section .loading').remove();

            if (response.response != undefined && response.response.data != undefined && response.response.data.status == "ok") {
                let dlink = response.response.data.dlink;
                window.location.href = dlink;
            }
            else {
                $('#msg').html('Sorry! Could not download the media. Try again.');
                $('#msg').removeClass('hide');
                $('.media-section').addClass('hide');

                setTimeout(function () {
                    $('#msg').addClass('hide');
                }, 9000);
            }

        }
    });

});


function convert() {
    event.preventDefault();

    let url = $('[data-is="url"]').val();

    $('.media-section').removeClass('hide');

    $('.media-section').html(`
            <span class="loading">
            <img width="25" src="./images/loader.svg" alt="loading">
            <span>Converting YouTube video ....</span>
        </span>
    `);

    $.ajax({
        type: 'POST',
        url: './backend/api.php',
        data: { url: url, type: state.page, api: 'token' },
        success: function (response) {

            if (response.response !== undefined && response.response.data !== undefined && response.response.data.status == 'ok') {

                let data = response.response.data;
                let vid = data.vid;
                let links = data.links;

                let reqeuiredData = links[state.page];

                let keys = Object.keys(reqeuiredData);
                let values = Object.values(reqeuiredData);


                let optionsView = ``;

                keys.forEach(function (key) {

                    let value = reqeuiredData[key];

                    optionsView += `
                    
                        <li>
                            <span class="text">${value.f} ${value.q} ${value.size}</span>
                            <button data-is="download-media" data-vid="${vid}" data-k="${value.k}" class="btn btn-small btn-1-success focus-style-1 no-focus-style">Download</button>
                        </li>
                    
                    `;

                });


                $('.media-section').html(`
                    <h2 class="media-section-title">${data.title}</h2>
                        <div class="media-info">

                            <div class="right">
                                <ul class="media-download-list">
                                    ${optionsView}
                                </ul>
                            </div>
                        </div>

                    `);


                $('.media-download-list button:first-of-type').focus();

            }
            else {
                $('#msg').html('Sorry! Could not convert the media. Try again.');
                $('#msg').removeClass('hide');
                $('.media-section').addClass('hide');

                setTimeout(function () {
                    $('#msg').addClass('hide');
                }, 9000);

            }
        }
    });
}