<footer class="footer_area clearfix">
    <div class="footer_inner">
        <div class="container">
            <div class="row">
                <div class="col-md-8 footer_menu_"> 
                    <ul class="footer_nav">
                        <li><a href="{{route('about_us')}}">About Us</a></li>
                        <li><a href="{{route('contact_us')}}">Contact Us</a></li>
                        <li><a href="{{route('appointment')}}">Appointment</a></li>
                        <li><a href="{{route('look_book')}}">Lookbook</a></li>
                        <li><a href="{{route('show_schedule')}}">Show Schedule</a></li>
                        <li><a href="{{route('return_info')}}">Returns</a></li>
                        <!--<li><a href="{{route('shipping')}}">Shipping</a></li>-->
                        <!--<li><a href="{{route('terms_conditions')}}">Terms & Condition</a></li>-->
                    </ul>
                </div>
                <div class="col-md-4 text-right">
                    <ul class="footer_social">
                        <li><a href="{{$social_links->instagram}}" title="polagram Instagram."><i class="fab fa-instagram"></i></a></li>
                        <li><a href="{{$social_links->instagram_baevely}}" title="Baevely Instagram."><i class="fab fa-instagram"></i></a></li>
                        <li><a href="{{$social_links->facebook}}" title="Facebook."><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="{{$social_links->twitter}}" title="Twitter."><i class="fab fa-twitter"></i></a></li>
                    </ul>
                    <div class="footer_email">
                        <input type="email" name="mail_to_add" id="mail_to_add" class="form-control" placeholder="Email">
                        <input type="hidden" id="this_site_url" value="{{ route('add_email_to_mailchimp')  }}">

                        <button class="btn" id="mailchimp_add">Submit</button>
                    </div>
                    <div> 
                        <p id="mail_chimp_message" class="  alert"></p>
                    </div>
                </div>
            </div>
          <div class="row">
              <div class="col-md-12">
                 <p id="mail_chimp_message" class="alert"></p>
              </div>
          </div>
        </div>
    </div>
</footer>
<a href="javascript:" id="return-to-top"><i class="fas fa-arrow-up"></i></a>

<script>
    function goTop(){
        $('html,body').animate({ scrollTop: 0 }, 500);
    }
    setTimeout(function(){
        $(function(){
            $(window).scroll(function(e){
                if(window.scrollY > 100){
                    $('#scrollToTop').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                }
            });
        })
    }, 1000);
</script>
<script>
    $('#mail_chimp_message').hide();
    $('#mailchimp_add').click(function(e){
        e.preventDefault();
        $('#mail_chimp_message').hide();
        if(validateEmail(document.getElementById('mail_to_add').value)){
            $.ajax({
               method: "POST",
                url:"{{ route('add_email_to_mailchimp') }}",
                data: {
                    email: document.getElementById('mail_to_add').value,
                },
                headers: {
                    'X-CSRF-Token': '{!! csrf_token() !!}'
                }
            }).done(function( data ) {
                $('#mail_chimp_message').show();
                $('#mail_chimp_message').html('Successfully added to mail list.Please check your email inbox.');
                $('#mail_chimp_message').removeClass("subs_faild p-2");
                $('#mail_chimp_message').addClass('subs_success text-success');
           })
        } else {
            $('#mail_chimp_message').show();
            $('#mail_chimp_message').html('Invalid Email or Your are allready subscribed. ');
            $('#mail_chimp_message').removeClass("subs_success p-2");
           $('#mail_chimp_message').addClass('subs_faild');
        }
    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
       return re.test(String(email).toLowerCase());
    }
</script>