$('body').on('click', '.sa-warning', function() {

    var url =$(this).attr('url');


    swal({
        title: "Haqiqatdan ham arizani qabul qilishni xohlaysizmi?",
        text: "Tasdiqlash uchun barcha ma'lumotlar to'g'riligiga ishonchiz komilmi!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#297FCA",
        confirmButtonText: "Qabul qilish!",
        cancelButtonText: "Bekor qilish",
        closeOnConfirm: false
    }).then((result) => {
        window.location.href = url;

    });
});
