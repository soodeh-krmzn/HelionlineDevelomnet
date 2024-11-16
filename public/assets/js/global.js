$(document).ready(function () {
    $(document.body).on('input', '.just-numbers', function (e) {
        var en = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        var fa = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
        var englishValue = str_replace(fa, en, $(this).val());
        var numericValue = englishValue.replace(/[^0-9.]/g, '');

        $(this).val(numericValue);
    });

    $('.just-letters').on('input', function (e) {
        var persianAndEnglishValue = $(this).val().replace(/[^a-zA-Zآ-ی ًٌٍَُِّ‌ ]/g, '');

        $(this).val(persianAndEnglishValue);
    });

    $('.nonPersianletters').on('input', function (e) {
        var nonPersianValue = $(this).val().replace(/[آ-ی ًٌٍَُِّ‌]/g, '');

        $(this).val(nonPersianValue);
    });

    $('.persianletters').on('input', function (e) {
        var persianValue = $(this).val().replace(/[^آ-ی ًٌٍَُِّ‌ ]/g, '');

        $(this).val(persianValue);
    });

    $(document.body).on("click", "#toggle-search", function() {
        $(".select2").select2();
    });

    $(document).on("keypress", function (e) {
        if (e.which == 13) {
            $(".submit-by-enter").trigger("click");
        }
    });

});

function checkEmpty(formId = "") {
    var errors = 0;

    if (formId != "") {
        var element = `#${formId} .checkEmpty`;
    } else {
        var element = '.checkEmpty';
    }

    $(element).each(function () {
        var id = $(this).data('id');
        if ($(this).val() == "") {
            $(this).addClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkEmpty]`).html(window.emptyError);
            errors++;
        } else {
            $(this).removeClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkEmpty]`).html('');
        }
    });

    if (errors > 0) {
        return true;
    } else {
        return false;
    }
}

function checkMobile(formId = "") {
    return false;
    var errors = 0;
    var pattern = /(09)[0-9]{9}/;

    if (formId != "") {
        var element = `#${formId} .checkMobile`;
    } else {
        var element = '.checkMobile';
    }

    $(element).each(function () {
        var id = $(this).data('id');
        if (!pattern.test($(this).val()) && $(this).val() != "") {
            $(this).addClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkMobile]`).html('الگوی شماره موبایل صحیح نمیباشد.');
            errors++;
        } else {
            $(this).removeClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkMobile]`).html('');
        }
    });

    if (errors > 0) {
        return true;
    } else {
        return false;
    }
}

function checkIp(formId = "") {
    var errors = 0;
    var pattern = /^((25[0-5]|(2[0-4]|1\d|[1-9]|)\d)\.?\b){4}$/;

    if (formId != "") {
        var element = `#${formId} .checkIp`;
    } else {
        var element = '.checkIp';
    }

    $(element).each(function () {
        var id = $(this).data('id');
        if (!pattern.test($(this).val()) && $(this).val() != "") {
            $(this).addClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkIp]`).html('الگوی آی پی صحیح نمیباشد.');
            errors++;
        } else {
            $(this).removeClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkIp]`).html('');
        }
    });

    if (errors > 0) {
        return true;
    } else {
        return false;
    }
}

function checkNationalCode(formId = "") {
    return false;
    var errors = 0;
    var pattern = /[0-9]{10}/;

    if (formId != "") {
        var element = `#${formId} .checkNationalCode`;
    } else {
        var element = '.checkNationalCode';
    }

    $(element).each(function () {
        var id = $(this).data('id');
        if (!pattern.test($(this).val()) && $(this).val() != "") {
            $(this).addClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkNationalCode]`).html('الگوی کد ملی صحیح نمیباشد.');
            errors++;
        } else {
            $(this).removeClass('is-invalid');
            $(`.invalid-feedback[data-id=${id}][data-error=checkNationalCode]`).html('');
        }
    });

    if (errors > 0) {
        return true;
    } else {
        return false;
    }
}

function checkDate(formId = "") {
    var errors = 0;
    var pattern = /^[1-4]\d{3}\/((0[1-6]\/((3[0-1])|([1-2][0-9])|(0[1-9])))|((1[0-2]|(0[7-9]))\/(30|31|([1-2][0-9])|(0[1-9]))))$/;
    // $|^([1۱][۰-۹ 0-9]{3}[/\/]([0 ۰][۱-۶ 1-6])[/\/]([0 ۰][۱-۹ 1-9]|[۱۲12][۰-۹ 0-9]|[3۳][01۰۱])|[1۱][۰-۹ 0-9]{3}[/\/]([۰0][۷-۹ 7-9]|[1۱][۰۱۲012])[/\/]([۰0][1-9 ۱-۹]|[12۱۲][0-9 ۰-۹]|(30|۳۰)))$

    if (formId != "") {
        var element = `#${formId} .checkDate`;
    } else {
        var element = '.checkDate';
    }

    $(element).each(function () {
        if ($(this).val() != "") {
            var id = $(this).data('id');
            if (!pattern.test($(this).val())) {
                $(this).addClass('is-invalid');
                $(`.invalid-feedback[data-id=${id}][data-error=checkDate]`).html('تاریخ وارد شده معتبر نمیباشد. (نمونه معتبر 1401/01/01)');
                errors++;
            } else {
                $(this).removeClass('is-invalid');
                $(`.invalid-feedback[data-id=${id}][data-error=checkDate]`).html('');
            }
        }
    });

    if (errors > 0) {
        return true;
    } else {
        return false;
    }
}

function addCommas(nStr) {
    nStr += '';
    let x = nStr.split('.');
    let x1 = x[0];
    let x2 = x.length > 1 ? '.' + x[1] : '';
    let rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


function totalPayment() {
    var sum = 0;
    $('.payment-price').each(function () {
        sum += Number($(this).val());
    });

    var number_format = addCommas(sum);

    $("#total-pay").html(number_format);
}

function dateMask() {
    /*$(".date-mask").each(function () {
        new Cleave(this, {
            date: true,
            delimiter: '/',
            datePattern: ['Y', 'm', 'd']
        });
    })
    */
    // $(".date-mask").persianDatepicker({
    //     initialValue: false,
    //     observer: true,
    //     format: 'YYYY/MM/DD',
    // });
}

function dateTimeMask() {
    return '';
    $(".datetime-mask").each(function () {
        new Cleave(this, {
            delimiters: ['/', '/', ' ', ':'],
            blocks: [4, 2, 2, 2, 2]
        });
    })
}

function moneyFilter() {
    $(document.body).on("keyup", ".money-filter", function () {
        var id = $(this).data('id');
        var value = $(this).val().replace(/[^0-9۰-۹.]/g, '');
        var en = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        var fa = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
        var englishValue = str_replace(fa, en, value);
        $(`#${id}`).val(englishValue);
        $(this).val(addCommas(englishValue));
    });
    $(document.body).on("change", ".money-filter", function () {
        var id = $(this).data('id');
        $(`#${id}`).trigger('change');
    });
}

function str_replace(search, replace, subject, countObj) {
    let i = 0
    let j = 0
    let temp = ''
    let repl = ''
    let sl = 0
    let fl = 0
    const f = [].concat(search)
    let r = [].concat(replace)
    let s = subject
    let ra = Object.prototype.toString.call(r) === '[object Array]'
    const sa = Object.prototype.toString.call(s) === '[object Array]'
    s = [].concat(s)
    const $global = (typeof window !== 'undefined' ? window : global)
    $global.$locutus = $global.$locutus || {}
    const $locutus = $global.$locutus
    $locutus.php = $locutus.php || {}
    if (typeof (search) === 'object' && typeof (replace) === 'string') {
        temp = replace
        replace = []
        for (i = 0; i < search.length; i += 1) {
            replace[i] = temp
        }
        temp = ''
        r = [].concat(replace)
        ra = Object.prototype.toString.call(r) === '[object Array]'
    }
    if (typeof countObj !== 'undefined') {
        countObj.value = 0
    }
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            if (f[j] === '') {
                continue
            }
            temp = s[i] + ''
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0]
            s[i] = (temp).split(f[j]).join(repl)
            if (typeof countObj !== 'undefined') {
                countObj.value += ((temp.split(f[j])).length - 1)
            }
        }
    }
    return sa ? s : s[0]
}
