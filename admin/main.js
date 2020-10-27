var currentDate = new Date();
var startDate = new Date(new Date().setDate(currentDate.getDate() - 1));
var endDate = new Date(new Date().setFullYear(currentDate.getFullYear() + 1));
$.ajaxSetup({
    cache: false
});

$('button#add-shortlink').click(function (event) {
    event.preventDefault();
    $('div#spinner').show();
    var validation = true;

    if ($('input#name').val() == '' || $('input#link').val() == '') {
        validation = false;
        alert('O campo Nome e Link devem ser preenchidos');
    }

    if (validation) {
        var params = {
            acao: $('input#acao').val(),
            username: $('input#username').val(),
            name: $('input#name').val(),
            link: $('input#link').val()
        }

        $.ajax({
            type: 'post',
            data: params,
            url: 'salvar_link.php',
            dataType: 'json',
            success: function (result) {
                if (result.erro) {
                    alert(result.mensagem);
                }
                window.location.href = './index.php';
            }
        });
    }
    getCharts();
});

$('button#search-shortlink').click(function (event) {
    event.preventDefault();
    $('div#spinner').show();
    var params = {
        acao: $('input#acao').val(),
        name: $('input#name').val(),
        link: $('input#link').val()
    }

    $.ajax({
        type: 'post',
        data: params,
        url: 'salvar_link.php',
        dataType: 'json',
        success: function (result) {
            if (result.erro) {
                alert(result.mensagem);
            } else {
                response = result.resultado;
                $("#result_search > tbody").html('');

                $.each(response, function (i, item) {
                    var del = '<a class=\"nav-link\" onclick=\"javascript: return confirm(\'Deseja excluir o link?\');\" href=\"./links.php?excluir=' + item.id_url + '\"><i class=\"material-icons\">delete_forever</i></a>';
                    var link = '<a class=\"nav-link\" target=\"_blank\" href=\"../redirect.php?short=' + item.name + '\">' + item.name + '</a>';

                    $("#result_search > tbody").append($('<tr>').append(
                        $('<td>').html(link),
                        $('<td>').text(item.url),
                        $('<td>').text(item.username),
                        $('<td>').html(del)
                    ));
                });
            }
        }
    });
});

$('a#refresh').click(function (event) {
    event.preventDefault();
    getCharts();
});

function getCharts() {
    'use strict';
    $('div#spinner').show();
    $('div#charts').empty();
    $.getJSON('stats.json', function (json) {
        $.each(json, function (name, data) {
            $('div#charts').append('<div id="card-' + name + '" class="card mb-3"><div class="card-body"><div id="heatmap-' + name + '" class="heatmap"></div></div><div class="card-footer text-center text-muted"><a id="export-' + name + '" href="#download" class="card-link">Download chart</a><a id="delete-' + name + '" href="#delete" class="card-link">Delete shortlink and dataset</a></div></div>');
            let heatmap = new frappe.Chart('div#heatmap-' + name, {
                type: 'heatmap',
                title: 'Estatísticas de acesso para ' + name,
                data: {
                    dataPoints: data,
                    start: startDate,
                    end: endDate
                },
                radius: 2,
                countLabel: 'Acessos'
            });
            $('a#export-' + name).click(function (event) {
                event.preventDefault();
                heatmap.export();
            });
            $('a#delete-' + name).click(function (event) {
                event.preventDefault();
                $.post('index.php?delete', {
                    name: name
                });
                $('div#card-' + name).remove();
            });
        });
        $('div#spinner').hide();
    });
}

getCharts();
