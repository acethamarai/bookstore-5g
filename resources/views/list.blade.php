@extends('app')
@section('content')
<!-- Styles -->
<style>
	html, body {
		background-color: #fff;
		color: #636b6f;
		font-family: 'Nunito', sans-serif;
		font-weight: 200;
		height: 100vh;
		margin: 0;
	}
	.book-price {
		position: absolute;
		left: 10px;
		bottom: 10px;
	}
	.book-author {
		position: absolute;
		right: 10px;
		bottom: 10px;
	}
	.book-item {
		position: relative;float:left;width:18%;margin:0 1%; margin-bottom:20px;min-width:164px;height: 410px;border:1px solid;
	}
	.book-image{width:100%;}
	.book-image a{width:100%;height:100%;display:block;}
	.book-image img{max-width:100%;}
	
	.book-title {
		    line-height: 20px;font-size:18px;text-align:center;
			padding: 5px;
    color: black;
    font-weight: 600;
	}
	.bdescription > img{width: 200px; float: left; padding: 10px;}
	.bprice{color:green;}
	.bauthor{font-style:italic;color:#aaa;}
	.ajax-loader img{width:76px;}
   
</style>
    </head>
    <body>
        <div class="row">
			<div class="books-wall">
				<!-- scroll loading -->
				<div id="books">
				<input type="hidden" name="total_count" id="total_count"
				value="" />
				</div>
				<div class="ajax-loader text-center">
					<img src="{{ asset('images') }}/loading.gif"> Loading more books...
				</div>
				<!-- scroll loading -->
			</div>
			<div id="pageContent"></div>		
			<!-- Display a single book information as POPUP -->
			<div id="small-dialog" class="mfp-hide">
				<div class="modal-content modal-info">
					<div class="modal-header">
						<h3 class="btitle"></h3>
					</div>
					<div class="modal-body modal-spa">
						<div class="bdescription"></div>
						<div class="bprice"></div>
						<div class="bauthor"></div>
					</div>
					<div class="modal-footer">
						<button>Get Now</button>
					</div>
				</div>
			</div>
			<!-- //end popup -->
        </div>
<script type="text/javascript">
$(document).ready(function(){
	// Inital Load data
	getLoadData();
	windowOnScroll();	
});
var imageUrl = "{{ asset('images/books/') }}";
function windowOnScroll() {
       $(window).on("scroll", function(e){
        if ($(window).scrollTop() == $(document).height() - $(window).height()){
            if($(".book-item").length < $("#total_count").val()) {
                var lastId = $(".book-item:last").attr("id");
                getMoreData(lastId);
            }
        }
    });
}
//Scroll loading using AJAX
function getMoreData(lastId) {
       $(window).off("scroll");
    $.ajax({
        url: "{{ url('/') }}/api/books?lastId=" + lastId,
        type: "get",
        beforeSend: function ()
        {
            $('.ajax-loader').show();
        },
        success: function (data) {
        	   setTimeout(function() {
                $('.ajax-loader').hide();
		   $('#total_count').val(data.datacounts);
			$.each(data.books, function (index, books) {
                $('#books').append('<div class="book-item" id="'+ books.id +'" ><div class="book-image"><a href="#'+ books.slug +'"><image src="'+imageUrl + '/' + books.image+'" alt ="'+books.title+'" ></a></div><div class="book-title" onclick="showBook('+ books.id +')">' + books.title + '</div><div class="book-price">$' + books.price + '</div><div class="book-author">' + books.name + '</div></div>');
            });
        	   }, 1000);
        }
   });
}
//Inital loading using AJAX
function getLoadData() {
    $.ajax({
        url: "{{ url('/') }}/api/books",
        type: "get",
        beforeSend: function ()
        {
            $('.ajax-loader').show();
        },
        success: function (data) {
        	   setTimeout(function() {
                $('.ajax-loader').hide();
			//var imageUrl = "{{ asset('images/books/') }}";
			$('#total_count').val(data.datacounts);
            $.each(data.books, function (index, books) {
                $('#books').append('<div class="book-item" id="'+ books.id +'" ><div class="book-image"><a href="#'+ books.slug +'"><image src="'+imageUrl + '/' + books.image+'" alt ="'+books.title+'" ></a></div><div class="book-title" onclick="showBook('+ books.id +')">' + books.title + '</div><div class="book-price">$' + books.price + '</div><div class="book-author">' + books.name + '</div></div>');
            });
        	}, 1000);
        }
   });
}

//Show single book information as POPUP - METHOD -1
function showBook(id) {	
	
	$.ajax({
	type: "POST",
	url: "{{ url('/') }}/api/bookid",
	data: {"id": id},
	dataType: 'json',
	success: function(response){
		$('.btitle').html(response.book['title']);
		var bimg = '<image src="'+imageUrl + '/' + response.book['image'] +'" alt ="'+response.book['title']+'" >';
		$('.bdescription').html(bimg+''+response.book['description']);
		$('.bprice').html('$'+response.book['price']);
		$('.bauthor').html('Written By '+response.book['name']);
		
		$.magnificPopup.open({
		items: { src: '#small-dialog' },
		type: 'inline'
		});
		
	} 
	});
}

//Show single book information Inline - METHOD-2
var lasturl=""; 
function checkURL(hash)
{
    if(!hash) hash = window.location.hash;    

    if(hash != lasturl) // if the hash value has changed
    {
        lasturl=hash;   //update the current hash
        loadPage(hash); // and load the new page
    }
}

function loadPage(url)  //the function that loads pages via AJAX
{
    url = url.replace('#','');   
	
    $('.ajax-loader').css('visibility','visible');  //show the rotating gif animation

    $.ajax({ 
        type: "GET",
        url: "{{ url('/') }}/api/book/"+ url,
            
        success: function(msg){
				var bimg = '<image src="'+imageUrl + '/' + msg.book['image'] +'" alt ="'+msg.book['title']+'" >';
				
				var text = '<div class="modal-content modal-info"><div class="modal-header"><h3 class="btitle">'+msg.book['title']+'</h3></div><div class="modal-body modal-spa"><div class="bdescription">'+bimg+''+msg.book['description']+'</div><div class="bprice">'+msg.book['price']+'</div><div class="bauthor">'+msg.book['name']+'</div></div><div class="modal-footer"><a onclick="window.history.back();">All Books</a><button>Get Now</button></div></div>'
                $('.books-wall').css('display','none'); 
				$('#pageContent').html(text);    //load the html into pageContet
                $('.ajax-loader').css('visibility','hidden'); 
            
        }

    });

}
$(document).ready(function(){
	$(document).on('click', '.book-image a', function(){
		//checkURL(this.hash); 
	});
	$(window).on('hashchange', function(e){
    var origEvent = e.originalEvent;
    //console.log('Going to: ' + origEvent.newURL + ' from: ' + origEvent.oldURL);
	var hashstr = window.location.hash.substr(1);
	if( hashstr == "" || hashstr == "NULL" ){ 
	$('.books-wall').css('display','block'); 
	$('#pageContent').css('display','none');
	//getLoadData(); 
	} else { 
	$('.books-wall').css('display','none');
	$('#pageContent').css('display','block');
	checkURL(hashstr); 
	}
	});
});
</script>		
@endsection
