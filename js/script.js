$('a.delete').on('click', function (e) {
  e.preventDefault();

  if (confirm('Are you sure?')) {
    var frm = $('<form>');
    frm.attr('method', 'post');
    frm.attr('action', $(this).attr('href'));
    frm.appendTo('body');
    frm.submit();
  }
});

$('#formArticle').validate({
  rules: {
    title: {
      required: true,
    },
    content: {
      required: true,
    },
  },
});

$('button.publish').on('click', function (e) {
  var id = $(this).data('id');
  var button = $(this);

  $.ajax({
    url: '/admin/publish-article.php',
    type: 'POST',
    data: { id: id },
  })
    .done(function (data) {
      console.log(data);
      button.parent().html(data);
    })
    .fail(function (data) {
      alert('An error occurred');
    });
});

$('#formContact').validate({
  rules: {
    email: {
      email: true,
      required: true,
    },
    subject: {
      required: true,
    },
    message: {
      required: true,
    },
  },
});
