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
require('../../vendor/omines/datatables-bundle/src/Resources/public/js/datatables');
const EasyMDE = require('easymde/dist/easymde.min');

$('textarea.markdown').each(el => new EasyMDE({element: el, spellChecker: false}));

// Prevent multiple form submit
$('form').submit(function (ev) {
    if ($(ev.target).data('submitting')) {
        console.warn('Prevented multiple submit!');
        return false;
    }
    $(ev.target).data('submitting', true);

    return true;
});

const refreshElementObservers = function () {
    // Confirm dialog on delete buttons
    $('.btn-delete').click(function (ev) {
        if (!confirm('Confirmer la suppression ?')) {
            ev.preventDefault();
            return false;
        }
        return true;
    });

    // Tagify
    $('input.autocomplete-tag').each(function (i, el) {
        if (!$(el).data('tagify')) {
            $(el).tagify({
                'autoComplete.rightKey': true
            })
                .on('input', function (e, input) {
                    const value = input.value;
                    const tagify = $(this).data('tagify');
                    const fetchUrl = $(this).data('fetch-url');

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
        }
    });

    // JQuery-ui Autocomplete
    $('input.jq-autocomplete').each(function (i, el) {
        const autocompleteValue = $(el).attr('autocomplete');
        $(el).autocomplete({
            source: function (request, response) {
                const fetchUrl = $(el).data('fetch-url');

                $.get(
                    fetchUrl,
                    {term: request.term},
                    function (data, textStatus, jqXHR) {
                        // Also add terms present in similar inputs on the page
                        var similarInputValues = $.map(
                                $('input.jq-autocomplete[data-fetch-url="' + fetchUrl + '"]'),
                                similarInput => similarInput === el ? '' : $(similarInput).val()
                            )
                            .filter(str => typeof str === 'string' && str.length > 0);

                        var results = similarInputValues.concat(data.map(result => result.value));

                        // Return sorted, unique values
                        response(
                            results.filter((el, i) => results.indexOf(el) === i)
                                .sort()
                        );
                    }
                );
            }
        });
        // Restore custom autocomplete after JQuery-ui overwrote it
        $(el).attr('autocomplete', autocompleteValue);
    });

    // Disable ENTER key default handling (form submission)
    $(document).on('keydown', 'form.no-submit-on-enter', function (ev) {
        if (!$(document.activeElement).hasClass('tagify__input')) {
            return ev.key != 'Enter';
        }
    });
};

const addCollectionItem = function($collectionHolder) {
    const prototype = $collectionHolder.data('prototype');
    let index = $collectionHolder.data('index') || $collectionHolder.children().length;
    const $newForm = $(prototype.replace(/__name__/g, index));

    $collectionHolder.append($newForm);

    $newForm.find('input')[0].focus();

    // Adjust page scroll to make sure the added element is visible
    $newForm[0].scrollIntoView({block: 'center'});

    $collectionHolder.data('index', index + 1);

    refreshElementObservers();
};

$('.form-group > div > button.add-collection-widget').click(function (ev) {
    const $collectionHolder = $(ev.target).siblings('[data-prototype]');

    addCollectionItem($collectionHolder);

    return false;
});
$('.form-group > div').on('click', 'button.delete-collection-widget', function (ev) {
    $(this).closest('.form-group.row').fadeOut(200, function () {
        $(this).remove();
    });

    return false;
});

// Add hotkey for "Add ingredient" action on recipes
$(document).on('keydown', 'form .form-recipe-ingredients-type, form .form-recipe-summary-type', function (ev) {
    if (ev.key == 'Enter' && ev.ctrlKey) {
        const $collectionHolder = $(ev.target).closest('[data-prototype]');

        addCollectionItem($collectionHolder);

        return false;
    }
});

$('.datatables-container').each(function (i, el) {
    let settings = $.extend(
        true,
        $(el).data('datatables-settings'),
        {options: {responsive: true}}
    );
    $(el).initDataTables(settings);
    $(el).on('draw.dt', function () {
        refreshElementObservers();
    });
});

refreshElementObservers();
