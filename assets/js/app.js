/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

const $ = require('jquery');
window.$ = window.jQuery = $;

require('@allmarkedup/purl/purl');
require('bootstrap');
require('datatables.net');
require('datatables.net-responsive-bs4');
require('jquery-ui');
require('@yaireo/tagify/dist/jQuery.tagify.min');
const SimpleMDE = require('simplemde/dist/simplemde.min');

$('.autocomplete').tagify()
    .on('input', function (e, input) {
        const value = input.value;
        const tagify = $(this).data('tagify');
        const fetchUrl = $(this).data('fetch-url');
            //console.log('Tagify input: ', input, fetchUrl);

        tagify.settings.whitelist.length = 0;
        tagify.loading(true).dropdown.hide.call(tagify);

            $.get(
                fetchUrl,
                {term: value},
                function (data, textStatus, jqXHR) {
                        tagify.settings.whitelist.splice(0, data.length, ...data);
                        tagify.loading(false).dropdown.show.call(tagify, value);
                }
            );
    });

$('textarea.markdown').each(el => new SimpleMDE({element: el, spellChecker: false}));
