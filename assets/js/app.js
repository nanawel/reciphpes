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
require('jquery-ui/ui/widgets/autocomplete');
require('@yaireo/tagify/dist/jQuery.tagify.min');
const SimpleMDE = require('simplemde/dist/simplemde.min');

$('.autocomplete-tag').tagify()
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


const refreshPluginObservers = function () {
    // JQuery-ui Autocomplete
    $('.jq-autocomplete').each(function (i, el) {
        $(el).autocomplete({
            source: function (request, response) {
                console.log(request);

                response(['a', 'b']);
            }
        });
        // Restore custom autocomplete after JQuery-ui overwrote it
        $(el).attr('autocomplete', $(el).data('autocomplete-bak'));
    });
};


$('.form-group > div > button.add-collection-widget').click(function (ev) {
    const $collectionHolder = $(ev.target).siblings('[data-prototype]');
    const prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index') || $collectionHolder.children().length;
    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);

    $collectionHolder.append(newForm);

    $collectionHolder.find('button.delete-collection-widget').click(function (ev) {
        $(this).parents('.form-row').remove();

        return false;
    });

    refreshPluginObservers();

    return false;
});

refreshPluginObservers();
