jQuery(function($) {
    $('.btn-load-more').click(function(e) {
        e.preventDefault();
        var button = $(this),
            data = {
                'action': 'loadmore',
                'limit': limit,
                'page': page,
                'type': type,
                'category': category
            };

        $.ajax({
            url: loadmore_params.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function(xhr) {
                button.text('Loading...'); // change the button text, you can also add a preloader image
            },
            success: function(data) {
                if (data) {
$(".latest_posts_wrapper").append(data);
                    page++;
                    button.text('LOAD MORE');
                    if (page == max_pages_latest)
                        button.remove(); // if last page, remove the button
                } else {
                    button.remove(); // if no data, remove the button as well
                }
            }
        });
    });

});